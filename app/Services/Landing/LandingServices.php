<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Models\Service;
use App\Services\Services\ServiceImageStore;
use Illuminate\Support\Facades\Cache;

/**
 * The services of the landing as the admin set them in the dashboard: active services with an address and
 * English text, in the admin's order. A language without its own text shows the English text. Cached per
 * language and cleared whenever a service is saved (see `SaveService`).
 *
 * @phpstan-type Item array{slug: string, title: string, summary: string|null, description: string|null, included: list<string>, fit: string|null, image: string|null}
 */
class LandingServices
{
    public function __construct(private readonly ServiceImageStore $images) {}

    /**
     * @return list<Item>
     */
    public function all(string $locale): array
    {
        /** @var list<Item> $items */
        $items = Cache::rememberForever($this->key($locale), fn (): array => $this->load($locale));

        return $items;
    }

    /**
     * @return Item|null
     */
    public function find(string $locale, string $slug): ?array
    {
        foreach ($this->all($locale) as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }

    public function flush(): void
    {
        foreach (config()->array('locales.landing') as $locale) {
            Cache::forget($this->key($locale));
        }
    }

    private function key(string $locale): string
    {
        return "landing.services.{$locale}";
    }

    /**
     * @return list<Item>
     */
    private function load(string $locale): array
    {
        $default = config()->string('locales.default');
        $items = [];

        $services = Service::query()
            ->with(['translations' => fn ($query) => $query->whereIn('locale', array_unique([$locale, $default]))])
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        foreach ($services as $service) {
            $text = $service->translations->firstWhere('locale', $locale) ?? $service->translations->firstWhere('locale', $default);

            if ($text === null || $service->slug === null) {
                continue;
            }

            $items[] = [
                'slug' => $service->slug,
                'title' => $text->title,
                'summary' => $text->summary,
                'description' => $text->description,
                'included' => array_values(array_map('strval', $text->included ?? [])),
                'fit' => $text->fit,
                'image' => $service->image_path === null ? null : $this->images->url($service->image_path),
            ];
        }

        return $items;
    }
}
