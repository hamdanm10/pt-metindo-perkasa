<?php

declare(strict_types=1);

namespace App\Services\Purchases;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores the documents of a purchase on the private disk under a random name. The uploaded name is kept
 * only as text for display and for the download, never as part of the path. They are served only
 * through a route that checks the signed-in user.
 */
class PurchaseFileStore
{
    public const DISK = 'local';

    /**
     * @return array{path: string, name: string, mime_type: string, size: int}
     */
    public function store(UploadedFile $file): array
    {
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        $path = 'purchases/'.Str::random(40).'.'.$extension;

        Storage::disk(self::DISK)->putFileAs('purchases', $file, basename($path));

        $name = Str::limit(preg_replace('/[\x00-\x1F\x7F\/\\\\]+/', '', basename($file->getClientOriginalName())) ?? '', 200, '');

        return [
            'path' => $path,
            'name' => $name === '' ? 'file.'.$extension : $name,
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => (int) $file->getSize(),
        ];
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
