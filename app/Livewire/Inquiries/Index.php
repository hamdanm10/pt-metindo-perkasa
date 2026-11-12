<?php

declare(strict_types=1);

namespace App\Livewire\Inquiries;

use App\Actions\Inquiries\DeleteInquiry;
use App\Actions\Inquiries\ListInquiries;
use App\Livewire\Concerns\ScopesInquiryKind;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The list of records. Shared by the staff area and the customer area: the area comes from the
 * signed-in role, and the buttons that change a record follow the policy.
 */
class Index extends Component
{
    use ScopesInquiryKind;
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $customer = '';

    #[Url(except: '')]
    public string $service = '';

    #[Url(except: '')]
    public string $product = '';

    public function mount(string $kind = ''): void
    {
        $this->setKind($kind);
        $this->authorize('viewAny', Inquiry::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCustomer(): void
    {
        $this->resetPage();
    }

    public function updatedService(): void
    {
        $this->resetPage();
    }

    public function updatedProduct(): void
    {
        $this->resetPage();
    }

    public function delete(DeleteInquiry $deleteInquiry, int $id): void
    {
        $inquiry = Inquiry::query()->findOrFail($id);

        $this->authorize('delete', $inquiry);

        $deleteInquiry->execute($inquiry);

        session()->now('status', __('inquiries.messages.deleted'));
    }

    public function render(ListInquiries $listInquiries): View
    {
        $this->authorize('viewAny', Inquiry::class);

        $actor = $this->actor();

        return view('livewire.inquiries.index', [
            'inquiries' => $listInquiries->execute(
                $actor,
                $this->ownerRole(),
                $this->search,
                ctype_digit($this->customer) ? (int) $this->customer : null,
                ctype_digit($this->service) ? (int) $this->service : null,
                ctype_digit($this->product) ? (int) $this->product : null,
            ),
            'area' => $actor->role->area(),
            'kind' => $this->kind,
            'routes' => $this->routePrefix(),
            'canCreate' => $actor->can('create', Inquiry::class),
        ])->layout('components.dashboard.layout', ['title' => $this->kind === '' ? __('inquiries.title') : __("inquiries.kinds.{$this->kind}.title")]);
    }
}
