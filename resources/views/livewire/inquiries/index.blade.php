<div>
    <x-dashboard.page-header :title="__('inquiries.title')" :description="$canCreate ? __('inquiries.description_staff') : __('inquiries.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid flex-1 gap-4 sm:grid-cols-2 {{ $area === 'app' ? 'xl:grid-cols-4' : 'lg:grid-cols-3' }}">
                <x-dashboard.field name="search" :label="__('inquiries.search')" type="search" wire:model.live.debounce.300ms="search" />
                @if ($area === 'app')
                    <x-dashboard.select name="customer" :label="__('inquiries.fields.customer')" wire:model.live="customer">
                        <option value="">{{ __('inquiries.all_customers') }}</option>
                        @foreach ($customers as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->email }})</option>
                        @endforeach
                    </x-dashboard.select>
                @endif
                <x-dashboard.select name="service" :label="__('inquiries.fields.service')" wire:model.live="service">
                    <option value="">{{ __('inquiries.all_services') }}</option>
                    @foreach ($services as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </x-dashboard.select>
                <x-dashboard.select name="product" :label="__('inquiries.fields.product')" wire:model.live="product">
                    <option value="">{{ __('inquiries.all_products') }}</option>
                    @foreach ($products as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </x-dashboard.select>
            </div>
            @if ($canCreate)
                <x-dashboard.button :href="route('app.inquiries.create')">{{ __('inquiries.create') }}</x-dashboard.button>
            @endif
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        @if ($area === 'app')
                            <th scope="col">{{ __('inquiries.fields.customer') }}</th>
                        @endif
                        <th scope="col">{{ __('inquiries.fields.question') }}</th>
                        <th scope="col">{{ __('inquiries.related') }}</th>
                        <th scope="col">{{ __('inquiries.fields.date') }}</th>
                        @if ($area === 'app')
                            <th scope="col">{{ __('inquiries.fields.created_by') }}</th>
                        @endif
                        <th scope="col"><span class="sr-only">{{ __('inquiries.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr wire:key="inquiry-{{ $inquiry->id }}" class="align-middle">
                            @if ($area === 'app')
                                <td class="font-semibold"><span class="cell-truncate" title="{{ $inquiry->customer->name }}">{{ $inquiry->customer->name }}</span></td>
                            @endif
                            <td>
                                <a href="{{ route($area.'.inquiries.show', $inquiry) }}" class="cell-truncate font-semibold hover:text-brand-700" title="{{ \Illuminate\Support\Str::limit($inquiry->question, 300) }}">{{ \Illuminate\Support\Str::limit($inquiry->question, 200) }}</a>
                                @if ($inquiry->images_count > 0)
                                    <p class="mt-1 text-xs text-muted">{{ trans_choice('inquiries.pictures_count', $inquiry->images_count) }}</p>
                                @endif
                            </td>
                            <td>
                                <ul class="flex gap-1.5">
                                    @if ($inquiry->service)
                                        <li><x-dashboard.badge tone="muted">{{ $inquiry->service->name }}</x-dashboard.badge></li>
                                    @endif
                                    @if ($inquiry->product)
                                        <li><x-dashboard.badge tone="muted">{{ $inquiry->product->name }}</x-dashboard.badge></li>
                                    @endif
                                    @if (! $inquiry->service && ! $inquiry->product)
                                        <li class="text-muted" aria-label="{{ __('inquiries.none') }}">&mdash;</li>
                                    @endif
                                </ul>
                            </td>
                            <td class="whitespace-nowrap text-muted">{{ $inquiry->asked_at?->isoFormat('LL') ?? '—' }}</td>
                            @if ($area === 'app')
                                <td class="text-muted"><span class="cell-truncate" title="{{ $inquiry->creator?->name ?? __('inquiries.unknown_author') }}">{{ $inquiry->creator?->name ?? __('inquiries.unknown_author') }}</span></td>
                            @endif
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route($area.'.inquiries.show', $inquiry) }}" class="row-action">{{ __('inquiries.view') }}<span class="sr-only"> {{ \Illuminate\Support\Str::limit($inquiry->question, 60) }}</span></a>
                                @can('update', $inquiry)
                                    <a href="{{ route('app.inquiries.edit', $inquiry) }}" class="row-action">{{ __('inquiries.edit') }}<span class="sr-only"> {{ \Illuminate\Support\Str::limit($inquiry->question, 60) }}</span></a>
                                @endcan
                                @can('delete', $inquiry)
                                    <button type="button" wire:click="delete({{ $inquiry->id }})" data-confirm="{{ __('inquiries.confirm_delete') }}" data-confirm-label="{{ __('inquiries.delete') }}" class="row-action row-action--danger">{{ __('inquiries.delete') }}<span class="sr-only"> {{ \Illuminate\Support\Str::limit($inquiry->question, 60) }}</span></button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $area === 'app' ? 6 : 4 }}" class="py-12 text-center text-muted">{{ $search !== '' || $customer !== '' || $service !== '' || $product !== '' ? __('inquiries.empty_filtered') : __('inquiries.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $inquiries->links() }}</div>
    </x-dashboard.card>
</div>
