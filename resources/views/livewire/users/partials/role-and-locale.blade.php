<x-dashboard.select name="form.role" :label="__('users.fields.role')" wire:model="form.role">
    @foreach (\App\Enums\Role::cases() as $role)
        <option value="{{ $role->value }}">{{ __('dashboard.roles.'.$role->value) }}</option>
    @endforeach
</x-dashboard.select>
<x-dashboard.select name="form.locale" :label="__('users.fields.locale')" wire:model="form.locale">
    @foreach (config('locales.dashboard') as $code)
        <option value="{{ $code }}">{{ config("locales.locales.{$code}.name") }}</option>
    @endforeach
</x-dashboard.select>
