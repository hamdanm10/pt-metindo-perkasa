<?php

declare(strict_types=1);

namespace App\Actions\Inquiries;

use App\Models\Inquiry;
use App\Services\Inquiries\InquiryImageStore;

class DeleteInquiry
{
    public function __construct(private readonly InquiryImageStore $images) {}

    public function execute(Inquiry $inquiry): void
    {
        /** @var list<string> $paths */
        $paths = $inquiry->images()->pluck('path')->all();

        $inquiry->delete();

        $this->images->delete($paths);
    }
}
