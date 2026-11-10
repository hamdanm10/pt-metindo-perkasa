@extends('landing.layouts.app')

@section('title', __('landing.nav.processes'))

@section('content')
    @php
        $tones = ['bg-ink', 'bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
        $steps = array_keys(__('pages.processes.organised.steps'));
    @endphp

    {{-- Opening: the headline, the intro, and how many categories there are. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(2.5rem,6vw,7.5rem)]">
            <h1 data-anim="hero-title" class="max-w-[12em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('pages.processes.title') }}</h1>
            <div class="mt-[3vw] grid items-end gap-8 max-lg:mt-8 lg:grid-cols-[1fr_auto]">
                <p data-anim="hero-item" class="max-w-[34em] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85">{{ __('pages.processes.intro') }}</p>
                <p data-anim="hero-item" class="flex items-end gap-[1vw] max-lg:gap-3">
                    <span data-count="{{ count($categories) }}" class="text-[clamp(3.5rem,7vw,9rem)] font-black leading-[0.85] text-brand-300">{{ count($categories) }}</span>
                    <span class="pb-[0.4vw] text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase max-lg:pb-1">{{ __('pages.processes.count') }}</span>
                </p>
            </div>
        </div>
    </section>

    {{-- The categories as cards. A card turns dark on hover, and a soft light follows the pointer. --}}
    <section class="bg-lavender text-ink">
        <div class="container-site section-y">
            <ul data-process-grid class="grid gap-[1.5vw] max-lg:gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('landing.processes.show', ['category' => $category['slug']]) }}" data-spot
                           class="process-card group relative flex h-full min-h-[clamp(15rem,17vw,22rem)] flex-col overflow-hidden rounded-lg border border-ink/15 bg-white p-[clamp(1.25rem,1.9vw,2.4rem)] transition-colors duration-500 hover:border-ink hover:bg-ink hover:text-white focus-visible:border-ink focus-visible:bg-ink focus-visible:text-white">
                            <span class="{{ $tones[$loop->index % count($tones)] }} absolute inset-x-0 top-0 h-1 origin-left scale-x-0 transition-transform duration-700 [transition-timing-function:var(--header-ease)] group-hover:scale-x-100 group-focus-visible:scale-x-100" aria-hidden="true"></span>
                            <span class="flex items-start justify-between">
                                <span class="text-[clamp(0.75rem,0.833vw,1.05rem)] font-extrabold text-brand-600 transition-colors duration-500 group-hover:text-brand-300" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="grid size-[clamp(2.25rem,2.6vw,3.25rem)] place-items-center rounded-full border border-current opacity-60 transition duration-500 group-hover:rotate-[-45deg] group-hover:bg-white group-hover:text-ink group-hover:opacity-100 rtl:-scale-x-100" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M3.5 10h12.5M11 4.5 16.5 10 11 15.5"/></svg>
                                </span>
                            </span>
                            <span class="mt-auto block pt-[3vw] max-lg:pt-8">
                                <span class="block text-[clamp(1.25rem,1.9vw,2.4rem)] font-black leading-[1.1] tracking-tight" lang="en" dir="ltr">{{ $category['name'] }}</span>
                                <span class="mt-[0.6vw] block text-[clamp(0.9rem,1.05vw,1.3rem)] leading-snug opacity-70 max-lg:mt-2">{{ __("pages.processes.categories.{$category['slug']}") }}</span>
                                <span class="mt-[1.1vw] flex flex-wrap gap-2 max-lg:mt-4" lang="en" dir="ltr">
                                    @foreach (array_slice($category['processes'], 0, 2) as $process)
                                        <span class="rounded-full border border-current/40 px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-semibold">{{ $process }}</span>
                                    @endforeach
                                </span>
                                <span class="mt-[1.4vw] inline-flex text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold max-lg:mt-5"><span class="uline">{{ __('pages.processes.view') }}</span></span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- How the catalogue is organised: four levels joined by a line that is drawn as the page scrolls. --}}
    <section class="bg-white text-ink">
        <div class="container-site section-y">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <h2 data-anim="grow" class="max-w-[10em] text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.processes.organised.title') }}</h2>
                <p data-anim="heading" class="max-w-[26em] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.45] text-ink/75">{{ __('pages.processes.organised.intro') }}</p>
            </div>

            <ol data-steps class="relative mt-[4vw] grid gap-10 max-lg:mt-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-[2vw]">
                <span data-steps-line class="pointer-events-none absolute start-0 top-[clamp(1.25rem,1.6vw,2rem)] hidden h-px w-full origin-left bg-ink/30 lg:block" aria-hidden="true"></span>
                @foreach ($steps as $step)
                    <li data-step class="relative">
                        <span class="relative grid size-[clamp(2.5rem,3.2vw,4rem)] place-items-center rounded-full bg-brand-600 text-[clamp(0.85rem,1vw,1.25rem)] font-black text-white">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-[1.6vw] text-[clamp(1.25rem,1.7vw,2.1rem)] font-black uppercase tracking-tight max-lg:mt-5">{{ __("pages.processes.organised.steps.{$step}.title") }}</h3>
                        <p class="mt-[0.6vw] text-[clamp(0.95rem,1.05vw,1.35rem)] leading-snug text-ink/75 max-lg:mt-2">{{ __("pages.processes.organised.steps.{$step}.text") }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <x-landing.cta />
@endsection
