<?php

declare(strict_types=1);

namespace App\Livewire\Purchases;

use App\Actions\Purchases\SavePurchase;
use App\Livewire\Concerns\ResolvesActor;
use App\Livewire\Concerns\ScopesPurchaseKind;
use App\Livewire\Forms\PurchaseForm;
use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use ResolvesActor;
    use ScopesPurchaseKind;
    use WithFileUploads;

    #[Locked]
    public ?Purchase $purchase = null;

    public PurchaseForm $form;

    /** @var list<TemporaryUploadedFile> */
    public array $uploads = [];

    public function mount(?Purchase $purchase = null, string $kind = ''): void
    {
        $this->setKind($kind);

        if ($purchase?->exists) {
            $this->authorize('update', $purchase);
            $this->purchase = $purchase->load(['items', 'files', 'buyer:id,role']);
            abort_unless($this->purchase->buyer?->role === $this->buyerRole(), 404);
        } else {
            $this->authorize('create', Purchase::class);
        }

        $this->form->kind = $this->kind;
        $this->form->initialize($this->purchase);
    }

    public function addItem(): void
    {
        if (count($this->form->items) < Purchase::MAX_ITEMS) {
            $this->form->items[] = $this->form->blankItem();
        }
    }

    public function removeItem(int $index): void
    {
        array_splice($this->form->items, $index, 1);
    }

    /**
     * Files picked in the field are checked one by one and added to the ones already waiting.
     */
    public function updatedUploads(): void
    {
        try {
            $this->validate(['uploads.*' => PurchaseForm::fileRules()], [], ['uploads.*' => __('purchases.fields.file')]);
        } catch (ValidationException $exception) {
            $this->uploads = [];

            throw $exception;
        }

        if ($this->form->remainingFiles() + count($this->form->files) + count($this->uploads) > Purchase::MAX_FILES) {
            $this->uploads = [];

            throw ValidationException::withMessages([
                'form.files' => __('purchases.messages.too_many_files', ['max' => Purchase::MAX_FILES]),
            ]);
        }

        $this->resetErrorBag('form.files');
        $this->form->files = [...$this->form->files, ...$this->uploads];
        $this->uploads = [];
    }

    public function removePending(int $index): void
    {
        array_splice($this->form->files, $index, 1);
        $this->resetErrorBag('form.files');
    }

    public function toggleRemoveFile(int $id): void
    {
        $this->form->removedFileIds = in_array($id, $this->form->removedFileIds, true)
            ? array_values(array_diff($this->form->removedFileIds, [$id]))
            : [...$this->form->removedFileIds, $id];
        $this->resetErrorBag('form.files');
    }

    public function save(SavePurchase $savePurchase): void
    {
        $this->purchase === null
            ? $this->authorize('create', Purchase::class)
            : $this->authorize('update', $this->purchase);

        $this->form->kind = $this->kind;
        $this->form->normalize();
        $this->form->validate();

        $savePurchase->execute(
            $this->purchase,
            $this->actor(),
            [
                'buyer_id' => (int) $this->form->buyerId,
                'purchased_at' => $this->form->purchasedAt,
                'invoice_number' => $this->form->invoiceNumber === '' ? null : $this->form->invoiceNumber,
                'notes' => $this->form->notes === '' ? null : $this->form->notes,
            ],
            array_map(fn (array $item): array => ['product_id' => (int) $item['productId'], 'quantity' => (int) $item['quantity']], $this->form->items),
            $this->form->files,
            $this->form->removedFileIds,
        );

        session()->flash('status', __($this->purchase === null ? 'purchases.messages.created' : 'purchases.messages.updated'));

        $this->redirectRoute($this->routePrefix().'.index');
    }

    public function render(): View
    {
        return view('livewire.purchases.editor', [
            'kind' => $this->kind,
            'routes' => $this->routePrefix(),
            'maxFiles' => Purchase::MAX_FILES,
            'maxItems' => Purchase::MAX_ITEMS,
        ])->layout('components.dashboard.layout', [
            'title' => __($this->purchase === null ? 'purchases.create_title' : 'purchases.edit_title'),
        ]);
    }
}
