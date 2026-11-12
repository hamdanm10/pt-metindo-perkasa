<?php

declare(strict_types=1);

namespace App\Livewire\Inquiries;

use App\Livewire\Concerns\ResolvesActor;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Show extends Component
{
    use ResolvesActor;

    #[Locked]
    public Inquiry $inquiry;

    public function mount(Inquiry $inquiry): void
    {
        $this->authorize('view', $inquiry);

        $this->inquiry = $inquiry->load(['service:id,name', 'product:id,name', 'creator:id,name', 'images']);
    }

    public function render(): View
    {
        $actor = $this->actor();

        return view('livewire.inquiries.show', [
            'area' => $actor->role->area(),
            'staffArea' => $actor->role->usesStaffArea(),
            'canUpdate' => $actor->can('update', $this->inquiry),
        ])->layout('components.dashboard.layout', ['title' => __('inquiries.show_title')]);
    }
}
