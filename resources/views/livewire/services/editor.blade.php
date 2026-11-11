<div>
    <x-dashboard.page-header :title="$service === null ? __('services.create_title') : __('services.edit_title')" />

    <x-dashboard.card class="max-w-2xl">
        <form wire:submit="save" class="space-y-6" novalidate>
            <x-dashboard.field name="form.name" :label="__('services.fields.name')" wire:model="form.name" autocomplete="off" required />

            <div>
                <label for="form-description" class="field-label">{{ __('services.fields.description') }}</label>
                <textarea id="form-description" rows="6" wire:model="form.description"
                          @error('form.description') aria-invalid="true" aria-describedby="form-description-error" @enderror
                          class="field-input"></textarea>
                @error('form.description')
                    <p id="form-description-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit">{{ __('services.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route('app.services.index')">{{ __('services.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
