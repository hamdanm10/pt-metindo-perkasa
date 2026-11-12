<?php

declare(strict_types=1);

namespace App\Services\Inquiries;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

/**
 * Stores the pictures of a record on the private disk: re-encoded to WebP under a random name and
 * scaled down (never cropped), so the uploaded file's name and metadata are not kept. They are served
 * only through a route that checks the signed-in user.
 */
class InquiryImageStore
{
    public const DISK = 'local';

    private const MAX_SIDE = 1600;

    public function store(UploadedFile $file): string
    {
        $encoded = (new ImageManager(new Driver))
            ->decodePath($file->getRealPath())
            ->scaleDown(self::MAX_SIDE, self::MAX_SIDE)
            ->encodeUsingFormat(Format::WEBP, quality: 82);

        $path = 'inquiries/'.Str::random(40).'.webp';

        Storage::disk(self::DISK)->put($path, (string) $encoded);

        return $path;
    }

    /**
     * @param  iterable<string>  $paths
     */
    public function delete(iterable $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
