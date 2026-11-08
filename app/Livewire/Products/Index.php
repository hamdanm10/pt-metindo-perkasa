<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Actions\Products\ListProducts;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Product::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(ListProducts $listProducts): View
    {
        $this->authorize('viewAny', Product::class);

        return view('livewire.products.index', ['products' => $listProducts->execute($this->search)])
            ->layout('components.dashboard.layout', ['title' => __('products.title')]);
    }
}
