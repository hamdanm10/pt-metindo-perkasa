<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\ProcessCategory;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

/**
 * The process category form: the name, the on or off switch, and one description per landing language.
 * A language is only saved when it has text, and English is required because it is the fallback for the others.
 */
class ProcessCategoryForm extends Form
{
    public ?ProcessCategory $processCategory = null;

    public string $name = '';

    public bool $isActive = true;

    /** @var array<string, string> */
    public array $descriptions = [];

    public function initialize(?ProcessCategory $processCategory): void
    {
        $this->processCategory = $processCategory;
        $this->name = $processCategory->name ?? '';
        $this->isActive = $processCategory->is_active ?? true;
        $this->descriptions = [];

        foreach (config()->array('locales.landing') as $locale) {
            $this->descriptions[$locale] = $processCategory?->translations->firstWhere('locale', $locale)->description ?? '';
        }
    }

    /**
     * Languages the admin has written, in landing order.
     *
     * @return list<string>
     */
    public function activeLocales(): array
    {
        return array_values(array_filter(
            array_keys($this->descriptions),
            fn (string $locale): bool => trim($this->descriptions[$locale]) !== '',
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
                Rule::unique('process_categories', 'name')->ignore($this->processCategory?->id),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $slug = $this->slug();

                    if ($slug === '' || $this->slugTaken($slug)) {
                        $fail(__('process_categories.messages.name_unusable'));
                    }
                },
            ],
            'isActive' => ['boolean'],
            'descriptions' => [function (string $attribute, mixed $value, Closure $fail) use ($default): void {
                if (! in_array($default, $this->activeLocales(), true)) {
                    $fail(__('process_categories.messages.default_language_required', ['language' => config()->string("locales.locales.{$default}.name")]));
                }
            }],
        ];

        foreach ($this->activeLocales() as $locale) {
            $rules["descriptions.{$locale}"] = ['string', 'max:5000'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        $attributes = [
            'name' => __('process_categories.fields.name'),
            'isActive' => __('process_categories.fields.active'),
            'descriptions' => __('process_categories.fields.languages'),
        ];

        foreach (array_keys($this->descriptions) as $locale) {
            $attributes["descriptions.{$locale}"] = __('process_categories.fields.description').' ('.config()->string("locales.locales.{$locale}.name").')';
        }

        return $attributes;
    }

    public function normalize(): void
    {
        $this->name = trim($this->name);

        foreach ($this->descriptions as $locale => $text) {
            $this->descriptions[$locale] = trim($text);
        }
    }

    /**
     * The address comes from the name when the category is created. An existing address is kept so
     * links already shared keep working.
     */
    public function slug(): string
    {
        $current = $this->processCategory?->slug;

        return $current !== null && $current !== '' ? $current : Str::slug($this->name);
    }

    private function slugTaken(string $slug): bool
    {
        return ProcessCategory::query()
            ->where('slug', $slug)
            ->when($this->processCategory !== null, fn ($query) => $query->whereKeyNot($this->processCategory?->id))
            ->exists();
    }

    /**
     * @return array<string, string>
     */
    public function payload(): array
    {
        $descriptions = [];

        foreach ($this->activeLocales() as $locale) {
            $descriptions[$locale] = $this->descriptions[$locale];
        }

        return $descriptions;
    }
}
