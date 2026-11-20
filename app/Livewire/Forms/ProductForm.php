<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Product;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * The product form: shared fields, the shop links, and one description per landing language. A language
 * is only saved when it has text, and English is required because it is the fallback for the others.
 */
class ProductForm extends Form
{
    private const MAX_LINKS = 10;

    public ?Product $product = null;

    public string $name = '';

    public bool $isActive = true;

    public string $brochureUrl = '';

    public string $videoUrl = '';

    public string $price = '';

    public ?TemporaryUploadedFile $tds = null;

    public bool $removeTds = false;

    /** @var array<int, array{label: string, url: string}> */
    public array $links = [];

    /** @var array<string, string> */
    public array $descriptions = [];

    public function initialize(?Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name ?? '';
        $this->isActive = $product->is_active ?? true;
        $this->brochureUrl = $product->brochure_url ?? '';
        $this->videoUrl = $product->video_url ?? '';
        $this->price = $product?->price === null ? '' : (string) $product->price;
        $this->removeTds = false;
        $this->links = $product === null
            ? []
            : $product->links->map(fn ($link): array => ['label' => $link->label, 'url' => $link->url])->values()->all();
        $this->descriptions = [];

        foreach (config()->array('locales.landing') as $locale) {
            $this->descriptions[$locale] = $product?->translations->firstWhere('locale', $locale)->description ?? '';
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
                Rule::unique('products', 'name')->ignore($this->product?->id),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $slug = $this->slug();

                    if ($slug === '' || $this->slugTaken($slug)) {
                        $fail(__('products.messages.name_unusable'));
                    }
                },
            ],
            'isActive' => ['boolean'],
            'brochureUrl' => ['nullable', 'string', 'max:2048', 'url:https'],
            'videoUrl' => ['nullable', 'string', 'max:2048', 'url:https'],
            'price' => ['nullable', 'integer', 'min:0', 'max:9999999999'],
            'tds' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'removeTds' => ['boolean'],
            'links' => ['array', 'max:'.self::MAX_LINKS],
            'links.*.label' => ['required', 'string', 'max:100'],
            'links.*.url' => ['required', 'string', 'max:2048', 'url:https'],
            'descriptions' => [function (string $attribute, mixed $value, Closure $fail) use ($default): void {
                if (! in_array($default, $this->activeLocales(), true)) {
                    $fail(__('products.messages.default_language_required', ['language' => config()->string("locales.locales.{$default}.name")]));
                }
            }],
        ];

        foreach ($this->activeLocales() as $locale) {
            $rules["descriptions.{$locale}"] = ['string', 'max:2000'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        $attributes = [
            'name' => __('products.fields.name'),
            'isActive' => __('products.fields.active'),
            'brochureUrl' => __('products.fields.brochure_url'),
            'videoUrl' => __('products.fields.video_url'),
            'price' => __('products.fields.price'),
            'tds' => __('products.fields.tds'),
            'links' => __('products.fields.links'),
            'links.*.label' => __('products.fields.link_label'),
            'links.*.url' => __('products.fields.link_url'),
            'descriptions' => __('products.fields.languages'),
        ];

        foreach (array_keys($this->descriptions) as $locale) {
            $attributes["descriptions.{$locale}"] = __('products.fields.description').' ('.config()->string("locales.locales.{$locale}.name").')';
        }

        return $attributes;
    }

    /**
     * Trims every text and drops link rows the admin left completely empty.
     */
    public function normalize(): void
    {
        $this->name = trim($this->name);
        $this->brochureUrl = trim($this->brochureUrl);
        $this->videoUrl = trim($this->videoUrl);
        $this->price = trim($this->price);

        foreach ($this->descriptions as $locale => $text) {
            $this->descriptions[$locale] = trim($text);
        }

        $this->links = array_values(array_filter(
            array_map(fn (array $link): array => ['label' => trim($link['label']), 'url' => trim($link['url'])], $this->links),
            fn (array $link): bool => $link['label'] !== '' || $link['url'] !== '',
        ));
    }

    /**
     * The address comes from the name when the product is created. An existing address is kept so
     * links already shared keep working.
     */
    public function slug(): string
    {
        $current = $this->product?->slug;

        return $current !== null && $current !== '' ? $current : Str::slug($this->name);
    }

    private function slugTaken(string $slug): bool
    {
        return Product::query()
            ->where('slug', $slug)
            ->when($this->product !== null, fn ($query) => $query->whereKeyNot($this->product?->id))
            ->exists();
    }

    /**
     * @return array{brochure_url: string|null, video_url: string|null, price: int|null, is_active: bool, links: list<array{label: string, url: string}>, descriptions: array<string, string>}
     */
    public function payload(): array
    {
        $descriptions = [];

        foreach ($this->activeLocales() as $locale) {
            $descriptions[$locale] = $this->descriptions[$locale];
        }

        return [
            'brochure_url' => $this->brochureUrl !== '' ? $this->brochureUrl : null,
            'video_url' => $this->videoUrl !== '' ? $this->videoUrl : null,
            'price' => $this->price !== '' ? (int) $this->price : null,
            'is_active' => $this->isActive,
            'links' => array_values($this->links),
            'descriptions' => $descriptions,
        ];
    }
}
