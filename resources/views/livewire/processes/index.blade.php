<div>
    <x-dashboard.page-header :title="__('processes.title')" :description="__('processes.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="grid flex-1 gap-4 sm:max-w-2xl sm:grid-cols-2">
                <x-dashboard.field name="search" :label="__('processes.search')" type="search" wire:model.live.debounce.300ms="search" />
                <x-dashboard.lookup-field name="category" :label="__('processes.fields.category')">
                    <livewire:lookup source="process_category" wire:model.live="category" input-id="category" :placeholder="__('processes.all_categories')" :empty-label="__('processes.all_categories')" :invalid="false" />
                </x-dashboard.lookup-field>
            </div>
            <x-dashboard.button :href="route('app.processes.create')">{{ __('processes.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('processes.fields.name') }}</th>
                        <th scope="col">{{ __('processes.fields.category') }}</th>
                        <th scope="col">{{ __('processes.fields.languages') }}</th>
                        <th scope="col">{{ __('processes.fields.steps') }}</th>
                        <th scope="col">{{ __('processes.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('processes.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($processes as $process)
                        <tr wire:key="process-{{ $process->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $process->name }}">{{ $process->name }}</span></td>
                            <td class="text-muted"><span class="cell-truncate" title="{{ $process->processCategory->name }}">{{ $process->processCategory->name }}</span></td>
                            @php($filled = $process->translations->pluck('locale'))
                            <td>
                                <ul class="flex gap-1.5">
                                    @foreach (config('locales.landing') as $code)
                                        <li>
                                            <x-dashboard.badge :tone="$filled->contains($code) ? 'active' : 'muted'">
                                                <span class="uppercase" aria-hidden="true">{{ $code }}</span>
                                                <span class="sr-only">{{ config("locales.locales.{$code}.name") }}:</span>
                                                <span class="sr-only">{{ __($filled->contains($code) ? 'processes.language_filled' : 'processes.language_empty') }}</span>
                                            </x-dashboard.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-muted">{{ $process->steps_count }}</td>
                            <td>{{ $process->is_active ? __('processes.active') : __('processes.inactive') }}</td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.processes.edit', $process) }}" class="row-action">{{ __('processes.edit') }}<span class="sr-only"> {{ $process->name }}</span></a>
                                <button type="button" wire:click="move({{ $process->id }}, 'up')" class="row-action">{{ __('processes.move_up') }}<span class="sr-only"> {{ $process->name }}</span></button>
                                <button type="button" wire:click="move({{ $process->id }}, 'down')" class="row-action">{{ __('processes.move_down') }}<span class="sr-only"> {{ $process->name }}</span></button>
                                <button type="button" wire:click="delete({{ $process->id }})" data-confirm="{{ __('processes.confirm_delete') }}" data-confirm-label="{{ __('processes.delete') }}" class="row-action row-action--danger">{{ __('processes.delete') }}<span class="sr-only"> {{ $process->name }}</span></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-muted">{{ $search !== '' || $category !== '' ? __('processes.empty_filtered') : __('processes.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $processes->links() }}</div>
    </x-dashboard.card>
</div>
