<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\Role;
use App\Models\Purchase;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class PurchaseForm extends Form
{
    /** The kind of buyer this form is for (`customer` or `distributor`); set by the page from its route, not by the user. */
    public string $kind = 'customer';

    public string $buyerId = '';

    public string $purchasedAt = '';

    public string $invoiceNumber = '';

    public string $notes = '';

    /**
     * The product rows. `key` only identifies a row on the page; `name` is the saved product name, shown when the product no longer exists.
     *
     * @var list<array{key: string, productId: string, quantity: string, name: string}>
     */
    public array $items = [];

    /** @var list<TemporaryUploadedFile> */
    public array $files = [];

    /** @var list<int> */
    public array $removedFileIds = [];

    public int $keptFiles = 0;

    public function initialize(?Purchase $purchase): void
    {
        if ($purchase === null) {
            $this->items = [$this->blankItem()];

            return;
        }

        $this->buyerId = (string) $purchase->buyer_id;
        $this->purchasedAt = $purchase->purchased_at->toDateString();
        $this->invoiceNumber = (string) $purchase->invoice_number;
        $this->notes = (string) $purchase->notes;
        $this->items = array_values($purchase->items->map(fn ($item): array => [
            'key' => Str::random(8),
            'productId' => (string) $item->product_id,
            'quantity' => (string) $item->quantity,
            'name' => $item->product_name,
        ])->all());
        $this->keptFiles = $purchase->files->count();
    }

    /**
     * @return array{key: string, productId: string, quantity: string, name: string}
     */
    public function blankItem(): array
    {
        return ['key' => Str::random(8), 'productId' => '', 'quantity' => '1', 'name' => ''];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'buyerId' => ['required', 'integer', Rule::exists('users', 'id')->where('role', in_array($this->kind, [Role::Customer->value, Role::Distributor->value], true) ? $this->kind : Role::Customer->value)],
            'purchasedAt' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'invoiceNumber' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:'.Purchase::MAX_ITEMS],
            'items.*.productId' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'files' => ['array', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($this->remainingFiles() + count($this->files) > Purchase::MAX_FILES) {
                    $fail(__('purchases.messages.too_many_files', ['max' => Purchase::MAX_FILES]));
                }
            }],
            'files.*' => self::fileRules(),
        ];
    }

    /**
     * @return list<string>
     */
    public static function fileRules(): array
    {
        return ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
    }

    /**
     * How many saved files stay after the removals.
     */
    public function remainingFiles(): int
    {
        return max(0, $this->keptFiles - count($this->removedFileIds));
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'buyerId' => __('purchases.kinds.'.$this->kind.'.buyer'),
            'purchasedAt' => __('purchases.fields.purchased_at'),
            'invoiceNumber' => __('purchases.fields.invoice_number'),
            'notes' => __('purchases.fields.notes'),
            'items' => __('purchases.fields.products'),
            'items.*.productId' => __('purchases.fields.product'),
            'items.*.quantity' => __('purchases.fields.quantity'),
            'files' => __('purchases.fields.files'),
            'files.*' => __('purchases.fields.file'),
        ];
    }

    public function normalize(): void
    {
        $this->invoiceNumber = trim($this->invoiceNumber);
        $this->notes = trim($this->notes);
    }
}
