<?php

declare(strict_types=1);

namespace App\Actions\Inquiries;

use App\Models\Inquiry;
use App\Models\User;
use App\Services\Inquiries\InquiryImageStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SaveInquiry
{
    public function __construct(private readonly InquiryImageStore $images) {}

    /**
     * @param  array{customer_id: int, question: string, answer: string, asked_at: string|null, service_id: int|null, product_id: int|null}  $data
     * @param  list<UploadedFile>  $newImages
     * @param  list<int>  $removeImageIds  Ids of pictures to remove; ids that are not this record's are ignored.
     */
    public function execute(?Inquiry $inquiry, User $actor, array $data, array $newImages, array $removeImageIds): Inquiry
    {
        $stored = array_map(fn (UploadedFile $file): string => $this->images->store($file), $newImages);

        try {
            /** @var list<string> $removedPaths */
            $removedPaths = [];

            $saved = DB::transaction(function () use ($inquiry, $actor, $data, $stored, $removeImageIds, &$removedPaths): Inquiry {
                $inquiry ??= new Inquiry(['created_by' => $actor->id]);
                $inquiry->fill($data)->save();

                $removed = $inquiry->images()->whereIn('id', $removeImageIds)->get();
                $removedPaths = $removed->pluck('path')->all();
                $inquiry->images()->whereIn('id', $removed->modelKeys())->delete();

                foreach ($stored as $path) {
                    $inquiry->images()->create(['path' => $path]);
                }

                return $inquiry;
            });
        } catch (\Throwable $exception) {
            $this->images->delete($stored);

            throw $exception;
        }

        $this->images->delete($removedPaths);

        return $saved;
    }
}
