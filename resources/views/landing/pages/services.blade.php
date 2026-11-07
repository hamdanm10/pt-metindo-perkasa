@extends('landing.layouts.app')

@section('title', __('landing.nav.services'))

@php
    $services = ['electroplating', 'chemical_supply', 'consultation'];
    $steps = ['enquiry', 'spec_review', 'process_supply', 'delivery'];
@endphp

@section('content')
    <x-landing.page-header :title="__('pages.services.title')" :intro="__('pages.services.intro')" />

    <section class="bg-white">
        <div data-anim="rows" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            @foreach ($services as $service)
                <div class="grid gap-4 border-t border-ink/20 py-10 lg:grid-cols-12 lg:gap-16 {{ $loop->last ? 'border-b' : '' }}">
                    <h2 data-anim="heading" class="text-3xl font-bold text-ink sm:text-4xl lg:col-span-5">{{ __("home.services.items.{$service}.title") }}</h2>
                    <p class="text-xl text-muted lg:col-span-7">{{ __("home.services.items.{$service}.text") }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-ink text-white">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <h2 data-anim="heading" class="text-3xl font-bold sm:text-5xl">{{ __('pages.services.how.title') }}</h2>
            <p class="mt-4 max-w-xl text-lg text-white/80">{{ __('pages.services.how.intro') }}</p>

            <ol data-anim="steps" class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                @foreach ($steps as $step)
                    <li class="border-t-2 border-white pt-5">
                        <span data-step-number class="inline-block text-4xl font-extrabold text-brand-300" aria-hidden="true">{{ $loop->iteration }}</span>
                        <h3 class="mt-3 text-xl font-bold">{{ __("pages.services.how.steps.{$step}.title") }}</h3>
                        <p class="mt-2 text-white/80">{{ __("pages.services.how.steps.{$step}.text") }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <x-landing.cta />
@endsection
