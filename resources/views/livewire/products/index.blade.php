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
                        <th scope="col">{{ __('products.fields.description') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('products.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="align-middle">
                            <td class="font-semibold">{{ $product->name }}</td>
                            <td class="text-muted">{{ \Illuminate\Support\Str::limit((string) $product->description, 120) }}</td>
                            <td class="text-end">
                                <a href="{{ route('app.products.edit', $product) }}" class="row-action">{{ __('products.edit') }}<span class="sr-only"> {{ $product->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-muted">{{ __('products.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
    </x-dashboard.card>
</div>
