<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Actions\Products\SaveProduct;
use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use App\Services\Products\ProductTdsStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use WithFileUploads;

    private const MAX_LINKS = 10;

    #[Locked]
    public ?Product $product = null;

    public ProductForm $form;

    public string $activeLocale = 'en';

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->authorize('update', $product);
            $product->load(['translations', 'links']);
            $this->product = $product;
        } else {
            $this->authorize('create', Product::class);
        }

        $this->form->initialize($this->product);
        $this->activeLocale = config()->string('locales.default');
    }

    public function updatedFormTds(): void
    {
        $this->form->validateOnly('tds');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function addLink(): void
    {
        if (count($this->form->links) < self::MAX_LINKS) {
            $this->form->links[] = ['label' => '', 'url' => ''];
        }
    }

    public function removeLink(int $index): void
    {
        unset($this->form->links[$index]);
        $this->form->links = array_values($this->form->links);
    }

    public function save(SaveProduct $saveProduct): void
    {
        $this->product === null
            ? $this->authorize('create', Product::class)
            : $this->authorize('update', $this->product);

        $this->form->normalize();
        $this->form->validate();

        $saveProduct->execute(
            $this->product,
            $this->form->name,
            $this->form->slug(),
            $this->form->payload(),
            $this->form->tds,
            $this->form->removeTds,
        );

        session()->flash('status', __($this->product === null ? 'products.messages.created' : 'products.messages.updated'));

        $this->redirectRoute('app.products.index');
    }

    public function render(ProductTdsStore $tds): View
    {
        $path = $this->product?->tds_path;

        return view('livewire.products.editor', [
            'currentTds' => $path === null ? null : $tds->url($path),
        ])->layout('components.dashboard.layout', [
            'title' => __($this->product === null ? 'products.create_title' : 'products.edit_title'),
        ]);
    }
}
