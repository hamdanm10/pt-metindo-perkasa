<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Services\Articles\LandingArticles;
use Illuminate\Support\Str;

/**
 * Static landing content read from config. The catalog moves to the database
 * in phase L4, and this class is the only place that needs to change.
 */
class LandingContent
{
    public function __construct(
        private readonly LandingArticles $articles,
        private readonly LandingServices $services,
    ) {}

    public function sampleContentEnabled(): bool
    {
        return (bool) config('landing.sample_content');
    }

    /**
     * @return list<array{slug: string, name: string, processes: list<string>}>
     */
    public function categories(): array
    {
        return config('landing.categories');
    }

    /**
     * @return array{slug: string, name: string, processes: list<string>}|null
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
     * @param  array{slug: string, name: string, processes: list<string>}  $category
     * @return list<array{slug: string, name: string, description: string}>
     */
    public function processes(array $category): array
    {
        return array_map(function (string $name): array {
            $slug = Str::slug($name);
            $description = config("landing.process_details.{$slug}.description");

            return [
                'slug' => $slug,
                'name' => $name,
                'description' => is_string($description) ? $description : __('pages.process.flow', ['name' => $name]),
            ];
        }, $category['processes']);
    }

    /**
     * @return list<array{name: string, description: string, materials: list<string>}>
     */
    private function configuredSteps(string $slug): array
    {
        return config("landing.process_details.{$slug}.steps", []);
    }

    /**
     * One process with its category, steps, materials, and the other processes of that category.
     *
     * @return array{slug: string, name: string, description: string, category: array{slug: string, name: string}, steps: list<array{name: string, description: string, materials: list<array{slug: string, name: string}>}>, materials: int, others: list<array{slug: string, name: string, description: string}>}|null
     */
    public function process(string $slug): ?array
    {
        foreach ($this->categories() as $category) {
            foreach ($this->processes($category) as $process) {
                if ($process['slug'] !== $slug) {
                    continue;
                }

                $steps = array_map(fn (array $step): array => [
                    'name' => $step['name'],
                    'description' => $step['description'],
                    'materials' => array_map(fn (string $name): array => ['slug' => Str::slug($name), 'name' => $name], $step['materials']),
                ], $this->configuredSteps($slug));

                return [
                    ...$process,
                    'category' => ['slug' => $category['slug'], 'name' => $category['name']],
                    'steps' => $steps,
                    'materials' => array_sum(array_map(fn (array $step): int => count($step['materials']), $steps)),
                    'others' => array_values(array_filter(
                        $this->processes($category),
                        fn (array $other): bool => $other['slug'] !== $slug,
                    )),
                ];
            }
        }

        return null;
    }

    /**
     * @return list<array{key: string, name: string, lat: float, lng: float, timezone: string}>
     */
    public function subsidiaries(): array
    {
        return $this->sampleContentEnabled() ? $this->locations() : [];
    }

    /**
     * The configured group locations, whether or not sample mode is on.
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
