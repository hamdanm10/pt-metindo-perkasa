<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PurchaseFile;
use App\Services\Purchases\PurchaseFileStore;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Sends a document of a purchase from the private disk as a download, to the signed-in roles allowed to read the purchase.
 */
class PurchaseFileController extends Controller
{
    public function show(PurchaseFile $file): StreamedResponse
    {
        abort_unless(Gate::allows('view', $file->purchase), 404);

        return Storage::disk(PurchaseFileStore::DISK)->download($file->path, $file->name, [
            'Content-Type' => $file->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
