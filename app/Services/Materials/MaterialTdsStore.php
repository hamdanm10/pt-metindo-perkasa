<?php

declare(strict_types=1);

namespace App\Services\Materials;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores the TDS (technical data sheet) PDF of a material under a random name on the public disk,
 * so the uploaded file's own name never reaches it.
 */
class MaterialTdsStore
{
    public const DISK = 'public';

    public function store(UploadedFile $file): string
    {
        $path = 'materials/tds/'.Str::random(40).'.pdf';

        Storage::disk(self::DISK)->put($path, (string) file_get_contents($file->getRealPath()));

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
