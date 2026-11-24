<div>
    <x-dashboard.page-header :title="$purchase === null ? __('purchases.create_title') : __('purchases.edit_title')" :description="$purchase === null ? __('purchases.kinds.'.$kind.'.create_description') : __('purchases.edit_description')" />

    <x-dashboard.card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6" novalidate>
            <x-dashboard.lookup-field name="form.buyerId" :label="__('purchases.kinds.'.$kind.'.buyer')" required>
                <livewire:lookup :source="$kind" wire:model="form.buyerId" input-id="form-buyerId" :placeholder="__('purchases.kinds.'.$kind.'.choose_buyer')" :invalid="$errors->has('form.buyerId')" />
            </x-dashboard.lookup-field>

            <div class="grid gap-6 sm:grid-cols-2">
                <x-dashboard.field name="form.purchasedAt" :label="__('purchases.fields.purchased_at')" type="date" wire:model="form.purchasedAt" :max="now()->toDateString()" required />
                <x-dashboard.field name="form.invoiceNumber" :label="__('purchases.fields.invoice_number')" wire:model="form.invoiceNumber" maxlength="100" />
            </div>

            <div>
                <label for="form-notes" class="field-label">{{ __('purchases.fields.notes') }}</label>
                <textarea id="form-notes" rows="3" wire:model="form.notes" maxlength="2000"
                          @error('form.notes') aria-invalid="true" aria-describedby="form-notes-error" @enderror
                          class="field-input"></textarea>
                @error('form.notes')
                    <p id="form-notes-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <fieldset>
                <legend class="field-label">{{ __('purchases.fields.products') }}<span class="text-brand-600" aria-hidden="true"> *</span></legend>
                @error('form.items')
                    <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                @enderror
                <ul class="mt-3 space-y-4">
                    @foreach ($form->items as $index => $item)
                        <li wire:key="item-{{ $item['key'] }}" class="grid gap-4 rounded-lg border border-ink/10 p-4 sm:grid-cols-[1fr_8rem_auto] sm:items-start">
                            <div>
                                <x-dashboard.lookup-field :name="'form.items.'.$index.'.productId'" :label="__('purchases.fields.product')">
                                    <livewire:lookup source="product" wire:model="form.items.{{ $index }}.productId" :input-id="'form-items-'.$index.'-productId'" :placeholder="__('purchases.choose_product')" :invalid="$errors->has('form.items.'.$index.'.productId')" wire:key="lookup-{{ $item['key'] }}" />
                                </x-dashboard.lookup-field>
                                @if ($item['name'] !== '' && $item['productId'] === '')
                                    <p class="mt-2 text-sm text-muted">{{ __('purchases.product_removed', ['name' => $item['name']]) }}</p>
                                @endif
                            </div>
                            <x-dashboard.field :name="'form.items.'.$index.'.quantity'" :label="__('purchases.fields.quantity')" type="number" min="1" max="999999" step="1" inputmode="numeric" wire:model="form.items.{{ $index }}.quantity" />
                            <div class="sm:pt-7">
                                @if (count($form->items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})" class="row-action row-action--danger">{{ __('purchases.remove_product', ['number' => $loop->iteration]) }}</button>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if (count($form->items) < $maxItems)
                    <x-dashboard.button type="button" variant="secondary" class="mt-4" wire:click="addItem">{{ __('purchases.add_product') }}</x-dashboard.button>
                @endif
            </fieldset>

            <div>
                <label for="uploads" class="field-label">{{ __('purchases.fields.files') }}</label>
                <input id="uploads" type="file" multiple wire:model="uploads" accept="application/pdf,image/jpeg,image/png"
                       @if ($errors->has('uploads.*') || $errors->has('form.files') || $errors->has('form.files.*')) aria-invalid="true" aria-describedby="uploads-error" @else aria-describedby="uploads-hint" @endif
                       class="mt-3 block w-full rounded-lg border-2 border-dashed border-ink/25 bg-white p-3 text-base file:me-4 file:min-h-10 file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-600 file:px-4 file:font-bold file:text-white hover:file:bg-brand-700">
                <p id="uploads-hint" class="mt-2 text-sm text-muted">{{ __('purchases.hints.files', ['max' => $maxFiles]) }}</p>
                <div id="uploads-error" role="alert">
                    @foreach ($errors->get('uploads.*') as $messages)
                        @foreach ($messages as $message)
                            <p class="mt-2 text-sm font-bold text-brand-700">{{ $message }}</p>
                        @endforeach
                    @endforeach
                    @foreach ($errors->get('form.files*') as $messages)
                        @foreach ($messages as $message)
                            <p class="mt-2 text-sm font-bold text-brand-700">{{ $message }}</p>
                        @endforeach
                    @endforeach
                </div>
                <div wire:loading wire:target="uploads" class="mt-2 text-sm text-muted">…</div>

                @if (($purchase && $purchase->files->isNotEmpty()) || $form->files !== [])
                    <ul class="mt-4 divide-y divide-ink/10 rounded-lg border border-ink/10">
                        @if ($purchase)
                            @foreach ($purchase->files as $file)
                                @php($removing = in_array($file->id, $form->removedFileIds, true))
                                <li wire:key="file-{{ $file->id }}" class="flex items-center justify-between gap-4 p-3">
                                    <span class="min-w-0 {{ $removing ? 'opacity-40' : '' }}">
                                        <span class="block truncate font-semibold" title="{{ $file->name }}">{{ $file->name }}</span>
                                        <span class="text-xs text-muted">{{ $removing ? __('purchases.will_be_removed') : \Illuminate\Support\Number::fileSize($file->size) }}</span>
                                    </span>
                                    <button type="button" wire:click="toggleRemoveFile({{ $file->id }})" class="row-action shrink-0 {{ $removing ? '' : 'row-action--danger' }}">
                                        {{ $removing ? __('purchases.undo_remove', ['name' => $file->name]) : __('purchases.remove_file', ['name' => $file->name]) }}
                                    </button>
                                </li>
                            @endforeach
                        @endif
                        @foreach ($form->files as $index => $file)
                            <li wire:key="pending-{{ $index }}-{{ $file->getFilename() }}" class="flex items-center justify-between gap-4 p-3">
                                <span class="min-w-0">
                                    <span class="block truncate font-semibold" title="{{ $file->getClientOriginalName() }}">{{ $file->getClientOriginalName() }}</span>
                                    <span class="text-xs text-muted">{{ __('purchases.new_file') }}</span>
                                </span>
                                <button type="button" wire:click="removePending({{ $index }})" class="row-action row-action--danger shrink-0">{{ __('purchases.remove_file', ['name' => $file->getClientOriginalName()]) }}</button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="save,uploads">{{ __('purchases.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route($routes.'.index')">{{ __('purchases.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
