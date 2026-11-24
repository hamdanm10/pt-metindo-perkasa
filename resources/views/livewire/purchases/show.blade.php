<div>
    <x-dashboard.page-header :title="__('purchases.show_title')" :description="__('purchases.purchased_on', ['date' => $purchase->purchased_at->isoFormat('LL')])" />

    <x-dashboard.flash />

    <x-dashboard.card class="max-w-3xl">
        <dl class="grid gap-4 sm:grid-cols-2">
            @if ($staffArea)
                <div>
                    <dt class="field-label">{{ __('purchases.kinds.'.$kind.'.buyer') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $purchase->buyer->name }}</dd>
                </div>
            @endif
            <div>
                <dt class="field-label">{{ __('purchases.fields.invoice_number') }}</dt>
                <dd class="mt-1">{{ $purchase->invoice_number ?? '—' }}</dd>
            </div>
        </dl>

        @if ($purchase->notes)
            <div class="mt-6">
                <h3 class="field-label">{{ __('purchases.fields.notes') }}</h3>
                <p class="mt-1 whitespace-pre-line">{{ $purchase->notes }}</p>
            </div>
        @endif

        <div class="mt-8 border-t border-ink/10 pt-6">
            <h3 class="field-label">{{ __('purchases.fields.products') }}</h3>
            <x-dashboard.table class="mt-3">
                <thead>
                    <tr>
                        <th scope="col">{{ __('purchases.fields.product') }}</th>
                        <th scope="col" class="text-end">{{ __('purchases.fields.quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr wire:key="item-{{ $item->id }}">
                            <td class="font-semibold">{{ $item->product_name }}</td>
                            <td class="text-end">{{ number_format($item->quantity) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-8 border-t border-ink/10 pt-6">
            <h3 class="field-label">{{ __('purchases.fields.files') }}</h3>
            @if ($purchase->files->isEmpty())
                <p class="mt-2 text-muted">{{ __('purchases.no_files') }}</p>
            @else
                <ul class="mt-3 divide-y divide-ink/10 rounded-lg border border-ink/10">
                    @foreach ($purchase->files as $file)
                        <li wire:key="file-{{ $file->id }}" class="flex items-center justify-between gap-4 p-3">
                            <span class="min-w-0">
                                <span class="block truncate font-semibold" title="{{ $file->name }}">{{ $file->name }}</span>
                                <span class="text-xs text-muted">{{ \Illuminate\Support\Number::fileSize($file->size) }}</span>
                            </span>
                            <a href="{{ route($area.'.purchase-files.show', $file) }}" class="row-action shrink-0">{{ __('purchases.download') }}<span class="sr-only"> {{ $file->name }}</span></a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if ($staffArea)
            <p class="mt-8 text-sm text-muted">{{ __('purchases.added_by', ['name' => $purchase->creator?->name ?? __('purchases.unknown_author')]) }}</p>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            <x-dashboard.button variant="secondary" :href="route($routes.'.index')">{{ __('purchases.back') }}</x-dashboard.button>
            @if ($canUpdate)
                <x-dashboard.button :href="route($routes.'.edit', $purchase)">{{ __('purchases.edit') }}</x-dashboard.button>
            @endif
        </div>
    </x-dashboard.card>
</div>
