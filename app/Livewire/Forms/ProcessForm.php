<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\StepAnimation;
use App\Models\Process;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

/**
 * The process form: name, category, the on or off switch, one description per landing language, and the
 * steps of the flow. Each step has a name, a description per language, and the products used in it.
 * A language of the process is only saved when it has text, and English is required because it is the
 * fallback for the others.
 *
 * @phpstan-import-type StepInput from \App\Actions\Processes\SaveProcess
 */
class ProcessForm extends Form
{
    public const MAX_STEPS = 30;

    public const MAX_PRODUCTS = 30;

    public ?Process $process = null;

    public string $name = '';

    public string $processCategoryId = '';

    public bool $isActive = true;

    /** @var array<string, string> */
    public array $descriptions = [];

    /** @var array<int, array{name: string, descriptions: array<string, string>, products: array<int, int>, animation: string}> */
    public array $steps = [];

    public function initialize(?Process $process): void
    {
        $this->process = $process;
        $this->name = $process->name ?? '';
        $this->processCategoryId = $process === null ? '' : (string) $process->process_category_id;
        $this->isActive = $process->is_active ?? true;
        $this->descriptions = [];

        foreach (config()->array('locales.landing') as $locale) {
            $this->descriptions[$locale] = $process?->translations->firstWhere('locale', $locale)->description ?? '';
        }

        $this->steps = [];

        foreach ($process === null ? [] : $process->steps as $step) {
            $texts = [];

            foreach (config()->array('locales.landing') as $locale) {
                $texts[$locale] = $step->translations->firstWhere('locale', $locale)->description ?? '';
            }

            $this->steps[] = [
                'name' => $step->name,
                'descriptions' => $texts,
                'animation' => $step->animation === null ? '' : $step->animation->value,
                'products' => $step->products->pluck('id')->map(fn ($id): int => (int) $id)->values()->all(),
            ];
        }
    }

    /**
     * An empty step, with a blank description for every landing language.
     *
     * @return array{name: string, descriptions: array<string, string>, products: array<int, int>, animation: string}
     */
    public function blankStep(): array
    {
        return [
            'name' => '',
            'descriptions' => array_fill_keys(config()->array('locales.landing'), ''),
            'animation' => '',
            'products' => [],
        ];
    }

    /**
     * Languages the admin has written for the process, in landing order.
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
                Rule::unique('processes', 'name')->ignore($this->process?->id),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $slug = $this->slug();

                    if ($slug === '' || $this->slugTaken($slug)) {
                        $fail(__('processes.messages.name_unusable'));
                    }
                },
            ],
            'processCategoryId' => ['required', 'integer', Rule::exists('process_categories', 'id')],
            'isActive' => ['boolean'],
            'descriptions' => [function (string $attribute, mixed $value, Closure $fail) use ($default): void {
                if (! in_array($default, $this->activeLocales(), true)) {
                    $fail(__('processes.messages.default_language_required', ['language' => config()->string("locales.locales.{$default}.name")]));
                }
            }],
            'steps' => ['array', 'max:'.self::MAX_STEPS],
            'steps.*.name' => ['required', 'string', 'max:255'],
            'steps.*.animation' => ['nullable', Rule::enum(StepAnimation::class)],
            'steps.*.descriptions.*' => ['nullable', 'string', 'max:1000'],
            'steps.*.products' => ['array', 'max:'.self::MAX_PRODUCTS],
            'steps.*.products.*' => ['integer', 'distinct', Rule::exists('products', 'id')],
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
            'name' => __('processes.fields.name'),
            'processCategoryId' => __('processes.fields.category'),
            'isActive' => __('processes.fields.active'),
            'descriptions' => __('processes.fields.languages'),
            'steps' => __('processes.fields.steps'),
            'steps.*.name' => __('processes.fields.step_name'),
            'steps.*.animation' => __('processes.fields.step_animation'),
            'steps.*.descriptions.*' => __('processes.fields.step_description'),
            'steps.*.products' => __('processes.fields.step_products'),
            'steps.*.products.*' => __('processes.fields.step_products'),
        ];

        foreach (array_keys($this->descriptions) as $locale) {
            $attributes["descriptions.{$locale}"] = __('processes.fields.description').' ('.config()->string("locales.locales.{$locale}.name").')';
        }

        return $attributes;
    }

    public function normalize(): void
    {
        $this->name = trim($this->name);
        $this->processCategoryId = trim($this->processCategoryId);

        foreach ($this->descriptions as $locale => $text) {
            $this->descriptions[$locale] = trim($text);
        }

        foreach ($this->steps as $index => $step) {
            $this->steps[$index]['name'] = trim($step['name']);

            foreach ($step['descriptions'] as $locale => $text) {
                $this->steps[$index]['descriptions'][$locale] = trim($text);
            }
        }
    }

    /**
     * The address comes from the name when the process is created. An existing address is kept so
     * links already shared keep working.
     */
    public function slug(): string
    {
        $current = $this->process?->slug;

        return $current !== null && $current !== '' ? $current : Str::slug($this->name);
    }

    private function slugTaken(string $slug): bool
    {
        return Process::query()
            ->where('slug', $slug)
            ->when($this->process !== null, fn ($query) => $query->whereKeyNot($this->process?->id))
            ->exists();
    }

    /**
     * @return array<string, string>
     */
    public function descriptionsPayload(): array
    {
        $descriptions = [];

        foreach ($this->activeLocales() as $locale) {
            $descriptions[$locale] = $this->descriptions[$locale];
        }

        return $descriptions;
    }

    /**
     * @return list<StepInput>
     */
    public function stepsPayload(): array
    {
        return array_values(array_map(fn (array $step): array => [
            'name' => $step['name'],
            'descriptions' => $step['descriptions'],
            'animation' => $step['animation'] === '' ? null : $step['animation'],
            'products' => array_values($step['products']),
        ], $this->steps));
    }
}
