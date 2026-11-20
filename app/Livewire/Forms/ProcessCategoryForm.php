<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\ProcessCategory;
use Livewire\Form;

class ProcessCategoryForm extends Form
{
    public string $name = '';

    public string $description = '';

    public function setProcessCategory(ProcessCategory $processCategory): void
    {
        $this->name = $processCategory->name;
        $this->description = (string) $processCategory->description;
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
            'name' => __('process_categories.fields.name'),
            'description' => __('process_categories.fields.description'),
        ];
    }

    public function normalize(): void
    {
        $this->name = trim($this->name);
        $this->description = trim($this->description);
    }
}
