<?php

declare(strict_types=1);

namespace App\Livewire\Purchases;

use App\Livewire\Concerns\ScopesPurchaseKind;
use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Show extends Component
{
    use ScopesPurchaseKind;

    #[Locked]
    public Purchase $purchase;

    public function mount(Purchase $purchase, string $kind = ''): void
    {
        $this->setKind($kind);

        abort_unless($this->actor()->can('view', $purchase), 404);

        $this->purchase = $purchase->load(['buyer:id,name,role', 'creator:id,name', 'items', 'files']);

        abort_unless($this->buyerRole() === null || $this->purchase->buyer?->role === $this->buyerRole(), 404);
    }

    public function render(): View
    {
        return view('livewire.purchases.show', [
            'area' => $this->actor()->role->area(),
            'staffArea' => $this->actor()->role->usesStaffArea(),
            'kind' => $this->kind,
            'routes' => $this->routePrefix(),
            'canUpdate' => $this->actor()->can('update', $this->purchase),
        ])->layout('components.dashboard.layout', ['title' => __('purchases.show_title')]);
    }
}
