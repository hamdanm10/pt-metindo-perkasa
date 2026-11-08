<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\ArticleType;
use App\Enums\PublicationStatus;
use App\Models\Article;
use App\Services\Articles\ArticleBody;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * The article form: shared fields plus one tab per landing language. A tab is only
 * saved when it has content, and a published tab has to be complete.
 *
 * @phpstan-import-type TranslationInput from \App\Actions\Articles\SaveArticle
 */
class ArticleForm extends Form
{
    public ?Article $article = null;

    public string $type = 'article';

    public ?TemporaryUploadedFile $thumbnail = null;

    /** @var array<string, array<string, mixed>> */
    public array $translations = [];

    public function initialize(?Article $article): void
    {
        $this->article = $article;
        $this->type = $article?->type->value ?? ArticleType::Article->value;
        $this->translations = [];

        foreach (config()->array('locales.landing') as $locale) {
            $translation = $article?->translations->firstWhere('locale', $locale);

            $this->translations[$locale] = [
                'title' => $translation->title ?? '',
                'slug' => $translation->slug ?? '',
                'excerpt' => $translation->excerpt ?? '',
                'body' => $translation?->body === null ? '' : (string) json_encode($translation->body),
                'meta_title' => $translation->meta_title ?? '',
                'meta_description' => $translation->meta_description ?? '',
                'image_alt' => $translation->image_alt ?? '',
                'published' => $translation?->status === PublicationStatus::Published,
            ];
        }
    }

    /**
     * Languages the admin has filled in, in landing order.
     *
     * @return list<string>
     */
    public function activeLocales(): array
    {
        return array_values(array_filter(
            array_keys($this->translations),
            fn (string $locale): bool => $this->isFilled($locale),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $active = $this->activeLocales();
        $anyPublished = array_filter($active, fn (string $locale): bool => (bool) $this->translations[$locale]['published']) !== [];

        $rules = [
            'type' => ['required', Rule::enum(ArticleType::class)],
            'thumbnail' => [
                Rule::requiredIf($anyPublished && $this->article?->thumbnail_path === null),
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048',
                'dimensions:min_width=1200,min_height=675',
            ],
            'translations' => [function (string $attribute, mixed $value, \Closure $fail) use ($active): void {
                if ($active === []) {
                    $fail(__('article_manager.messages.no_language'));
                }
            }],
        ];

        foreach ($active as $locale) {
            $published = (bool) $this->translations[$locale]['published'];
            $own = $this->article?->translations->firstWhere('locale', $locale);
            $prefix = "translations.{$locale}";

            $rules["{$prefix}.title"] = ['required', 'string', 'max:255'];
            $rules["{$prefix}.slug"] = [
                'required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('article_translations', 'slug')->where('locale', $locale)->ignore($own?->id),
            ];
            $rules["{$prefix}.excerpt"] = ['nullable', 'string', 'max:500'];
            $rules["{$prefix}.meta_title"] = ['nullable', 'string', 'max:255'];
            $rules["{$prefix}.meta_description"] = [$published ? 'required' : 'nullable', 'string', 'max:320'];
            $rules["{$prefix}.image_alt"] = ['nullable', 'string', 'max:255'];
            $rules["{$prefix}.body"] = [$published ? 'required' : 'nullable', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_string($value) && $value !== '' && ! $this->bodyIsValid($value)) {
                    $fail(__('article_manager.messages.body_invalid'));
                }
            }];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        $attributes = [
            'type' => __('article_manager.fields.type'),
            'thumbnail' => __('article_manager.fields.thumbnail'),
            'translations' => __('article_manager.fields.translations'),
        ];

        foreach (array_keys($this->translations) as $locale) {
            $language = config()->string("locales.locales.{$locale}.name");

            foreach (['title', 'slug', 'excerpt', 'body', 'meta_title', 'meta_description', 'image_alt'] as $field) {
                $attributes["translations.{$locale}.{$field}"] = __("article_manager.fields.{$field}").' ('.$language.')';
            }
        }

        return $attributes;
    }

    public function normalize(): void
    {
        foreach ($this->translations as $locale => $fields) {
            foreach (['title', 'slug', 'excerpt', 'meta_title', 'meta_description', 'image_alt'] as $field) {
                $this->translations[$locale][$field] = trim((string) $fields[$field]);
            }

            $this->translations[$locale]['slug'] = mb_strtolower((string) $this->translations[$locale]['slug']);
        }
    }

    /**
     * @return array<string, TranslationInput>
     */
    public function payload(): array
    {
        $payload = [];

        foreach ($this->activeLocales() as $locale) {
            $fields = $this->translations[$locale];

            $payload[$locale] = [
                'title' => (string) $fields['title'],
                'slug' => (string) $fields['slug'],
                'excerpt' => (string) $fields['excerpt'],
                'body' => (string) $fields['body'] !== '' ? (string) $fields['body'] : '{"type":"doc","content":[]}',
                'meta_title' => (string) $fields['meta_title'],
                'meta_description' => (string) $fields['meta_description'],
                'image_alt' => (string) $fields['image_alt'],
                'published' => (bool) $fields['published'],
            ];
        }

        return $payload;
    }

    private function isFilled(string $locale): bool
    {
        $fields = $this->translations[$locale];

        foreach (['title', 'slug', 'excerpt', 'meta_title', 'meta_description', 'image_alt'] as $field) {
            if (trim((string) $fields[$field]) !== '') {
                return true;
            }
        }

        return (bool) $fields['published'] || $this->hasBody((string) $fields['body']);
    }

    private function hasBody(string $json): bool
    {
        return $json !== '' && $this->bodyIsValid($json, requireText: true);
    }

    private function bodyIsValid(string $json, bool $requireText = false): bool
    {
        $body = app(ArticleBody::class);

        try {
            $document = $body->parse($json);
        } catch (InvalidArgumentException) {
            return false;
        }

        return ! $requireText || ! $body->isEmpty($document);
    }
}
