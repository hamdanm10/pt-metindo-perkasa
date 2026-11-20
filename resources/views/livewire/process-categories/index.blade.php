<div>
    <x-dashboard.page-header :title="__('process_categories.title')" :description="__('process_categories.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex-1 sm:max-w-sm">
                <x-dashboard.field name="search" :label="__('process_categories.search')" type="search" wire:model.live.debounce.300ms="search" />
            </div>
            <x-dashboard.button :href="route('app.process-categories.create')">{{ __('process_categories.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('process_categories.fields.name') }}</th>
                        <th scope="col">{{ __('process_categories.fields.slug') }}</th>
                        <th scope="col">{{ __('process_categories.fields.languages') }}</th>
                        <th scope="col">{{ __('process_categories.processes') }}</th>
                        <th scope="col">{{ __('process_categories.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('process_categories.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($processCategories as $processCategory)
                        <tr wire:key="process-category-{{ $processCategory->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $processCategory->name }}">{{ $processCategory->name }}</span></td>
                            <td class="text-muted"><span class="cell-truncate" title="{{ $processCategory->slug }}">{{ $processCategory->slug }}</span></td>
                            @php($filled = $processCategory->translations->pluck('locale'))
                            <td>
                                <ul class="flex gap-1.5">
                                    @foreach (config('locales.landing') as $code)
                                        <li>
                                            <x-dashboard.badge :tone="$filled->contains($code) ? 'active' : 'muted'">
                                                <span class="uppercase" aria-hidden="true">{{ $code }}</span>
                                                <span class="sr-only">{{ config("locales.locales.{$code}.name") }}:</span>
                                                <span class="sr-only">{{ __($filled->contains($code) ? 'process_categories.language_filled' : 'process_categories.language_empty') }}</span>
                                            </x-dashboard.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-muted">{{ $processCategory->processes_count }}</td>
                            <td>{{ $processCategory->is_active ? __('process_categories.active') : __('process_categories.inactive') }}</td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.process-categories.edit', $processCategory) }}" class="row-action">{{ __('process_categories.edit') }}<span class="sr-only"> {{ $processCategory->name }}</span></a>
                                <button type="button" wire:click="move({{ $processCategory->id }}, 'up')" @disabled($processCategory->id === $ends['first']) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('process_categories.move_up') }}<span class="sr-only"> {{ $processCategory->name }}</span></button>
                                <button type="button" wire:click="move({{ $processCategory->id }}, 'down')" @disabled($processCategory->id === $ends['last']) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('process_categories.move_down') }}<span class="sr-only"> {{ $processCategory->name }}</span></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-muted">{{ __('process_categories.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $processCategories->links() }}</div>
    </x-dashboard.card>
</div>
