<div>
    <x-dashboard.page-header :title="__('users.kinds.'.$kind.'.create_title')" :description="__('users.kinds.'.$kind.'.create_description')" />

    <x-dashboard.card class="max-w-2xl">
        <form wire:submit="save" class="space-y-6" novalidate>
            <x-dashboard.field name="form.name" :label="__('users.fields.name')" wire:model="form.name" autocomplete="off" required />
            <x-dashboard.field name="form.email" :label="__('users.fields.email')" type="email" wire:model="form.email" autocomplete="off" required />
            <x-dashboard.field name="form.password" :label="__('users.fields.initial_password')" :hint="__('users.password_hint')" type="password" wire:model="form.password" autocomplete="new-password" required />

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit">{{ __('users.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route($prefix.'.index')">{{ __('users.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
