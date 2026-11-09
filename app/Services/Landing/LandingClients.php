<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * The slides of the clients section: one per sector, made of the logo files found in
 * public/images/landing/clients/<sector>/, and one with the partner names. A sector without
 * logos has no slide, except in sample mode where it shows labelled placeholders.
 *
 * @phpstan-type Item array{src: string|null, name: string}
 * @phpstan-type Slide array{key: string, label: string, items: list<Item>}
 */
class LandingClients
{
    public const SECTORS = ['automotive', 'electronics', 'machinery', 'construction', 'furniture_hardware', 'oil_gas'];

    public const PER_SLIDE = 9;

    /** @var list<string> */
    private const EXTENSIONS = ['svg', 'webp', 'png', 'jpg', 'jpeg'];

    /**
     * @return list<Slide>
     */
    public function slides(): array
    {
        $slides = [];

        foreach (self::SECTORS as $sector) {
            $items = $this->logos($sector);

            if ($items === [] && config()->boolean('landing.sample_content')) {
                $items = array_fill(0, self::PER_SLIDE, ['src' => null, 'name' => 'Logo']);
            }

            if ($items !== []) {
                $slides[] = ['key' => $sector, 'label' => __("home.clients.sectors.{$sector}"), 'items' => $items];
            }
        }

        $partners = array_map(
            fn (string $name): array => ['src' => null, 'name' => $name],
            array_slice(config()->array('landing.partners'), 0, self::PER_SLIDE),
        );

        if ($partners !== []) {
            $slides[] = ['key' => 'partners', 'label' => __('home.clients.partners_short'), 'items' => array_values($partners)];
        }

        return $slides;
    }

    /**
     * @return list<Item>
     */
    private function logos(string $sector): array
    {
        $directory = public_path("images/landing/clients/{$sector}");
        $files = is_dir($directory) ? (glob($directory.'/*') ?: []) : [];
        sort($files);

        $items = [];

        foreach ($files as $file) {
            if (! is_file($file) || ! in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
                continue;
            }

            $name = pathinfo($file, PATHINFO_FILENAME);

            $items[] = [
                'src' => asset("images/landing/clients/{$sector}/".basename($file)),
                'name' => ucwords(str_replace(['-', '_'], ' ', $name)),
            ];
        }

        return array_slice($items, 0, self::PER_SLIDE);
    }
}
