<div>
    <x-dashboard.page-header :title="__('whatsapp_manager.title')" :description="__('whatsapp_manager.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex justify-end">
            <x-dashboard.button :href="route('app.whatsapp.create')">{{ __('whatsapp_manager.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('whatsapp_manager.fields.name') }}</th>
                        <th scope="col">{{ __('whatsapp_manager.fields.number') }}</th>
                        <th scope="col">{{ __('whatsapp_manager.fields.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('whatsapp_manager.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agents as $agent)
                        <tr wire:key="agent-{{ $agent->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $agent->name }}">{{ $agent->name }}</span></td>
                            <td class="whitespace-nowrap text-muted" dir="ltr">+{{ $agent->number }}</td>
                            <td>
                                <x-dashboard.badge :tone="$agent->is_active ? 'active' : 'muted'">{{ $agent->is_active ? __('whatsapp_manager.status.active') : __('whatsapp_manager.status.inactive') }}</x-dashboard.badge>
                            </td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.whatsapp.edit', $agent) }}" class="row-action">{{ __('whatsapp_manager.edit') }}<span class="sr-only"> {{ $agent->name }}</span></a>
                                <button type="button" wire:click="toggle({{ $agent->id }})" class="row-action">{{ $agent->is_active ? __('whatsapp_manager.deactivate') : __('whatsapp_manager.activate') }}<span class="sr-only"> {{ $agent->name }}</span></button>
                                <button type="button" wire:click="move({{ $agent->id }}, 'up')" @disabled($loop->first) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('whatsapp_manager.move_up') }}<span class="sr-only"> {{ $agent->name }}</span></button>
                                <button type="button" wire:click="move({{ $agent->id }}, 'down')" @disabled($loop->last) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('whatsapp_manager.move_down') }}<span class="sr-only"> {{ $agent->name }}</span></button>
                                <button type="button" wire:click="delete({{ $agent->id }})" data-confirm="{{ __('whatsapp_manager.confirm_delete') }}" data-confirm-label="{{ __('whatsapp_manager.delete') }}" class="row-action row-action--danger">{{ __('whatsapp_manager.delete') }}<span class="sr-only"> {{ $agent->name }}</span></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-muted">{{ __('whatsapp_manager.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>
    </x-dashboard.card>
</div>
