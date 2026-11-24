<?php

declare(strict_types=1);

namespace App\Livewire\Purchases;

use App\Actions\Purchases\DeletePurchase;
use App\Actions\Purchases\ListPurchases;
use App\Livewire\Concerns\ScopesPurchaseKind;
use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ScopesPurchaseKind;
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $buyer = '';

    public function mount(string $kind = ''): void
    {
        $this->setKind($kind);
        $this->authorize('viewAny', Purchase::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedBuyer(): void
    {
        $this->resetPage();
    }

    public function delete(DeletePurchase $deletePurchase, int $id): void
    {
        $purchase = Purchase::query()->findOrFail($id);

        $this->authorize('delete', $purchase);

        $deletePurchase->execute($purchase);

        session()->now('status', __('purchases.messages.deleted'));
    }

    public function render(ListPurchases $listPurchases): View
    {
        $this->authorize('viewAny', Purchase::class);

        $actor = $this->actor();

        return view('livewire.purchases.index', [
            'purchases' => $listPurchases->execute($actor, $this->buyerRole(), $this->search, ctype_digit($this->buyer) ? (int) $this->buyer : null),
            'area' => $actor->role->area(),
            'kind' => $this->kind,
            'routes' => $this->routePrefix(),
            'canCreate' => $actor->can('create', Purchase::class),
        ])->layout('components.dashboard.layout', ['title' => $this->kind === '' ? __('purchases.title') : __("purchases.kinds.{$this->kind}.title")]);
    }
}
