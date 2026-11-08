<div class="space-y-8">
    <x-dashboard.page-header :title="$user->name">
        <p class="mt-2 text-muted" dir="ltr">{{ $user->email }}</p>
    </x-dashboard.page-header>

    <x-dashboard.flash />

    <x-dashboard.card class="max-w-2xl">
        <h2 class="text-xl font-bold">{{ __('users.edit_title') }}</h2>
        <form wire:submit="save" class="mt-6 space-y-6" novalidate>
            <x-dashboard.field name="form.name" :label="__('users.fields.name')" wire:model="form.name" autocomplete="off" />
            <x-dashboard.field name="form.email" :label="__('users.fields.email')" type="email" wire:model="form.email" autocomplete="off" />
            <div class="grid gap-6 sm:grid-cols-2">
                @include('livewire.users.partials.role-and-locale')
            </div>

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit">{{ __('users.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route('app.users.index')">{{ __('users.back') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>

    <x-dashboard.card class="max-w-2xl">
        <h2 class="text-xl font-bold">{{ __('users.reset_title') }}</h2>
        <p class="mt-2 text-sm text-muted">{{ __('users.reset_text') }}</p>
        <form wire:submit="resetPassword" class="mt-6 space-y-6" novalidate>
            <x-dashboard.field name="newPassword" :label="__('users.fields.new_password')" :hint="__('users.password_hint')" type="password" wire:model="newPassword" autocomplete="new-password" />
            <x-dashboard.button type="submit" variant="secondary">{{ __('users.reset_password') }}</x-dashboard.button>
        </form>
    </x-dashboard.card>

    @can('setActive', $user)
        <x-dashboard.card class="max-w-2xl">
            <h2 class="text-xl font-bold">{{ __('users.status_title') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ $user->is_active ? __('users.deactivate_text') : __('users.activate_text') }}</p>
            @error('active')
                <p class="mt-3 text-sm font-semibold text-brand-700" role="alert">{{ $message }}</p>
            @enderror
            <div class="mt-6">
                @if ($user->is_active)
                    <x-dashboard.button variant="danger" wire:click="toggleActive" wire:confirm="{{ __('users.confirm_deactivate') }}">{{ __('users.deactivate') }}</x-dashboard.button>
                @else
                    <x-dashboard.button wire:click="toggleActive">{{ __('users.activate') }}</x-dashboard.button>
                @endif
            </div>
        </x-dashboard.card>
    @endcan
</div>
