<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Form;

class ProductForm extends Form
{
    public string $name = '';

    public string $description = '';

    public function setProduct(Product $product): void
    {
        $this->name = $product->name;
        $this->description = (string) $product->description;
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
            'name' => __('products.fields.name'),
            'description' => __('products.fields.description'),
        ];
    }

    public function normalize(): void
    {
        $this->name = trim($this->name);
        $this->description = trim($this->description);
    }
}
