<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Service;
use Livewire\Form;

class ServiceForm extends Form
{
    public string $name = '';

    public string $description = '';

    public function setService(Service $service): void
    {
        $this->name = $service->name;
        $this->description = (string) $service->description;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'name' => __('services.fields.name'),
            'description' => __('services.fields.description'),
        ];
    }

    public function normalize(): void
    {
        $this->name = trim($this->name);
        $this->description = trim($this->description);
    }
}
