<?php

declare(strict_types=1);

namespace App\Livewire\Whatsapp;

use App\Actions\Whatsapp\SaveWhatsappAgent;
use App\Livewire\Forms\WhatsappAgentForm;
use App\Models\WhatsappAgent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editor extends Component
{
    #[Locked]
    public ?WhatsappAgent $agent = null;

    public WhatsappAgentForm $form;

    public function mount(?WhatsappAgent $agent = null): void
    {
        if ($agent?->exists) {
            $this->authorize('update', $agent);
            $this->agent = $agent;
            $this->form->setAgent($agent);
        } else {
            $this->authorize('create', WhatsappAgent::class);
        }
    }

    public function save(SaveWhatsappAgent $saveWhatsappAgent): void
    {
        $this->agent === null
            ? $this->authorize('create', WhatsappAgent::class)
            : $this->authorize('update', $this->agent);

        $this->form->normalize();
        $this->form->validate();

        $saveWhatsappAgent->execute($this->agent, $this->form->name, $this->form->number);

        session()->flash('status', __($this->agent === null ? 'whatsapp_manager.messages.created' : 'whatsapp_manager.messages.updated'));

        $this->redirectRoute('app.whatsapp.index');
    }

    public function render(): View
    {
        return view('livewire.whatsapp.editor')
            ->layout('components.dashboard.layout', [
                'title' => __($this->agent === null ? 'whatsapp_manager.create_title' : 'whatsapp_manager.edit_title'),
            ]);
    }
}
