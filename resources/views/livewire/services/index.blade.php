<div>
    <x-dashboard.page-header :title="__('services.title')" :description="__('services.description')" />

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
                        <th scope="col">{{ __('services.fields.slug') }}</th>
                        <th scope="col">{{ __('services.fields.languages') }}</th>
                        <th scope="col">{{ __('services.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('services.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr wire:key="service-{{ $service->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $service->name }}">{{ $service->name }}</span></td>
                            <td class="text-muted"><span class="cell-truncate" title="{{ $service->slug }}">{{ $service->slug ?? '—' }}</span></td>
                            @php($filled = $service->translations->pluck('locale'))
                            <td>
                                <ul class="flex gap-1.5">
                                    @foreach (config('locales.landing') as $code)
                                        <li>
                                            <x-dashboard.badge :tone="$filled->contains($code) ? 'active' : 'muted'">
                                                <span class="uppercase" aria-hidden="true">{{ $code }}</span>
                                                <span class="sr-only">{{ config("locales.locales.{$code}.name") }}:</span>
                                                <span class="sr-only">{{ __($filled->contains($code) ? 'services.language_filled' : 'services.language_empty') }}</span>
                                            </x-dashboard.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $service->is_active ? __('services.active') : __('services.inactive') }}</td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.services.edit', $service) }}" class="row-action">{{ __('services.edit') }}<span class="sr-only"> {{ $service->name }}</span></a>
                                <button type="button" wire:click="move({{ $service->id }}, 'up')" @disabled($service->id === $ends['first']) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('services.move_up') }}<span class="sr-only"> {{ $service->name }}</span></button>
                                <button type="button" wire:click="move({{ $service->id }}, 'down')" @disabled($service->id === $ends['last']) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('services.move_down') }}<span class="sr-only"> {{ $service->name }}</span></button>
                                <button type="button" wire:click="delete({{ $service->id }})" data-confirm="{{ __('services.confirm_delete') }}" data-confirm-label="{{ __('services.delete') }}" class="row-action row-action--danger">{{ __('services.delete') }}<span class="sr-only"> {{ $service->name }}</span></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-muted">{{ __('services.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $services->links() }}</div>
    </x-dashboard.card>
</div>
