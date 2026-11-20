<div>
    <x-dashboard.page-header :title="__('materials.title')" :description="__('materials.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex-1 sm:max-w-sm">
                <x-dashboard.field name="search" :label="__('materials.search')" type="search" wire:model.live.debounce.300ms="search" />
            </div>
            <x-dashboard.button :href="route('app.materials.create')">{{ __('materials.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('materials.fields.name') }}</th>
                        <th scope="col">{{ __('materials.fields.slug') }}</th>
                        <th scope="col">{{ __('materials.fields.languages') }}</th>
                        <th scope="col">{{ __('materials.status') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('materials.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materials as $material)
                        <tr wire:key="material-{{ $material->id }}" class="align-middle">
                            <td class="font-semibold"><span class="cell-truncate" title="{{ $material->name }}">{{ $material->name }}</span></td>
                            <td class="text-muted"><span class="cell-truncate" title="{{ $material->slug }}">{{ $material->slug }}</span></td>
                            @php($filled = $material->translations->pluck('locale'))
                            <td>
                                <ul class="flex gap-1.5">
                                    @foreach (config('locales.landing') as $code)
                                        <li>
                                            <x-dashboard.badge :tone="$filled->contains($code) ? 'active' : 'muted'">
                                                <span class="uppercase" aria-hidden="true">{{ $code }}</span>
                                                <span class="sr-only">{{ config("locales.locales.{$code}.name") }}:</span>
                                                <span class="sr-only">{{ __($filled->contains($code) ? 'materials.language_filled' : 'materials.language_empty') }}</span>
                                            </x-dashboard.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $material->is_active ? __('materials.active') : __('materials.inactive') }}</td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.materials.edit', $material) }}" class="row-action">{{ __('materials.edit') }}<span class="sr-only"> {{ $material->name }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-muted">{{ __('materials.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $materials->links() }}</div>
    </x-dashboard.card>
</div>
