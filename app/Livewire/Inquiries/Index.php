<?php

declare(strict_types=1);

namespace App\Livewire\Inquiries;

use App\Actions\Inquiries\DeleteInquiry;
use App\Actions\Inquiries\ListInquiries;
use App\Livewire\Concerns\ResolvesActor;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Service;
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
    use ResolvesActor;
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $service = '';

    #[Url(except: '')]
    public string $product = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Inquiry::class);
    }

    public function updatedSearch(): void
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
                $this->search,
                ctype_digit($this->service) ? (int) $this->service : null,
                ctype_digit($this->product) ? (int) $this->product : null,
            ),
            'services' => Service::query()->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'area' => $actor->role->area(),
            'canCreate' => $actor->can('create', Inquiry::class),
        ])->layout('components.dashboard.layout', ['title' => __('inquiries.title')]);
    }
}
