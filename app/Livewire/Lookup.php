<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\ResolvesActor;
use App\Services\Lookup\LookupSearch;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;
use Livewire\Component;

/**
 * A searchable dropdown for records that can be many (customers, distributors, services, products). The options are
 * not loaded with the page: the first page of matches loads when the dropdown opens, and the search
 * box asks the server again (after a short pause in typing) as the user types.
 *
 * Use it inside a Livewire page: `<livewire:lookup source="customer" wire:model="form.customerId" input-id="form-customerId" />`.
 * The value is the chosen id as a string, or an empty string when nothing is chosen.
 */
class Lookup extends Component
{
    use ResolvesActor;

    #[Modelable]
    public string $value = '';

    #[Locked]
    public string $source = 'customer';

    /** Text of the first option that clears the choice. Empty means the choice cannot be cleared. */
    #[Locked]
    public string $emptyLabel = '';

    #[Locked]
    public string $placeholder = '';

    #[Locked]
    public string $inputId = '';

    #[Reactive]
    public bool $invalid = false;

    public bool $opened = false;

    public string $search = '';

    /**
     * Customers' and distributors' names and emails are for the staff area only; services and products can be searched by anyone signed in.
     */
    public function hydrate(): void
    {
        $this->ensureAllowed();
    }

    public function mount(string $source = 'customer', string $emptyLabel = '', string $placeholder = '', string $inputId = '', bool $invalid = false): void
    {
        abort_unless(in_array($source, LookupSearch::SOURCES, true), 404);

        $this->source = $source;
        $this->emptyLabel = $emptyLabel;
        $this->placeholder = $placeholder;
        $this->inputId = $inputId;
        $this->invalid = $invalid;

        $this->ensureAllowed();
    }

    private function ensureAllowed(): void
    {
        abort_unless(! in_array($this->source, LookupSearch::ACCOUNT_SOURCES, true) || $this->actor()->role->usesStaffArea(), 403);
    }

    public function openPanel(): void
    {
        $this->opened = true;
        $this->search = '';
    }

    public function select(string $id, LookupSearch $lookup): void
    {
        if (ctype_digit($id) && $lookup->label($this->source, (int) $id) !== null) {
            $this->value = $id;
        }

        $this->closePanel();
    }

    public function clear(): void
    {
        $this->value = '';
        $this->closePanel();
    }

    private function closePanel(): void
    {
        $this->opened = false;
        $this->search = '';
    }

    public function render(LookupSearch $lookup): View
    {
        return view('livewire.lookup', [
            'label' => ctype_digit($this->value) ? $lookup->label($this->source, (int) $this->value) : null,
            'results' => $this->opened ? $lookup->search($this->source, $this->search) : ['items' => [], 'more' => false],
            'limit' => LookupSearch::LIMIT,
        ]);
    }
}
