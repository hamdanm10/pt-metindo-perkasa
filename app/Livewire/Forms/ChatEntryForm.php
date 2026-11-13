<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\ChatEntry;
use Livewire\Form;

/**
 * The chat question form: one block per landing language. A language is saved only when it has
 * content, and a language with content needs a question and an answer. At least one language is required.
 *
 * @phpstan-import-type TranslationInput from \App\Actions\Chat\SaveChatEntry
 */
class ChatEntryForm extends Form
{
    /** @var array<string, array{question: string, answer: string, link_label: string, link_url: string}> */
    public array $translations = [];

    public function initialize(?ChatEntry $entry): void
    {
        $this->translations = [];

        foreach (config()->array('locales.landing') as $locale) {
            $translation = $entry?->translations->firstWhere('locale', $locale);

            $this->translations[(string) $locale] = [
                'question' => $translation->question ?? '',
                'answer' => $translation->answer ?? '',
                'link_label' => $translation->link_label ?? '',
                'link_url' => $translation->link_url ?? '',
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
        $rules = [
            'translations' => [function (string $attribute, mixed $value, \Closure $fail): void {
                if ($this->activeLocales() === []) {
                    $fail(__('chat_manager.messages.language_required'));
                }
            }],
        ];

        foreach ($this->activeLocales() as $locale) {
            $rules["translations.{$locale}.question"] = ['required', 'string', 'max:200'];
            $rules["translations.{$locale}.answer"] = ['required', 'string', 'max:2000'];
            $rules["translations.{$locale}.link_label"] = ['nullable', 'string', 'max:100', "required_with:translations.{$locale}.link_url"];
            $rules["translations.{$locale}.link_url"] = ['nullable', 'string', 'max:500', "required_with:translations.{$locale}.link_label", function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_string($value) && $value !== '' && ! self::isSafeUrl($value)) {
                    $fail(__('chat_manager.messages.bad_link'));
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
        $attributes = ['translations' => __('chat_manager.fields.languages')];

        foreach (array_keys($this->translations) as $locale) {
            $name = (string) config("locales.locales.{$locale}.name");

            $attributes["translations.{$locale}.question"] = __('chat_manager.fields.question').' ('.$name.')';
            $attributes["translations.{$locale}.answer"] = __('chat_manager.fields.answer').' ('.$name.')';
            $attributes["translations.{$locale}.link_label"] = __('chat_manager.fields.link_label').' ('.$name.')';
            $attributes["translations.{$locale}.link_url"] = __('chat_manager.fields.link_url').' ('.$name.')';
        }

        return $attributes;
    }

    public function normalize(): void
    {
        foreach ($this->translations as $locale => $fields) {
            foreach ($fields as $key => $value) {
                $this->translations[$locale][$key] = trim($value);
            }
        }
    }

    /**
     * What to save: only the languages with content, empty link fields as null.
     *
     * @return array<string, TranslationInput>
     */
    public function payload(): array
    {
        $payload = [];

        foreach ($this->activeLocales() as $locale) {
            $fields = $this->translations[$locale];

            $payload[$locale] = [
                'question' => $fields['question'],
                'answer' => $fields['answer'],
                'link_label' => $fields['link_label'] === '' ? null : $fields['link_label'],
                'link_url' => $fields['link_url'] === '' ? null : $fields['link_url'],
            ];
        }

        return $payload;
    }

    /**
     * A page of this site (starts with a single slash) or a web address (http or https). Anything else,
     * such as `javascript:`, is refused.
     */
    public static function isSafeUrl(string $url): bool
    {
        if (str_starts_with($url, '/')) {
            return ! str_starts_with($url, '//') && ! str_contains($url, '\\');
        }

        return preg_match('#^https?://[^\s/]+#i', $url) === 1 && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    private function isFilled(string $locale): bool
    {
        foreach ($this->translations[$locale] ?? [] as $value) {
            if (trim($value) !== '') {
                return true;
            }
        }

        return false;
    }
}
