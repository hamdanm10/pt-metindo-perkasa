@extends('landing.layouts.app')

@section('title', $category['name'])

@section('content')
    <x-landing.page-header :title="$category['name']" :intro="__('pages.products.categories.'.$category['slug'])">
        <nav aria-label="{{ __('pages.products.title') }}" class="mb-6">
            <a href="{{ route('landing.products.index') }}" class="inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-4">
                {{ __('pages.products.title') }}
            </a>
        </nav>
    </x-landing.page-header>

    <section class="bg-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:px-8 lg:py-24">
            <h2 class="text-3xl font-bold text-ink lg:col-span-4">{{ __('pages.products.processes') }}</h2>
            <ul class="lg:col-span-8">
                @foreach ($category['processes'] as $process)
                    <li class="border-t border-ink/20 py-5 text-2xl font-semibold text-ink last:border-b" lang="en" dir="ltr">{{ $process }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="bg-lavender">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <h2 class="text-2xl font-bold text-ink">{{ __('pages.products.others') }}</h2>
            <ul class="mt-6 flex flex-wrap gap-3">
                @foreach ($others as $other)
                    <li>
                        <a href="{{ route('landing.products.show', ['category' => $other['slug']]) }}"
                           class="inline-flex min-h-11 items-center rounded-lg border border-ink/40 bg-white px-4 font-semibold text-ink hover:border-brand-700 hover:text-brand-700" lang="en" dir="ltr">
                            {{ $other['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <x-landing.cta />
@endsection
