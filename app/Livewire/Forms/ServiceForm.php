<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Service;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * The service form: shared fields plus one tab per landing language. A tab is only saved when
 * it has content, and English is required because it is the fallback for the other languages.
 *
 * @phpstan-import-type TranslationInput from \App\Actions\Services\SaveService
 */
class ServiceForm extends Form
{
    private const TEXT_FIELDS = ['title', 'summary', 'description', 'included', 'fit'];

    public ?Service $service = null;

    public string $name = '';

    public bool $isActive = true;

    public ?TemporaryUploadedFile $image = null;

    /** @var array<string, array<string, string>> */
    public array $translations = [];

    public function initialize(?Service $service): void
    {
        $this->service = $service;
        $this->name = $service->name ?? '';
        $this->isActive = $service->is_active ?? true;
        $this->translations = [];

        foreach (config()->array('locales.landing') as $locale) {
            $translation = $service?->translations->firstWhere('locale', $locale);

            $this->translations[$locale] = [
                'title' => $translation->title ?? '',
                'summary' => $translation->summary ?? '',
                'description' => $translation->description ?? '',
                'included' => implode("\n", $translation->included ?? []),
                'fit' => $translation->fit ?? '',
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
        $default = config()->string('locales.default');

        $rules = [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('services', 'name')->ignore($this->service?->id),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $slug = $this->slug();

                    if ($slug === '' || $this->slugTaken($slug)) {
                        $fail(__('services.messages.name_unusable'));
                    }
                },
            ],
            'isActive' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=800,min_height=600'],
            'translations' => [function (string $attribute, mixed $value, Closure $fail) use ($default): void {
                if (! in_array($default, $this->activeLocales(), true)) {
                    $fail(__('services.messages.default_language_required', ['language' => config()->string("locales.locales.{$default}.name")]));
                }
            }],
        ];

        foreach ($this->activeLocales() as $locale) {
            $prefix = "translations.{$locale}";

            $rules["{$prefix}.title"] = ['required', 'string', 'max:255'];
            $rules["{$prefix}.summary"] = ['nullable', 'string', 'max:500'];
            $rules["{$prefix}.description"] = ['nullable', 'string', 'max:5000'];
            $rules["{$prefix}.included"] = ['nullable', 'string', 'max:3000', function (string $attribute, mixed $value, Closure $fail) use ($locale): void {
                foreach ($this->includedItems($locale) as $item) {
                    if (mb_strlen($item) > 255) {
                        $fail(__('services.messages.included_item_too_long'));

                        return;
                    }
                }
            }];
            $rules["{$prefix}.fit"] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        $attributes = [
            'name' => __('services.fields.name'),
            'isActive' => __('services.fields.active'),
            'image' => __('services.fields.image'),
            'translations' => __('services.fields.languages'),
        ];

        foreach (array_keys($this->translations) as $locale) {
            $language = config()->string("locales.locales.{$locale}.name");

            foreach (self::TEXT_FIELDS as $field) {
                $attributes["translations.{$locale}.{$field}"] = __("services.fields.{$field}").' ('.$language.')';
            }
        }

        return $attributes;
    }

    public function normalize(): void
    {
        $this->name = trim($this->name);

        foreach ($this->translations as $locale => $fields) {
            foreach (self::TEXT_FIELDS as $field) {
                $this->translations[$locale][$field] = trim($fields[$field]);
            }
        }
    }

    /**
     * The address comes from the name when the service is created. An existing address is kept so
     * links already shared keep working, unless it is still empty (services from before slugs).
     */
    public function slug(): string
    {
        $current = $this->service?->slug;

        return $current !== null && $current !== '' ? $current : Str::slug($this->name);
    }

    private function slugTaken(string $slug): bool
    {
        return Service::query()
            ->where('slug', $slug)
            ->when($this->service !== null, fn ($query) => $query->whereKeyNot($this->service?->id))
            ->exists();
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
                'title' => $fields['title'],
                'summary' => $fields['summary'],
                'description' => $fields['description'],
                'included' => $this->includedItems($locale),
                'fit' => $fields['fit'],
            ];
        }

        return $payload;
    }

    /**
     * The "included" textarea holds one item per line.
     *
     * @return list<string>
     */
    private function includedItems(string $locale): array
    {
        $lines = preg_split('/\R/u', $this->translations[$locale]['included']) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn (string $line): bool => $line !== ''));
    }

    private function isFilled(string $locale): bool
    {
        foreach (self::TEXT_FIELDS as $field) {
            if (trim($this->translations[$locale][$field]) !== '') {
                return true;
            }
        }

        return false;
    }
}
