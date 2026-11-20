<div>
    <x-dashboard.page-header :title="__('products.title')" :description="__('products.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex-1 sm:max-w-sm">
                <x-dashboard.field name="search" :label="__('products.search')" type="search" wire:model.live.debounce.300ms="search" />
            </div>
            <x-dashboard.button :href="route('app.products.create')">{{ __('products.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('products.fields.name') }}</th>
                        <th scope="col">{{ __('products.fields.slug') }}</th>
                        <th scope="col">{{ __('products.fields.languages') }}</th>
                        <th scope="col">{{ __('products.used_in_steps') }}</th>
                        <th scope="col">{{ __('products.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('products.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $product->name }}">{{ $product->name }}</span></td>
                            <td class="text-muted"><span class="cell-truncate" title="{{ $product->slug }}">{{ $product->slug }}</span></td>
                            @php($filled = $product->translations->pluck('locale'))
                            <td>
                                <ul class="flex gap-1.5">
                                    @foreach (config('locales.landing') as $code)
                                        <li>
                                            <x-dashboard.badge :tone="$filled->contains($code) ? 'active' : 'muted'">
                                                <span class="uppercase" aria-hidden="true">{{ $code }}</span>
                                                <span class="sr-only">{{ config("locales.locales.{$code}.name") }}:</span>
                                                <span class="sr-only">{{ __($filled->contains($code) ? 'products.language_filled' : 'products.language_empty') }}</span>
                                            </x-dashboard.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-muted">{{ $product->steps_count }}</td>
                            <td>{{ $product->is_active ? __('products.active') : __('products.inactive') }}</td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.products.edit', $product) }}" class="row-action">{{ __('products.edit') }}<span class="sr-only"> {{ $product->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-muted">{{ __('products.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
    </x-dashboard.card>
</div>
