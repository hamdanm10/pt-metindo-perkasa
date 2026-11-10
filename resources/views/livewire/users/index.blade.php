<div>
    <x-dashboard.page-header :title="__('users.title')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid flex-1 gap-4 sm:grid-cols-3">
                <x-dashboard.field name="search" :label="__('users.search')" type="search" wire:model.live.debounce.300ms="search" />
                <x-dashboard.select name="role" :label="__('users.fields.role')" wire:model.live="role">
                    <option value="">{{ __('users.all_roles') }}</option>
                    @foreach (\App\Enums\Role::cases() as $role)
                        <option value="{{ $role->value }}">{{ __('dashboard.roles.'.$role->value) }}</option>
                    @endforeach
                </x-dashboard.select>
                <x-dashboard.select name="status" :label="__('users.fields.status')" wire:model.live="status">
                    <option value="">{{ __('users.all_statuses') }}</option>
                    <option value="active">{{ __('users.status.active') }}</option>
                    <option value="inactive">{{ __('users.status.inactive') }}</option>
                </x-dashboard.select>
            </div>
            <x-dashboard.button :href="route('app.users.create')">{{ __('users.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('users.fields.name') }}</th>
                        <th scope="col">{{ __('users.fields.email') }}</th>
                        <th scope="col">{{ __('users.fields.role') }}</th>
                        <th scope="col">{{ __('users.fields.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('users.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="font-semibold">{{ $user->name }}</td>
                            <td dir="ltr">{{ $user->email }}</td>
                            <td><x-dashboard.badge tone="role">{{ __('dashboard.roles.'.$user->role->value) }}</x-dashboard.badge></td>
                            <td>
                                <x-dashboard.badge :tone="$user->is_active ? 'active' : 'muted'">{{ __('users.status.'.($user->is_active ? 'active' : 'inactive')) }}</x-dashboard.badge>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('app.users.edit', $user) }}" class="row-action">{{ __('users.edit') }}<span class="sr-only"> {{ $user->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-muted">{{ __('users.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    </x-dashboard.card>
</div>
