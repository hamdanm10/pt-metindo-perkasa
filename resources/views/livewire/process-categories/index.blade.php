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
                        <th scope="col">{{ __('process_categories.fields.description') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('process_categories.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($processCategories as $processCategory)
                        <tr wire:key="process-category-{{ $processCategory->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $processCategory->name }}">{{ $processCategory->name }}</span></td>
                            <td class="text-muted"><span class="cell-truncate" title="{{ \Illuminate\Support\Str::limit((string) $processCategory->description, 300) }}">{{ \Illuminate\Support\Str::limit((string) $processCategory->description, 200) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('app.process-categories.edit', $processCategory) }}" class="row-action">{{ __('process_categories.edit') }}<span class="sr-only"> {{ $processCategory->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-muted">{{ __('process_categories.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $processCategories->links() }}</div>
    </x-dashboard.card>
</div>
