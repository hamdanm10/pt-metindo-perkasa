<div>
    <x-dashboard.page-header :title="$agent === null ? __('whatsapp_manager.create_title') : __('whatsapp_manager.edit_title')" :description="$agent === null ? __('whatsapp_manager.create_description') : __('whatsapp_manager.edit_description')" />

    <x-dashboard.card class="max-w-2xl">
        <form wire:submit="save" class="space-y-6" novalidate>
            <x-dashboard.field name="form.name" :label="__('whatsapp_manager.fields.name')" wire:model="form.name" autocomplete="off" required />
            <x-dashboard.field name="form.number" :label="__('whatsapp_manager.fields.number')" wire:model="form.number" type="tel" dir="ltr" autocomplete="off" :hint="__('whatsapp_manager.hints.number')" required />

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit">{{ __('whatsapp_manager.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route('app.whatsapp.index')">{{ __('whatsapp_manager.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
