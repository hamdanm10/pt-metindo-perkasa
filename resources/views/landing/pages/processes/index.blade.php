@extends('landing.layouts.app')

@section('title', __('landing.nav.processes'))

@section('content')
    <x-landing.page-header :title="__('pages.processes.title')" :intro="__('pages.processes.intro')" />

    <section class="bg-white">
        <div class="container-site section-y">
            <ul data-anim="rows">
                @foreach ($categories as $category)
                    <li class="border-t border-ink/20 last:border-b">
                        <a href="{{ route('landing.processes.show', ['category' => $category['slug']]) }}"
                           class="group flex items-center gap-5 py-6 sm:py-7">
                            <span class="grid flex-1 gap-1 sm:grid-cols-12 sm:items-baseline sm:gap-8">
                                <span class="text-2xl font-bold text-ink group-hover:text-brand-700 sm:col-span-6 sm:text-3xl" lang="en" dir="ltr">{{ $category['name'] }}</span>
                                <span class="text-lg text-muted sm:col-span-6">{{ __("pages.processes.categories.{$category['slug']}") }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <x-landing.cta />
@endsection
