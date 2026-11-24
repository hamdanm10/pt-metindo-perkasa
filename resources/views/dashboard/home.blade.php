@php($user = auth()->user())

<x-dashboard.layout :title="__('dashboard.home.title')">
    <x-dashboard.page-header :title="__('dashboard.home.greeting', ['name' => $user?->name])" :description="__('dashboard.home.description')">
        <p class="mt-3 inline-block rounded-full border border-ink/10 bg-white px-3 py-1 text-xs font-bold uppercase tracking-wide">{{ __('dashboard.roles.'.$user?->role->value) }}</p>
    </x-dashboard.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($stats as $stat)
            <a href="{{ route($stat['route']) }}" class="group flex items-center gap-4 rounded-lg border border-ink/10 bg-white p-5 transition hover:border-brand-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600">
                <span class="grid size-12 shrink-0 place-items-center rounded-lg bg-lavender text-brand-600"><x-dashboard.icon :name="$stat['icon']" class="size-6" /></span>
                <span class="min-w-0">
                    <span class="block text-3xl font-black leading-none tracking-tight">{{ number_format($stat['value']) }}</span>
                    <span class="mt-1 block truncate text-sm text-muted">{{ __($stat['label']) }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        @foreach ($recent as $section)
            <x-dashboard.card class="!p-6">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="text-lg font-black uppercase tracking-tight">{{ __($section['title']) }}</h2>
                    <a href="{{ route($section['route']) }}" class="text-sm font-bold text-brand-600 hover:underline">{{ __('dashboard.home.view_all') }}</a>
                </div>

                @if ($section['rows'] === [])
                    <p class="mt-4 text-muted">{{ __('dashboard.home.empty_text') }}</p>
                @else
                    <ul class="mt-4 divide-y divide-ink/10">
                        @foreach ($section['rows'] as $row)
                            <li>
                                <a href="{{ $row['url'] }}" class="block py-3 hover:text-brand-600">
                                    <span class="block truncate font-bold">{{ $row['title'] }}</span>
                                    <span class="block truncate text-sm text-muted">{{ $row['meta'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-dashboard.card>
        @endforeach
    </div>
</x-dashboard.layout>
