<?php

declare(strict_types=1);

namespace App\Livewire\Inquiries;

use App\Actions\Inquiries\SaveInquiry;
use App\Livewire\Concerns\ResolvesActor;
use App\Livewire\Forms\InquiryForm;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use ResolvesActor;
    use WithFileUploads;

    #[Locked]
    public ?Inquiry $inquiry = null;

    public InquiryForm $form;

    /** @var list<TemporaryUploadedFile> */
    public array $uploads = [];

    public function mount(?Inquiry $inquiry = null): void
    {
        if ($inquiry?->exists) {
            $this->authorize('update', $inquiry);
            $this->inquiry = $inquiry->load('images');
        } else {
            $this->authorize('create', Inquiry::class);
        }

        $this->form->initialize($this->inquiry);
    }

    /**
     * Pictures picked in the field are checked one by one and added to the ones already waiting.
     */
    public function updatedUploads(): void
    {
        try {
            $this->validate(['uploads.*' => InquiryForm::imageRules()], [], ['uploads.*' => __('inquiries.fields.image')]);
        } catch (ValidationException $exception) {
            $this->uploads = [];

            throw $exception;
        }

        if ($this->form->remainingImages() + count($this->form->images) + count($this->uploads) > Inquiry::MAX_IMAGES) {
            $this->uploads = [];

            throw ValidationException::withMessages([
                'form.images' => __('inquiries.messages.too_many_images', ['max' => Inquiry::MAX_IMAGES]),
            ]);
        }

        $this->resetErrorBag('form.images');
        $this->form->images = [...$this->form->images, ...$this->uploads];
        $this->uploads = [];
    }

    public function removePending(int $index): void
    {
        array_splice($this->form->images, $index, 1);
        $this->resetErrorBag('form.images');
    }

    public function toggleRemoveImage(int $id): void
    {
        $this->form->removedImageIds = in_array($id, $this->form->removedImageIds, true)
            ? array_values(array_diff($this->form->removedImageIds, [$id]))
            : [...$this->form->removedImageIds, $id];
        $this->resetErrorBag('form.images');
    }

    public function save(SaveInquiry $saveInquiry): void
    {
        $this->inquiry === null
            ? $this->authorize('create', Inquiry::class)
            : $this->authorize('update', $this->inquiry);

        $this->form->normalize();
        $this->form->validate();

        $saveInquiry->execute(
            $this->inquiry,
            $this->actor(),
            [
                'question' => $this->form->question,
                'answer' => $this->form->answer,
                'asked_at' => $this->form->askedAt,
                'service_id' => $this->form->serviceId === '' ? null : (int) $this->form->serviceId,
                'product_id' => $this->form->productId === '' ? null : (int) $this->form->productId,
            ],
            $this->form->images,
            $this->form->removedImageIds,
        );

        session()->flash('status', __($this->inquiry === null ? 'inquiries.messages.created' : 'inquiries.messages.updated'));

        $this->redirectRoute('app.inquiries.index');
    }

    public function render(): View
    {
        return view('livewire.inquiries.editor', [
            'services' => Service::query()->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'max' => Inquiry::MAX_IMAGES,
        ])->layout('components.dashboard.layout', [
            'title' => __($this->inquiry === null ? 'inquiries.create_title' : 'inquiries.edit_title'),
        ]);
    }
}
