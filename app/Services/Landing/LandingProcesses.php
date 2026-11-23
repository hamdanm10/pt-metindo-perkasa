<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Models\ProcessCategory;
use App\Services\ProcessCategories\ProcessCategoryImageStore;
use App\Services\Products\ProductTdsStore;
use Illuminate\Support\Facades\Cache;

/**
 * The process catalog of the landing as the admin set it in the dashboard: active categories, their active
 * processes, the steps of each, and the active products of each step, all in the admin's order. A text without
 * its own translation shows the English one, and stays null when there is none. Cached per language and cleared
 * whenever the admin saves or moves a category, process, or product.
 *
 * @phpstan-type Product array{slug: string, name: string, description: string|null, brochure_url: string|null, video_url: string|null, price: string|null, tds_url: string|null, links: list<array{label: string, url: string}>}
 * @phpstan-type Step array{name: string, description: string|null, products: list<Product>}
 * @phpstan-type Process array{slug: string, name: string, description: string|null, steps: list<Step>}
 * @phpstan-type Category array{slug: string, name: string, description: string|null, image: string|null, processes: list<Process>}
 */
class LandingProcesses
{
    public function __construct(
        private readonly ProductTdsStore $tds,
        private readonly ProcessCategoryImageStore $images,
    ) {}

    /**
     * @return list<Category>
     */
    public function all(string $locale): array
    {
        /** @var list<Category> $categories */
        $categories = Cache::rememberForever($this->key($locale), fn (): array => $this->load($locale));

        return $categories;
    }

    public function flush(): void
    {
        foreach (config()->array('locales.landing') as $locale) {
            Cache::forget($this->key($locale));
        }
    }

    private function key(string $locale): string
    {
        return "landing.processes.{$locale}";
    }

    /**
     * @return list<Category>
     */
    private function load(string $locale): array
    {
        $default = config()->string('locales.default');
        $languages = array_values(array_unique([$locale, $default]));
        $translations = fn ($query) => $query->whereIn('locale', $languages);

        $categories = ProcessCategory::query()
            ->with([
                'translations' => $translations,
                'processes' => fn ($query) => $query->where('is_active', true)->orderBy('position')->orderBy('id'),
                'processes.translations' => $translations,
                'processes.steps.translations' => $translations,
                'processes.steps.products' => fn ($query) => $query->where('products.is_active', true),
                'processes.steps.products.translations' => $translations,
                'processes.steps.products.links',
            ])
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $result = [];

        foreach ($categories as $category) {
            $processes = [];

            foreach ($category->processes as $process) {
                $steps = [];

                foreach ($process->steps as $step) {
                    $products = [];

                    foreach ($step->products as $product) {
                        $links = [];

                        foreach ($product->links as $link) {
                            $links[] = ['label' => $link->label, 'url' => $link->url];
                        }

                        $products[] = [
                            'slug' => $product->slug,
                            'name' => $product->name,
                            'description' => $this->text($product->translations->pluck('description', 'locale')->all(), $locale, $default),
                            'brochure_url' => $product->brochure_url,
                            'video_url' => $product->video_url,
                            'price' => $product->price === null ? null : 'Rp '.number_format($product->price, 0, ',', '.'),
                            'tds_url' => $product->tds_path === null ? null : $this->tds->url($product->tds_path),
                            'links' => $links,
                        ];
                    }

                    $steps[] = [
                        'name' => $step->name,
                        'description' => $this->text($step->translations->pluck('description', 'locale')->all(), $locale, $default),
                        'products' => $products,
                    ];
                }

                $processes[] = [
                    'slug' => $process->slug,
                    'name' => $process->name,
                    'description' => $this->text($process->translations->pluck('description', 'locale')->all(), $locale, $default),
                    'steps' => $steps,
                ];
            }

            $result[] = [
                'slug' => (string) $category->slug,
                'name' => $category->name,
                'description' => $this->text($category->translations->pluck('description', 'locale')->all(), $locale, $default),
                'image' => $category->image_path === null ? null : $this->images->url($category->image_path),
                'processes' => $processes,
            ];
        }

        return $result;
    }

    /**
     * The description in the language, else in the default language, else null.
     *
     * @param  array<string, string|null>  $byLocale  the descriptions keyed by language
     */
    private function text(array $byLocale, string $locale, string $default): ?string
    {
        foreach ([$locale, $default] as $code) {
            $description = $byLocale[$code] ?? null;

            if (is_string($description) && trim($description) !== '') {
                return $description;
            }
        }

        return null;
    }
}
