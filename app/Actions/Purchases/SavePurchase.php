<?php

declare(strict_types=1);

namespace App\Actions\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Services\Purchases\PurchaseFileStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SavePurchase
{
    public function __construct(private readonly PurchaseFileStore $files) {}

    /**
     * The product rows replace the saved ones; each row's product name is copied from the product.
     *
     * @param  array{buyer_id: int, purchased_at: string, invoice_number: string|null, notes: string|null}  $data
     * @param  list<array{product_id: int, quantity: int}>  $items
     * @param  list<UploadedFile>  $newFiles
     * @param  list<int>  $removeFileIds  Ids of files to remove; ids that are not this record's are ignored.
     */
    public function execute(?Purchase $purchase, User $actor, array $data, array $items, array $newFiles, array $removeFileIds): Purchase
    {
        $stored = array_map(fn (UploadedFile $file): array => $this->files->store($file), $newFiles);

        try {
            /** @var list<string> $removedPaths */
            $removedPaths = [];

            $saved = DB::transaction(function () use ($purchase, $actor, $data, $items, $stored, $removeFileIds, &$removedPaths): Purchase {
                $purchase ??= new Purchase(['created_by' => $actor->id]);
                $purchase->fill($data)->save();

                $names = Product::query()->whereIn('id', array_column($items, 'product_id'))->pluck('name', 'id');

                $purchase->items()->delete();
                foreach ($items as $item) {
                    $purchase->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => (string) $names[$item['product_id']],
                        'quantity' => $item['quantity'],
                    ]);
                }

                $removed = $purchase->files()->whereIn('id', $removeFileIds)->get();
                $removedPaths = $removed->pluck('path')->all();
                $purchase->files()->whereIn('id', $removed->modelKeys())->delete();

                foreach ($stored as $file) {
                    $purchase->files()->create($file);
                }

                return $purchase;
            });
        } catch (\Throwable $exception) {
            $this->files->delete(array_column($stored, 'path'));

            throw $exception;
        }

        $this->files->delete($removedPaths);

        return $saved;
    }
}
