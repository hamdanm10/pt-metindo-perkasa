<?php

declare(strict_types=1);

namespace App\Services\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

/**
 * Stores the one picture of a service: re-encoded to WebP at 4:3 under a random name,
 * so the uploaded file's name and metadata never reach the public disk.
 */
class ServiceImageStore
{
    public const DISK = 'public';

    private const WIDTH = 1200;

    private const HEIGHT = 900;

    public function store(UploadedFile $file): string
    {
        $encoded = (new ImageManager(new Driver))
            ->decodePath($file->getRealPath())
            ->cover(self::WIDTH, self::HEIGHT)
            ->encodeUsingFormat(Format::WEBP, quality: 82);

        $path = 'services/'.Str::random(40).'.webp';

        Storage::disk(self::DISK)->put($path, (string) $encoded);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public function url(string $path): string
    {
        return Storage::disk(self::DISK)->url($path);
    }
}
