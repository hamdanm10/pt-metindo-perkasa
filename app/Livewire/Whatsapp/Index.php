<?php

declare(strict_types=1);

namespace App\Livewire\Whatsapp;

use App\Actions\Whatsapp\DeleteWhatsappAgent;
use App\Actions\Whatsapp\MoveWhatsappAgent;
use App\Actions\Whatsapp\SetWhatsappAgentActive;
use App\Models\WhatsappAgent;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', WhatsappAgent::class);
    }

    public function move(MoveWhatsappAgent $moveWhatsappAgent, int $id, string $direction): void
    {
        $agent = WhatsappAgent::query()->findOrFail($id);

        $this->authorize('update', $agent);

        $moveWhatsappAgent->execute($agent, $direction === 'up');
    }

    public function toggle(SetWhatsappAgentActive $setActive, int $id): void
    {
        $agent = WhatsappAgent::query()->findOrFail($id);

        $this->authorize('update', $agent);

        $setActive->execute($agent, ! $agent->is_active);
    }

    public function delete(DeleteWhatsappAgent $deleteWhatsappAgent, int $id): void
    {
        $agent = WhatsappAgent::query()->findOrFail($id);

        $this->authorize('delete', $agent);

        $deleteWhatsappAgent->execute($agent);

        session()->now('status', __('whatsapp_manager.messages.deleted'));
    }

    public function render(): View
    {
        $this->authorize('viewAny', WhatsappAgent::class);

        return view('livewire.whatsapp.index', [
            'agents' => WhatsappAgent::query()->orderBy('position')->orderBy('id')->get(),
        ])->layout('components.dashboard.layout', ['title' => __('whatsapp_manager.title')]);
    }
}
