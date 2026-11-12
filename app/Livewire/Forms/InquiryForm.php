<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\Role;
use App\Models\Inquiry;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class InquiryForm extends Form
{
    public string $customerId = '';

    public string $question = '';

    public string $answer = '';

    public string $askedAt = '';

    public string $serviceId = '';

    public string $productId = '';

    /** @var list<TemporaryUploadedFile> */
    public array $images = [];

    /** @var list<int> */
    public array $removedImageIds = [];

    public int $keptImages = 0;

    public function initialize(?Inquiry $inquiry): void
    {
        if ($inquiry === null) {
            return;
        }

        $this->customerId = (string) $inquiry->customer_id;
        $this->askedAt = $inquiry->asked_at?->toDateString() ?? '';
        $this->question = $inquiry->question;
        $this->answer = $inquiry->answer;
        $this->serviceId = (string) $inquiry->service_id;
        $this->productId = (string) $inquiry->product_id;
        $this->keptImages = $inquiry->images->count();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customerId' => ['required', 'integer', Rule::exists('users', 'id')->where('role', Role::Customer->value)],
            'question' => ['required', 'string', 'max:5000'],
            'answer' => ['required', 'string', 'max:10000'],
            'askedAt' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'serviceId' => ['nullable', 'integer', Rule::exists('services', 'id')],
            'productId' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'images' => ['array', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($this->remainingImages() + count($this->images) > Inquiry::MAX_IMAGES) {
                    $fail(__('inquiries.messages.too_many_images', ['max' => Inquiry::MAX_IMAGES]));
                }
            }],
            'images.*' => self::imageRules(),
        ];
    }

    /**
     * @return list<string>
     */
    public static function imageRules(): array
    {
        return ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
    }

    /**
     * How many saved pictures stay after the removals.
     */
    public function remainingImages(): int
    {
        return max(0, $this->keptImages - count($this->removedImageIds));
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'customerId' => __('inquiries.fields.customer'),
            'question' => __('inquiries.fields.question'),
            'answer' => __('inquiries.fields.answer'),
            'askedAt' => __('inquiries.fields.asked_at'),
            'serviceId' => __('inquiries.fields.service'),
            'productId' => __('inquiries.fields.product'),
            'images' => __('inquiries.fields.images'),
            'images.*' => __('inquiries.fields.image'),
        ];
    }

    public function normalize(): void
    {
        $this->question = trim($this->question);
        $this->answer = trim($this->answer);
    }
}
