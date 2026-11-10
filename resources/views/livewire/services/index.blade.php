<div>
    <x-dashboard.page-header :title="__('services.title')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex-1 sm:max-w-sm">
                <x-dashboard.field name="search" :label="__('services.search')" type="search" wire:model.live.debounce.300ms="search" />
            </div>
            <x-dashboard.button :href="route('app.services.create')">{{ __('services.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('services.fields.name') }}</th>
                        <th scope="col">{{ __('services.fields.description') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('services.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr wire:key="service-{{ $service->id }}" class="align-top">
                            <td class="font-semibold">{{ $service->name }}</td>
                            <td class="text-muted">{{ \Illuminate\Support\Str::limit((string) $service->description, 120) }}</td>
                            <td class="text-end">
                                <a href="{{ route('app.services.edit', $service) }}" class="row-action">{{ __('services.edit') }}<span class="sr-only"> {{ $service->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-muted">{{ __('services.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $services->links() }}</div>
    </x-dashboard.card>
</div>
