<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * The two slides of the clients section: clients and partners. Logos come from
 * public/images/landing/clients and public/images/landing/partners. Partners fall back to the
 * names in config, and clients to labelled placeholders in sample mode. A slide with nothing to
 * show is left out.
 *
 * @phpstan-type Item array{src: string|null, name: string}
 * @phpstan-type Slide array{key: string, label: string, items: list<Item>}
 */
class LandingClients
{
    public const PER_SLIDE = 9;

    /** @var list<string> */
    private const EXTENSIONS = ['svg', 'webp', 'png', 'jpg', 'jpeg'];

    /**
     * @return list<Slide>
     */
    public function slides(): array
    {
        $clients = $this->logos('clients');

        if ($clients === [] && config()->boolean('landing.sample_content')) {
            $clients = array_fill(0, self::PER_SLIDE, ['src' => null, 'name' => 'Logo']);
        }

        $partners = $this->logos('partners');

        if ($partners === []) {
            $partners = array_values(array_map(
                fn (string $name): array => ['src' => null, 'name' => $name],
                array_slice(config()->array('landing.partners'), 0, self::PER_SLIDE),
            ));
        }

        $slides = [];

        if ($clients !== []) {
            $slides[] = ['key' => 'clients', 'label' => __('home.clients.clients'), 'items' => $clients];
        }

        if ($partners !== []) {
            $slides[] = ['key' => 'partners', 'label' => __('home.clients.partners'), 'items' => $partners];
        }

        return $slides;
    }

    /**
     * @return list<Item>
     */
    private function logos(string $folder): array
    {
        $directory = public_path("images/landing/{$folder}");
        $files = is_dir($directory) ? (glob($directory.'/*') ?: []) : [];
        sort($files);

        $items = [];

        foreach ($files as $file) {
            if (! is_file($file) || ! in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
                continue;
            }

            $items[] = [
                'src' => asset("images/landing/{$folder}/".basename($file)),
                'name' => ucwords(str_replace(['-', '_'], ' ', pathinfo($file, PATHINFO_FILENAME))),
            ];
        }

        return array_slice($items, 0, self::PER_SLIDE);
    }
}
