@extends('landing.layouts.app')

@section('title', $category['name'])

@section('content')
    @php
        $tones = ['bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
        $tone = $tones[($position - 1) % count($tones)];
        $number = str_pad((string) $position, 2, '0', STR_PAD_LEFT);
    @endphp

    {{-- Opening: breadcrumb, the category name, its text, and how many processes it holds. --}}
    <section class="relative overflow-hidden bg-ink text-white" style="--focus-ring: #ffffff">
        <span class="pointer-events-none absolute -bottom-[0.18em] end-[2vw] select-none text-[clamp(10rem,32vw,36rem)] font-black leading-none text-white/[0.04]" aria-hidden="true">{{ $number }}</span>
        <div class="container-site relative pb-[clamp(3rem,6.67vw,8rem)] pt-10 sm:pt-14 lg:pt-20">
            <nav aria-label="{{ __('pages.processes.title') }}" class="mb-6 flex flex-wrap items-center gap-x-2 text-white">
                <a href="{{ route('landing.processes.index') }}" class="inline-flex min-h-11 items-center font-semibold underline underline-offset-4">{{ __('landing.nav.processes') }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page" lang="en" dir="ltr">{{ $category['name'] }}</span>
            </nav>
            <p data-anim="hero-item" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-brand-300">{{ __('pages.processes.position', ['number' => $number, 'total' => $total]) }}</p>
            <h1 data-anim="hero-title" class="mt-3 max-w-[12em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight" lang="en" dir="ltr">{{ $category['name'] }}</h1>
            <div class="mt-[3vw] grid items-end gap-8 max-lg:mt-8 lg:grid-cols-[1fr_auto]">
                <p data-anim="hero-item" class="max-w-[34em] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85">{{ $category['description'] }}</p>
                <p data-anim="hero-item" class="flex items-end gap-[1vw] max-lg:gap-3">
                    <span class="text-[clamp(3.5rem,7vw,9rem)] font-black leading-[0.85] text-brand-300">{{ count($processes) }}</span>
                    <span class="pb-[0.4vw] text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase max-lg:pb-1">{{ __('pages.processes.list') }}</span>
                </p>
            </div>
        </div>
    </section>

    {{-- The processes: a sticky picture on one side and a grid of numbered cards on the other. --}}
    <section class="bg-lavender text-ink">
        <div class="container-site section-y grid items-start gap-10 lg:grid-cols-[4fr_8fr] lg:gap-[2.5vw]">
            <div class="{{ $tone }} relative aspect-[4/3] overflow-hidden rounded-lg lg:sticky lg:top-[calc(var(--header-h)+1.5rem)]" aria-hidden="true">
                <x-landing.photo :name="'product-'.$category['slug']" ratio="aspect-[4/3]" bare class="absolute inset-0 size-full bg-transparent! text-white!" />
            </div>

            <ul data-anim="rows" class="grid gap-4 sm:grid-cols-2">
                @foreach ($processes as $process)
                    <li>
                        <a href="{{ route('landing.process.show', ['process' => $process['slug']]) }}" class="group flex h-full flex-col rounded-lg border border-ink/15 bg-white p-[clamp(1.25rem,1.8vw,2.25rem)] transition duration-300 hover:-translate-y-1 hover:border-brand-600 hover:shadow-lg" lang="en" dir="ltr">
                            <span class="text-sm font-extrabold tabular-nums text-brand-600">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="mt-3 block text-[clamp(1.25rem,1.6vw,2rem)] font-black leading-tight tracking-tight group-hover:text-brand-700">{{ $process['name'] }}</span>
                            <span class="mt-2 block flex-1 text-base leading-snug text-ink/70">{{ $process['description'] }}</span>
                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold">{{ __('pages.process.view') }} <span class="transition-transform duration-300 group-hover:translate-x-1 rtl:rotate-180 rtl:group-hover:-translate-x-1" aria-hidden="true">→</span></span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="bg-white">
        <div class="container-site section-y">
            <h2 data-anim="heading" class="text-2xl font-black tracking-tight text-ink">{{ __('pages.processes.others') }}</h2>
            <ul data-anim="rows" class="mt-6 flex flex-wrap gap-3">
                @foreach ($others as $other)
                    <li>
                        <a href="{{ route('landing.processes.show', ['category' => $other['slug']]) }}"
                           class="inline-flex min-h-11 items-center rounded-lg border border-ink/40 bg-lavender px-4 font-semibold text-ink hover:border-brand-700 hover:text-brand-700" lang="en" dir="ltr">
                            {{ $other['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <x-landing.cta />
@endsection
