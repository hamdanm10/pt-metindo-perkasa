<x-dashboard.layout :title="__('dashboard.home.title')">
    <x-dashboard.page-header :title="__('dashboard.home.greeting', ['name' => auth()->user()?->name])" />

    <x-dashboard.card class="max-w-2xl">
        <h2 class="text-xl font-bold">{{ __('dashboard.home.empty_title') }}</h2>
        <p class="mt-2 text-muted">{{ __('dashboard.home.empty_text') }}</p>
    </x-dashboard.card>
</x-dashboard.layout>
