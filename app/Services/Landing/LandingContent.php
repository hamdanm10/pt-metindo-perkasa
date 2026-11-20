<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Services\Articles\LandingArticles;
use Illuminate\Support\Str;

/**
 * @phpstan-import-type Category from LandingProcesses
 * @phpstan-import-type Process from LandingProcesses
 * @phpstan-import-type Product from LandingProcesses
 *
 * The landing content: the process catalog from the dashboard (the static list in the configuration is the
 * fallback until a category is active), the group locations, and the menu.
 */
class LandingContent
{
    public function __construct(
        private readonly LandingArticles $articles,
        private readonly LandingServices $services,
        private readonly LandingProcesses $catalog,
    ) {}

    /**
     * The catalog the landing shows: the static list in the configuration, or, when `landing.catalog_source` is
     * "database", what the admin set in the dashboard (the static list while no category is active yet).
     *
     * @return list<array{slug: string, name: string, description: string|null, processes: list<array{slug: string, name: string, description: string|null, steps: list<array{name: string, description: string|null, products: list<array{slug: string, name: string, description: string|null, brochure_url: string|null, video_url: string|null, price: string|null, tds_url: string|null, links: list<array{label: string, url: string}>}>}>}>}>
     */
    private function source(): array
    {
        $catalog = config('landing.catalog_source') === 'database' ? $this->catalog->all(app()->getLocale()) : [];

        return $catalog !== [] ? $catalog : $this->configured();
    }

    /**
     * The static catalog: categories from `landing.categories`, and steps and materials from
     * `landing.process_details`. Only the name of a material is known.
     *
     * @return list<array{slug: string, name: string, description: string|null, processes: list<array{slug: string, name: string, description: string|null, steps: list<array{name: string, description: string|null, products: list<array{slug: string, name: string, description: string|null, brochure_url: string|null, video_url: string|null, price: string|null, tds_url: string|null, links: list<array{label: string, url: string}>}>}>}>}>
     */
    private function configured(): array
    {
        return array_values(array_map($this->configuredCategory(...), config('landing.categories')));
    }

    /**
     * @param  array{slug: string, name: string, processes: list<string>}  $category
     * @return Category
     */
    private function configuredCategory(array $category): array
    {
        $description = __("pages.processes.categories.{$category['slug']}");

        return [
            'slug' => $category['slug'],
            'name' => $category['name'],
            'description' => is_string($description) && $description !== "pages.processes.categories.{$category['slug']}" ? $description : null,
            'processes' => array_map($this->configuredProcess(...), $category['processes']),
        ];
    }

    /**
     * @return Process
     */
    private function configuredProcess(string $name): array
    {
        $slug = Str::slug($name);
        $description = config("landing.process_details.{$slug}.description");

        return [
            'slug' => $slug,
            'name' => $name,
            'description' => is_string($description) ? $description : null,
            'steps' => array_map(fn (array $step): array => [
                'name' => $step['name'],
                'description' => $step['description'],
                'products' => array_map(fn (string $material): array => [
                    'slug' => Str::slug($material),
                    'name' => $material,
                    'description' => null,
                    'brochure_url' => null,
                    'video_url' => null,
                    'price' => null,
                    'tds_url' => null,
                    'links' => [],
                ], $step['materials']),
            ], $this->configuredSteps($slug)),
        ];
    }

    /**
     * @return list<array{name: string, description: string, materials: list<string>}>
     */
    private function configuredSteps(string $slug): array
    {
        return config("landing.process_details.{$slug}.steps", []);
    }

    /**
     * @return list<array{slug: string, name: string, description: string, processes: list<array{slug: string, name: string}>}>
     */
    public function categories(): array
    {
        return array_map(fn (array $category): array => [
            'slug' => $category['slug'],
            'name' => $category['name'],
            'description' => $category['description'] ?? '',
            'processes' => array_map(fn (array $process): array => ['slug' => $process['slug'], 'name' => $process['name']], $category['processes']),
        ], $this->source());
    }

    /**
     * @return array{slug: string, name: string, description: string, processes: list<array{slug: string, name: string}>}|null
     */
    public function category(string $slug): ?array
    {
        foreach ($this->categories() as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }

        return null;
    }

    /**
     * The processes of a category with their slug and short description.
     *
     * @param  array{slug: string, name: string, description: string, processes: list<array{slug: string, name: string}>}  $category
     * @return list<array{slug: string, name: string, description: string}>
     */
    public function processes(array $category): array
    {
        foreach ($this->source() as $item) {
            if ($item['slug'] === $category['slug']) {
                return array_map(fn (array $process): array => [
                    'slug' => $process['slug'],
                    'name' => $process['name'],
                    'description' => $process['description'] ?? __('pages.process.flow', ['name' => $process['name']]),
                ], $item['processes']);
            }
        }

        return [];
    }

    /**
     * One process with its category, steps, products, and the other processes of that category.
     *
     * @return array{slug: string, name: string, description: string, category: array{slug: string, name: string}, steps: list<array{name: string, description: string, products: list<array{slug: string, name: string, description: string|null, brochure_url: string|null, video_url: string|null, price: string|null, tds_url: string|null, links: list<array{label: string, url: string}>}>}>, products: int, others: list<array{slug: string, name: string, description: string}>}|null
     */
    public function process(string $slug): ?array
    {
        foreach ($this->source() as $category) {
            foreach ($category['processes'] as $process) {
                if ($process['slug'] !== $slug) {
                    continue;
                }

                $steps = array_map(fn (array $step): array => [
                    'name' => $step['name'],
                    'description' => $step['description'] ?? '',
                    'products' => $step['products'],
                ], $process['steps']);

                return [
                    'slug' => $process['slug'],
                    'name' => $process['name'],
                    'description' => $process['description'] ?? __('pages.process.flow', ['name' => $process['name']]),
                    'category' => ['slug' => $category['slug'], 'name' => $category['name']],
                    'steps' => $steps,
                    'products' => array_sum(array_map(fn (array $step): int => count($step['products']), $steps)),
                    'others' => array_values(array_map(
                        fn (array $other): array => [
                            'slug' => $other['slug'],
                            'name' => $other['name'],
                            'description' => $other['description'] ?? __('pages.process.flow', ['name' => $other['name']]),
                        ],
                        array_filter($category['processes'], fn (array $other): bool => $other['slug'] !== $slug),
                    )),
                ];
            }
        }

        return null;
    }

    /**
     * The group locations from the configuration.
     *
     * @return list<array{key: string, name: string, lat: float, lng: float, timezone: string}>
     */
    public function locations(): array
    {
        return config('landing.subsidiaries');
    }

    /**
     * Menu entries for pages that exist right now.
     *
     * @return list<array{route: string, label: string, active: string}>
     */
    public function menu(): array
    {
        $items = [
            ['route' => 'landing.home', 'label' => 'landing.nav.home', 'active' => 'landing.home'],
            ['route' => 'landing.about', 'label' => 'landing.nav.about', 'active' => 'landing.about'],
            ['route' => 'landing.processes.index', 'label' => 'landing.nav.processes', 'active' => 'landing.processes.*'],
        ];

        if ($this->services->all(app()->getLocale()) !== []) {
            $items[] = ['route' => 'landing.services.index', 'label' => 'landing.nav.services', 'active' => 'landing.services.*'];
        }

        if ($this->articles->hasAny(app()->getLocale())) {
            $items[] = ['route' => 'landing.articles.index', 'label' => 'landing.nav.articles', 'active' => 'landing.articles.*'];
        }

        $items[] = ['route' => 'landing.contact', 'label' => 'landing.nav.contact', 'active' => 'landing.contact'];

        return $items;
    }
}
