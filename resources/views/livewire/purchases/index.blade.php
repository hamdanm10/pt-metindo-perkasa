<div>
    <x-dashboard.page-header :title="$kind === '' ? __('purchases.title') : __('purchases.kinds.'.$kind.'.title')" :description="$kind === '' ? __('purchases.description') : __('purchases.kinds.'.$kind.'.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid flex-1 gap-4 sm:grid-cols-2">
                <x-dashboard.field name="search" :label="$area === 'app' ? __('purchases.search') : __('purchases.search_own')" type="search" wire:model.live.debounce.300ms="search" />
                @if ($area === 'app')
                    <x-dashboard.lookup-field name="buyer" :label="__('purchases.kinds.'.$kind.'.buyer')">
                        <livewire:lookup :source="$kind" wire:model.live="buyer" input-id="buyer" :placeholder="__('purchases.kinds.'.$kind.'.all_buyers')" :empty-label="__('purchases.kinds.'.$kind.'.all_buyers')" :invalid="false" />
                    </x-dashboard.lookup-field>
                @endif
            </div>
            @if ($canCreate)
                <x-dashboard.button :href="route($routes.'.create')">{{ __('purchases.create') }}</x-dashboard.button>
            @endif
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        @if ($area === 'app')
                            <th scope="col">{{ __('purchases.kinds.'.$kind.'.buyer') }}</th>
                        @endif
                        <th scope="col">{{ __('purchases.fields.invoice_number') }}</th>
                        <th scope="col">{{ __('purchases.fields.purchased_at') }}</th>
                        <th scope="col">{{ __('purchases.contents') }}</th>
                        @if ($area === 'app')
                            <th scope="col">{{ __('purchases.fields.created_by') }}</th>
                        @endif
                        <th scope="col"><span class="sr-only">{{ __('purchases.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr wire:key="purchase-{{ $purchase->id }}" class="align-middle">
                            @if ($area === 'app')
                                <td>
                                    <a href="{{ route($routes.'.show', $purchase) }}" class="cell-truncate font-semibold hover:text-brand-700" title="{{ $purchase->buyer->name }}">{{ $purchase->buyer->name }}</a>
                                </td>
                            @endif
                            <td class="text-muted"><span class="cell-truncate" title="{{ $purchase->invoice_number }}">{{ $purchase->invoice_number ?? '—' }}</span></td>
                            <td class="whitespace-nowrap text-muted">{{ $purchase->purchased_at->isoFormat('LL') }}</td>
                            <td class="text-muted">
                                {{ trans_choice('purchases.products_count', $purchase->items_count) }}
                                @if ($purchase->files_count > 0)
                                    <span class="block text-xs">{{ trans_choice('purchases.files_count', $purchase->files_count) }}</span>
                                @endif
                            </td>
                            @if ($area === 'app')
                                <td class="text-muted"><span class="cell-truncate" title="{{ $purchase->creator?->name ?? __('purchases.unknown_author') }}">{{ $purchase->creator?->name ?? __('purchases.unknown_author') }}</span></td>
                            @endif
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route($routes.'.show', $purchase) }}" class="row-action">{{ __('purchases.view') }}<span class="sr-only"> {{ $purchase->invoice_number ?? $purchase->purchased_at->isoFormat('LL') }}</span></a>
                                @can('update', $purchase)
                                    <a href="{{ route($routes.'.edit', $purchase) }}" class="row-action">{{ __('purchases.edit') }}<span class="sr-only"> {{ $purchase->invoice_number ?? $purchase->purchased_at->isoFormat('LL') }}</span></a>
                                @endcan
                                @can('delete', $purchase)
                                    <button type="button" wire:click="delete({{ $purchase->id }})" data-confirm="{{ __('purchases.confirm_delete') }}" data-confirm-label="{{ __('purchases.delete') }}" class="row-action row-action--danger">{{ __('purchases.delete') }}<span class="sr-only"> {{ $purchase->invoice_number ?? $purchase->purchased_at->isoFormat('LL') }}</span></button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $area === 'app' ? 6 : 4 }}" class="py-12 text-center text-muted">{{ $search !== '' || $buyer !== '' ? __('purchases.empty_filtered') : __('purchases.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $purchases->links() }}</div>
    </x-dashboard.card>
</div>
