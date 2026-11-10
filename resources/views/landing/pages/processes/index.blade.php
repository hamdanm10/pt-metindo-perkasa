@extends('landing.layouts.app')

@section('title', __('landing.nav.processes'))

@section('content')
    @php
        $tones = ['bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
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

    {{-- The categories as a list with a large preview, as in the prototype: choose a category on the left, and its picture, text and processes appear on the right. --}}
    <section class="bg-lavender text-ink">
        <div class="container-site section-y">
            <div data-ptabs class="grid gap-6 lg:grid-cols-[4fr_8fr] lg:gap-[2.5vw]">
                <div class="min-w-0 lg:sticky lg:top-[calc(var(--header-h)+1.5rem)] lg:self-start">
                    <div role="tablist" aria-orientation="vertical" aria-label="{{ __('pages.processes.title') }}" data-ptabs-list
                         class="relative -mx-4 flex snap-x gap-2 overflow-x-auto px-4 pb-2 sm:-mx-6 sm:px-6 lg:mx-0 lg:flex-col lg:gap-0 lg:overflow-visible lg:px-0 lg:pb-0">
                        <span data-ptabs-bar class="pointer-events-none absolute start-0 top-0 hidden w-full rounded-lg border-s-4 border-brand-600 bg-white shadow-sm lg:block" aria-hidden="true"></span>
                        @foreach ($categories as $category)
                            <button type="button" role="tab" id="tab-{{ $category['slug'] }}" aria-controls="panel-{{ $category['slug'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}" data-ptab
                                    class="ptab group relative flex shrink-0 snap-center items-center gap-[1vw] whitespace-nowrap rounded-full border border-ink/15 bg-white/70 px-4 py-2 text-start text-sm font-bold transition-colors duration-300 hover:bg-white focus-visible:outline-offset-2 max-lg:aria-selected:border-brand-600 max-lg:aria-selected:bg-white lg:w-full lg:whitespace-normal lg:rounded-lg lg:border-0 lg:bg-transparent lg:px-[1.1vw] lg:py-[0.95vw] lg:text-[clamp(0.95rem,1.15vw,1.5rem)] lg:hover:bg-transparent">
                                <span class="hidden w-[1.6em] text-[0.7em] font-extrabold tabular-nums text-ink/40 transition-colors duration-300 group-aria-selected:text-brand-600 lg:inline" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="flex-1 transition-transform duration-500 [transition-timing-function:var(--header-ease)] lg:group-hover:translate-x-1 lg:group-aria-selected:translate-x-1" lang="en" dir="ltr">{{ $category['name'] }}</span>
                                <span class="hidden rounded-full bg-ink/10 px-2 py-0.5 text-[0.7em] font-bold transition-colors duration-300 group-aria-selected:bg-brand-600 group-aria-selected:text-white lg:inline">{{ count($category['processes']) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="ptabs-panels grid min-w-0">
                    @foreach ($categories as $category)
                        @php($tone = $tones[$loop->index % count($tones)])
                        <article role="tabpanel" id="panel-{{ $category['slug'] }}" aria-labelledby="tab-{{ $category['slug'] }}" data-ppanel @if ($loop->first) data-active @endif
                                 class="ppanel col-start-1 row-start-1 overflow-hidden rounded-lg bg-ink text-white" style="--focus-ring: #ffffff">
                            <div data-pmedia class="{{ $tone }} relative aspect-[16/8] overflow-hidden">
                                <div data-pmedia-inner class="absolute inset-0">
                                    <x-landing.photo :name="'product-'.$category['slug']" ratio="aspect-[16/8]" bare class="absolute inset-0 size-full bg-transparent! text-white!" />
                                </div>
                                <span data-pnumber class="absolute bottom-0 start-[clamp(1.25rem,2.2vw,2.75rem)] block overflow-hidden text-[clamp(4rem,9vw,11rem)] font-black leading-[0.8] text-white/35" aria-hidden="true"><span class="block">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></span>
                                <span class="absolute end-[clamp(1rem,1.6vw,2rem)] top-[clamp(1rem,1.6vw,2rem)] rounded-full bg-white px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-ink">{{ __('pages.processes.list') }} · {{ count($category['processes']) }}</span>
                            </div>

                            <div class="p-[clamp(1.25rem,2.4vw,3rem)]">
                                <p data-pitem class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-brand-300">{{ __('pages.processes.position', ['number' => str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT), 'total' => count($categories)]) }}</p>
                                <h2 data-ptitle class="mt-[0.6vw] text-[clamp(1.75rem,3.3vw,4.25rem)] font-black uppercase leading-[0.98] tracking-tight max-lg:mt-2" lang="en" dir="ltr">{{ $category['name'] }}</h2>
                                <p data-pitem class="mt-[0.8vw] max-w-[34em] text-[clamp(1rem,1.15vw,1.45rem)] leading-snug text-white/75 max-lg:mt-3">{{ __("pages.processes.categories.{$category['slug']}") }}</p>

                                <ul class="mt-[2vw] grid gap-x-[2vw] sm:grid-cols-2 xl:grid-cols-3 max-lg:mt-6" lang="en" dir="ltr">
                                    @foreach ($category['processes'] as $process)
                                        <li data-pitem class="flex items-baseline gap-3 border-t border-white/20 py-[0.75vw] text-[clamp(0.9rem,1vw,1.25rem)] font-semibold max-lg:py-3">
                                            <span class="text-[0.7em] font-extrabold tabular-nums text-brand-300">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                            <span>{{ $process }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <div data-pitem class="mt-[2vw] max-lg:mt-6">
                                    <x-landing.button :href="route('landing.processes.show', ['category' => $category['slug']])" variant="light">{{ __('pages.processes.view_all', ['count' => count($category['processes'])]) }}</x-landing.button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
            <noscript><style>[data-ppanel]{visibility:visible!important}.ptabs-panels{display:block!important}[data-ppanel]+[data-ppanel]{margin-top:1.5rem}[data-ptabs-list]{display:none!important}</style></noscript>
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
