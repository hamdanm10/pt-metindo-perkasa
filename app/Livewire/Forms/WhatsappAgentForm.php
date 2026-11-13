<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\WhatsappAgent;
use Illuminate\Validation\Rule;
use Livewire\Form;

class WhatsappAgentForm extends Form
{
    public ?WhatsappAgent $agent = null;

    public string $name = '';

    public string $number = '';

    public function setAgent(WhatsappAgent $agent): void
    {
        $this->agent = $agent;
        $this->name = $agent->name;
        $this->number = '+'.$agent->number;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            // Digits only after normalizing: the country code first (no leading zero), 8 to 15 digits in all.
            'number' => ['required', 'regex:/^[1-9]\d{7,14}$/', Rule::unique('whatsapp_agents', 'number')->ignore($this->agent?->id)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'name' => __('whatsapp_manager.fields.name'),
            'number' => __('whatsapp_manager.fields.number'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'number.regex' => __('whatsapp_manager.messages.bad_number'),
        ];
    }

    /**
     * Trims the name and keeps only the digits of the number, so "+62 812-0000-0001" is stored as 628120000001.
     */
    public function normalize(): void
    {
        $this->name = trim($this->name);
        $this->number = preg_replace('/\D+/', '', $this->number) ?? '';
    }
}
