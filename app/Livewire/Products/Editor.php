<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Actions\Products\SaveProduct;
use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editor extends Component
{
    #[Locked]
    public ?Product $product = null;

    public ProductForm $form;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->authorize('update', $product);
            $this->product = $product;
            $this->form->setProduct($product);
        } else {
            $this->authorize('create', Product::class);
        }
    }

    public function save(SaveProduct $saveProduct): void
    {
        $this->product === null
            ? $this->authorize('create', Product::class)
            : $this->authorize('update', $this->product);

        $this->form->normalize();
        $this->form->validate();

        $saveProduct->execute($this->product, $this->form->name, $this->form->description);

        session()->flash('status', __($this->product === null ? 'products.messages.created' : 'products.messages.updated'));

        $this->redirectRoute('app.products.index');
    }

    public function render(): View
    {
        return view('livewire.products.editor')
            ->layout('components.dashboard.layout', [
                'title' => __($this->product === null ? 'products.create_title' : 'products.edit_title'),
            ]);
    }
}
