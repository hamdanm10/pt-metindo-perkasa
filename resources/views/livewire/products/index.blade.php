<div>
    <x-dashboard.page-header :title="__('products.title')" />

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
                    <tr class="border-b border-ink/20 text-start text-sm text-muted">
                        <th scope="col" class="py-3 pe-4 text-start font-semibold">{{ __('products.fields.name') }}</th>
                        <th scope="col" class="py-3 pe-4 text-start font-semibold">{{ __('products.fields.description') }}</th>
                        <th scope="col" class="py-3 text-start font-semibold"><span class="sr-only">{{ __('products.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="border-b border-ink/10 align-top">
                            <td class="py-3 pe-4 font-semibold">{{ $product->name }}</td>
                            <td class="py-3 pe-4 text-muted">{{ \Illuminate\Support\Str::limit((string) $product->description, 120) }}</td>
                            <td class="py-3 text-end">
                                <a href="{{ route('app.products.edit', $product) }}" class="inline-flex min-h-11 items-center font-bold text-brand-700 underline underline-offset-4">{{ __('products.edit') }}<span class="sr-only"> {{ $product->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-muted">{{ __('products.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
    </x-dashboard.card>
</div>
