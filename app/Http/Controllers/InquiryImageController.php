<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InquiryImage;
use App\Services\Inquiries\InquiryImageStore;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves a picture of a record from the private disk to the signed-in roles allowed to read the record.
 * Used by both areas (`app.inquiry-images.show` and `customer.inquiry-images.show`).
 */
class InquiryImageController extends Controller
{
    public function show(InquiryImage $image): StreamedResponse
    {
        Gate::authorize('view', $image->inquiry);

        return Storage::disk(InquiryImageStore::DISK)->response($image->path, null, [
            'Cache-Control' => 'private, max-age=86400',
            'Content-Type' => 'image/webp',
        ]);
    }
}
