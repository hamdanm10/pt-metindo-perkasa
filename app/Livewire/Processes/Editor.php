<?php

declare(strict_types=1);

namespace App\Livewire\Processes;

use App\Actions\Processes\SaveProcess;
use App\Livewire\Forms\ProcessForm;
use App\Models\Process;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editor extends Component
{
    #[Locked]
    public ?Process $process = null;

    public ProcessForm $form;

    public string $activeLocale = 'en';

    /**
     * The product dropdown of each step. A chosen product is added to the step and the dropdown is cleared.
     *
     * @var array<int, string>
     */
    public array $picker = [];

    public function mount(?Process $process = null): void
    {
        if ($process?->exists) {
            $this->authorize('update', $process);
            $process->load(['translations', 'steps.translations', 'steps.products:id']);
            $this->process = $process;
        } else {
            $this->authorize('create', Process::class);
        }

        $this->form->initialize($this->process);
        $this->activeLocale = config()->string('locales.default');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function addStep(): void
    {
        if (count($this->form->steps) < ProcessForm::MAX_STEPS) {
            $this->form->steps[] = $this->form->blankStep();
        }
    }

    public function removeStep(int $index): void
    {
        unset($this->form->steps[$index]);
        $this->form->steps = array_values($this->form->steps);
        $this->picker = [];
    }

    public function moveStep(int $index, bool $up): void
    {
        $target = $index + ($up ? -1 : 1);

        if (isset($this->form->steps[$index], $this->form->steps[$target])) {
            [$this->form->steps[$index], $this->form->steps[$target]] = [$this->form->steps[$target], $this->form->steps[$index]];
            $this->picker = [];
        }
    }

    public function updatedPicker(string $value, string $key): void
    {
        $index = (int) $key;
        $this->picker[$index] = '';

        if (! isset($this->form->steps[$index]) || ! ctype_digit($value)) {
            return;
        }

        $id = (int) $value;
        $current = $this->form->steps[$index]['products'];

        if (in_array($id, $current, true) || count($current) >= ProcessForm::MAX_PRODUCTS || ! Product::query()->whereKey($id)->exists()) {
            return;
        }

        $this->form->steps[$index]['products'][] = $id;
    }

    public function removeProduct(int $index, int $productId): void
    {
        if (isset($this->form->steps[$index])) {
            $this->form->steps[$index]['products'] = array_values(array_filter(
                $this->form->steps[$index]['products'],
                fn (int $id): bool => $id !== $productId,
            ));
        }
    }

    public function save(SaveProcess $saveProcess): void
    {
        $this->process === null
            ? $this->authorize('create', Process::class)
            : $this->authorize('update', $this->process);

        $this->form->normalize();
        $this->form->validate();

        $saveProcess->execute(
            $this->process,
            $this->form->name,
            $this->form->slug(),
            (int) $this->form->processCategoryId,
            $this->form->isActive,
            $this->form->descriptionsPayload(),
            $this->form->stepsPayload(),
        );

        session()->flash('status', __($this->process === null ? 'processes.messages.created' : 'processes.messages.updated'));

        $this->redirectRoute('app.processes.index');
    }

    public function render(): View
    {
        // Every step needs a dropdown value to bind to, even before a product is chosen.
        $this->picker = array_fill(0, count($this->form->steps), '');

        $ids = array_merge(...array_map(fn (array $step): array => $step['products'], $this->form->steps ?: [['products' => []]]));

        return view('livewire.processes.editor', [
            'productNames' => $ids === [] ? [] : Product::query()->whereKey($ids)->pluck('name', 'id')->all(),
        ])->layout('components.dashboard.layout', [
            'title' => __($this->process === null ? 'processes.create_title' : 'processes.edit_title'),
        ]);
    }
}
