<?php

declare(strict_types=1);

namespace App\Livewire\Inquiries;

use App\Livewire\Concerns\ScopesInquiryKind;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Show extends Component
{
    use ScopesInquiryKind;

    #[Locked]
    public Inquiry $inquiry;

    public function mount(Inquiry $inquiry, string $kind = ''): void
    {
        $this->setKind($kind);

        abort_unless($this->actor()->can('view', $inquiry), 404);

        $this->inquiry = $inquiry->load(['customer:id,name,role', 'service:id,name', 'product:id,name', 'creator:id,name', 'images']);

        abort_unless($this->ownerRole() === null || $this->inquiry->customer?->role === $this->ownerRole(), 404);
    }

    public function render(): View
    {
        $actor = $this->actor();

        return view('livewire.inquiries.show', [
            'area' => $actor->role->area(),
            'kind' => $this->kind,
            'routes' => $this->routePrefix(),
            'staffArea' => $actor->role->usesStaffArea(),
            'canUpdate' => $actor->can('update', $this->inquiry),
        ])->layout('components.dashboard.layout', ['title' => __('inquiries.show_title')]);
    }
}
