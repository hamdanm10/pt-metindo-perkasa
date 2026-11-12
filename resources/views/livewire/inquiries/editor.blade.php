<div>
    <x-dashboard.page-header :title="$inquiry === null ? __('inquiries.create_title') : __('inquiries.edit_title')" :description="$inquiry === null ? __('inquiries.create_description') : __('inquiries.edit_description')" />

    <x-dashboard.card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6" novalidate>
            <x-dashboard.lookup-field name="form.customerId" :label="__('inquiries.fields.customer')" required>
                <livewire:lookup source="customer" wire:model="form.customerId" input-id="form-customerId" :placeholder="__('inquiries.choose_customer')" :invalid="$errors->has('form.customerId')" />
            </x-dashboard.lookup-field>

            <div>
                <label for="form-question" class="field-label">{{ __('inquiries.fields.question') }}<span class="text-brand-600" aria-hidden="true"> *</span></label>
                <textarea id="form-question" rows="3" wire:model="form.question" required
                          @error('form.question') aria-invalid="true" aria-describedby="form-question-error" @enderror
                          class="field-input"></textarea>
                @error('form.question')
                    <p id="form-question-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="form-answer" class="field-label">{{ __('inquiries.fields.answer') }}<span class="text-brand-600" aria-hidden="true"> *</span></label>
                <textarea id="form-answer" rows="7" wire:model="form.answer" required
                          @error('form.answer') aria-invalid="true" aria-describedby="form-answer-error" @enderror
                          class="field-input"></textarea>
                @error('form.answer')
                    <p id="form-answer-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <x-dashboard.field name="form.askedAt" :label="__('inquiries.fields.asked_at')" type="date" wire:model="form.askedAt" :max="now()->toDateString()" />
                <x-dashboard.lookup-field name="form.serviceId" :label="__('inquiries.fields.service')">
                    <livewire:lookup source="service" wire:model="form.serviceId" input-id="form-serviceId" :placeholder="__('inquiries.none')" :empty-label="__('inquiries.none')" :invalid="$errors->has('form.serviceId')" />
                </x-dashboard.lookup-field>
                <x-dashboard.lookup-field name="form.productId" :label="__('inquiries.fields.product')">
                    <livewire:lookup source="product" wire:model="form.productId" input-id="form-productId" :placeholder="__('inquiries.none')" :empty-label="__('inquiries.none')" :invalid="$errors->has('form.productId')" />
                </x-dashboard.lookup-field>
            </div>

            <div>
                <label for="uploads" class="field-label">{{ __('inquiries.fields.images') }}</label>
                <input id="uploads" type="file" multiple wire:model="uploads" accept="image/jpeg,image/png,image/webp"
                       @if ($errors->has('uploads.*') || $errors->has('form.images') || $errors->has('form.images.*')) aria-invalid="true" aria-describedby="uploads-error" @else aria-describedby="uploads-hint" @endif
                       class="mt-3 block w-full rounded-lg border-2 border-dashed border-ink/25 bg-white p-3 text-base file:me-4 file:min-h-10 file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-600 file:px-4 file:font-bold file:text-white hover:file:bg-brand-700">
                <p id="uploads-hint" class="mt-2 text-sm text-muted">{{ __('inquiries.hints.images', ['max' => $max]) }}</p>
                <div id="uploads-error" role="alert">
                    @foreach ($errors->get('uploads.*') as $messages)
                        @foreach ($messages as $message)
                            <p class="mt-2 text-sm font-bold text-brand-700">{{ $message }}</p>
                        @endforeach
                    @endforeach
                    @foreach ($errors->get('form.images*') as $messages)
                        @foreach ($messages as $message)
                            <p class="mt-2 text-sm font-bold text-brand-700">{{ $message }}</p>
                        @endforeach
                    @endforeach
                </div>
                <div wire:loading wire:target="uploads" class="mt-2 text-sm text-muted">…</div>

                @if (($inquiry && $inquiry->images->isNotEmpty()) || $form->images !== [])
                    <ul class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @if ($inquiry)
                            @foreach ($inquiry->images as $image)
                                @php($removing = in_array($image->id, $form->removedImageIds, true))
                                <li wire:key="image-{{ $image->id }}">
                                    <div class="overflow-hidden rounded-lg border border-ink/10 bg-lavender {{ $removing ? 'opacity-40' : '' }}">
                                        <img src="{{ route('app.inquiry-images.show', $image) }}" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover">
                                    </div>
                                    @if ($removing)
                                        <p class="mt-1 text-xs text-muted">{{ __('inquiries.will_be_removed') }}</p>
                                    @endif
                                    <button type="button" wire:click="toggleRemoveImage({{ $image->id }})" class="row-action mt-2 {{ $removing ? '' : 'row-action--danger' }}">
                                        {{ $removing ? __('inquiries.undo_remove', ['number' => $loop->iteration]) : __('inquiries.remove_picture', ['number' => $loop->iteration]) }}
                                    </button>
                                </li>
                            @endforeach
                        @endif
                        @foreach ($form->images as $index => $file)
                            <li wire:key="pending-{{ $index }}-{{ $file->getFilename() }}">
                                <div class="overflow-hidden rounded-lg border border-ink/10 bg-lavender">
                                    @if ($file->isPreviewable())
                                        <img src="{{ $file->temporaryUrl() }}" alt="" class="aspect-[4/3] w-full object-cover">
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-muted">{{ __('inquiries.new_picture') }}</p>
                                <button type="button" wire:click="removePending({{ $index }})" class="row-action row-action--danger mt-2">{{ __('inquiries.remove_picture', ['number' => ($inquiry?->images->count() ?? 0) + $loop->iteration]) }}</button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="save,uploads">{{ __('inquiries.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route('app.inquiries.index')">{{ __('inquiries.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
