<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Services\Articles\LandingArticles;

/**
 * Static landing content read from config. The catalog moves to the database
 * in phase L4, and this class is the only place that needs to change.
 */
class LandingContent
{
    public function __construct(private readonly LandingArticles $articles) {}

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
     * @return list<array{key: string, name: string, lat: float, lng: float}>
     */
    public function subsidiaries(): array
    {
        return $this->sampleContentEnabled() ? config('landing.subsidiaries') : [];
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
            ['route' => 'landing.products.index', 'label' => 'landing.nav.products', 'active' => 'landing.products.*'],
            ['route' => 'landing.services', 'label' => 'landing.nav.services', 'active' => 'landing.services'],
        ];

        if ($this->articles->hasAny(app()->getLocale())) {
            $items[] = ['route' => 'landing.articles.index', 'label' => 'landing.nav.articles', 'active' => 'landing.articles.*'];
        }

        $items[] = ['route' => 'landing.contact', 'label' => 'landing.nav.contact', 'active' => 'landing.contact'];

        return $items;
    }
}
