@extends('landing.layouts.app')

@section('title', __('landing.nav.about'))

@section('content')
    <x-landing.page-header :title="__('pages.about.title')" :intro="__('pages.about.intro')" />

    <section class="bg-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:px-8 lg:py-28">
            <h2 data-anim="heading" class="text-balance text-3xl font-bold leading-tight text-ink sm:text-4xl lg:col-span-5">
                {{ __('pages.about.approach.title') }}
            </h2>
            <div class="space-y-5 text-lg text-muted lg:col-span-7">
                <p>{{ __('pages.about.approach.first') }}</p>
                <p>{{ __('pages.about.approach.second') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-white pb-20 lg:pb-28">
        <div class="mx-auto grid max-w-6xl gap-5 px-4 sm:px-6 lg:grid-cols-5 lg:px-8">
            <div data-anim="card" data-from="start" class="rounded-3xl bg-brand-600 p-8 text-white sm:p-12 lg:col-span-3 lg:rounded-ss-[5rem]">
                <h2 data-anim="heading" class="text-3xl font-bold">{{ __('pages.about.vision.title') }}</h2>
                <p class="mt-5 max-w-md text-xl text-white">{{ __('pages.about.vision.text') }}</p>
            </div>
            <div data-anim="card" data-from="end" class="rounded-3xl bg-ink p-8 text-white sm:p-12 lg:col-span-2">
                <h2 data-anim="heading" class="text-3xl font-bold">{{ __('pages.about.mission.title') }}</h2>
                <p class="mt-5 text-xl text-white">{{ __('pages.about.mission.text') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-lavender">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:px-8 lg:py-24">
            <h2 data-anim="heading" class="text-3xl font-bold text-ink lg:col-span-5">{{ __('pages.about.facilities.title') }}</h2>
            <p class="text-lg text-muted lg:col-span-7">{{ __('pages.about.facilities.text') }}</p>
        </div>
    </section>

    @if ($subsidiaries)
        <section class="bg-white">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
                <div class="max-w-2xl">
                    <h2 data-anim="heading" class="text-3xl font-bold text-ink sm:text-4xl">{{ __('pages.about.subsidiaries.title') }}</h2>
                    <p class="mt-4 text-lg text-muted">{{ __('pages.about.subsidiaries.intro') }}</p>
                    <p class="mt-4 inline-block rounded-lg border border-ink/30 px-4 py-2 text-sm font-semibold text-ink">{{ __('landing.sample_note') }}</p>
                </div>

                <ul data-anim="rows" class="mt-12 grid gap-x-12 sm:grid-cols-2">
                    @foreach ($subsidiaries as $subsidiary)
                        <li class="border-t border-ink/20 py-6">
                            <h3 class="text-xl font-bold text-ink" lang="en" dir="ltr">{{ $subsidiary['name'] }}</h3>
                            <p class="mt-1 text-muted">{{ __("pages.about.subsidiaries.items.{$subsidiary['key']}") }}</p>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $subsidiary['lat'] }},{{ $subsidiary['lng'] }}"
                               target="_blank" rel="noopener noreferrer"
                               class="mt-3 inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-4">
                                {{ __('pages.about.subsidiaries.open_map') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-landing.cta />
@endsection
