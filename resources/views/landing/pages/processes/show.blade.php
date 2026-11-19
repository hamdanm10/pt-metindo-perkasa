@extends('landing.layouts.app')

@section('title', $category['name'])

@section('content')
    <x-landing.page-header :title="$category['name']" :intro="__('pages.processes.categories.'.$category['slug'])">
        <nav aria-label="{{ __('pages.processes.title') }}" class="mb-6">
            <a href="{{ route('landing.processes.index') }}" class="inline-flex min-h-11 items-center font-semibold text-white underline underline-offset-4">
                {{ __('pages.processes.title') }}
            </a>
        </nav>
    </x-landing.page-header>

    <section class="bg-white">
        <div class="container-site grid gap-10 lg:grid-cols-12 lg:gap-16 section-y">
            <h2 data-anim="heading" class="text-3xl font-black tracking-tight text-ink lg:col-span-4">{{ __('pages.processes.list') }}</h2>
            <ul data-anim="rows" class="lg:col-span-8">
                @foreach ($processes as $process)
                    <li class="border-t border-ink/20 last:border-b">
                        <a href="{{ route('landing.process.show', ['process' => $process['slug']]) }}" class="group flex items-baseline gap-5 py-5 hover:text-brand-700" lang="en" dir="ltr">
                            <span class="w-[2ch] text-sm font-extrabold tabular-nums text-ink/40 group-hover:text-brand-600">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="flex-1">
                                <span class="block text-2xl font-semibold text-ink group-hover:text-brand-700">{{ $process['name'] }}</span>
                                <span class="mt-1 block text-base text-ink/70">{{ $process['description'] }}</span>
                            </span>
                            <span class="shrink-0 text-sm font-bold">{{ __('pages.process.view') }} →</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="bg-lavender">
        <div class="container-site lg: section-y">
            <h2 data-anim="heading" class="text-2xl font-black tracking-tight text-ink">{{ __('pages.processes.others') }}</h2>
            <ul data-anim="rows" class="mt-6 flex flex-wrap gap-3">
                @foreach ($others as $other)
                    <li>
                        <a href="{{ route('landing.processes.show', ['category' => $other['slug']]) }}"
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
