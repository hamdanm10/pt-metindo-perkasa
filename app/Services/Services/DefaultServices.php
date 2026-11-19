<?php

declare(strict_types=1);

namespace App\Services\Services;

use App\Actions\Services\SaveService;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

/**
 * Brings the three services that the landing used to show from fixed texts into the database, so the admin
 * can carry on from them. The texts are read from `home.services.items` (title and the longer text, which becomes the description) and
 * `pages.services.detail.items` (summary, included, fit) in every landing language, and the pictures are copied from `public/images/landing/photos`. A service
 * whose address already exists is left alone, so running it again never overwrites the admin's changes.
 *
 * @phpstan-import-type TranslationInput from SaveService
 */
class DefaultServices
{
    private const SERVICES = [
        ['slug' => 'electroplating-service', 'key' => 'electroplating', 'photo' => 'electroplating'],
        ['slug' => 'chemical-supply', 'key' => 'chemical_supply', 'photo' => 'chemical-supply'],
        ['slug' => 'technical-consultation', 'key' => 'consultation', 'photo' => 'consultation'],
    ];

    public function __construct(private readonly SaveService $save) {}

    /**
     * @return int how many services were added
     */
    public function import(): int
    {
        $added = 0;

        foreach (self::SERVICES as $position => $definition) {
            if (Service::query()->where('slug', $definition['slug'])->exists()) {
                continue;
            }

            $translations = $this->translations($definition['key']);
            $service = $this->save->execute(null, $translations['en']['title'], $definition['slug'], $position, true, $translations);

            $this->copyPhoto($service, $definition['photo']);
            $added++;
        }

        return $added;
    }

    /**
     * @return array<string, TranslationInput>
     */
    private function translations(string $key): array
    {
        $translations = [];

        foreach (config()->array('locales.landing') as $locale) {
            $title = trans("home.services.items.{$key}.title", [], $locale);
            $included = trans("pages.services.detail.items.{$key}.included", [], $locale);

            // A language without these texts is skipped and shows English on the landing.
            if ($title === "home.services.items.{$key}.title") {
                continue;
            }

            $translations[$locale] = [
                'title' => $title,
                'summary' => $this->text("pages.services.detail.items.{$key}.summary", $locale),
                'description' => $this->text("home.services.items.{$key}.text", $locale),
                'included' => is_array($included) ? array_values(array_map('strval', $included)) : [],
                'fit' => $this->text("pages.services.detail.items.{$key}.fit", $locale),
            ];
        }

        return $translations;
    }

    private function text(string $key, string $locale): string
    {
        $text = trans($key, [], $locale);

        return is_string($text) && $text !== $key ? $text : '';
    }

    private function copyPhoto(Service $service, string $photo): void
    {
        $file = public_path("images/landing/photos/service-{$photo}.webp");

        if (! is_file($file)) {
            return;
        }

        $path = 'services/'.bin2hex(random_bytes(20)).'.webp';
        Storage::disk(ServiceImageStore::DISK)->put($path, (string) file_get_contents($file));
        $service->update(['image_path' => $path]);
    }
}
