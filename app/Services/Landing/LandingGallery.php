<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * The slides of the full-screen gallery. Each slide is one team photo at
 * public/images/landing/photos/gallery-<key>.{webp,jpg,jpeg,png,svg} with a label and a caption
 * that describe the photo (lang/<locale>/home.php, `gallery.slides`). A slide whose photo is
 * missing is skipped, except in sample mode where it shows a labelled placeholder.
 *
 * @phpstan-type Slide array{key: string, label: string, caption: string}
 */
class LandingGallery
{
    /**
     * @var list<string>
     */
    public const KEYS = ['celebration', 'teamwork', 'togetherness'];

    /**
     * @return list<Slide>
     */
    public function slides(): array
    {
        $slides = [];

        foreach (self::KEYS as $key) {
            if (! config('landing.sample_content') && ! $this->hasPhoto($key)) {
                continue;
            }

            $slides[] = [
                'key' => $key,
                'label' => __("home.gallery.slides.{$key}.label"),
                'caption' => __("home.gallery.slides.{$key}.caption"),
            ];
        }

        return $slides;
    }

    private function hasPhoto(string $key): bool
    {
        foreach (['webp', 'svg', 'jpg', 'jpeg', 'png'] as $extension) {
            if (is_file(public_path("images/landing/photos/gallery-{$key}.{$extension}"))) {
                return true;
            }
        }

        return false;
    }
}
