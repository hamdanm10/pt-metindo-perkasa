@extends('landing.layouts.app')

@section('title', $process['name'])

@section('content')
    <x-landing.page-header :title="$process['name']" :intro="$process['description']">
        <nav aria-label="{{ __('pages.processes.title') }}" class="mb-6 flex flex-wrap items-center gap-x-2 text-white">
            <a href="{{ route('landing.processes.index') }}" class="inline-flex min-h-11 items-center font-semibold underline underline-offset-4">{{ __('pages.processes.title') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('landing.processes.show', ['category' => $process['category']['slug']]) }}" class="inline-flex min-h-11 items-center font-semibold underline underline-offset-4" lang="en" dir="ltr">{{ $process['category']['name'] }}</a>
        </nav>
    </x-landing.page-header>

    <section class="bg-white">
        <div class="container-site section-y">
            <dl class="grid gap-px overflow-hidden rounded-lg border border-ink/20 bg-ink/20 sm:grid-cols-3">
                <div class="bg-white p-6">
                    <dt class="text-xs font-extrabold uppercase text-ink/60">{{ __('pages.process.category') }}</dt>
                    <dd class="mt-3 text-lg font-bold"><a href="{{ route('landing.processes.show', ['category' => $process['category']['slug']]) }}" class="text-ink hover:text-brand-700" lang="en" dir="ltr">{{ $process['category']['name'] }}</a></dd>
                </div>
                <div class="bg-white p-6">
                    <dt class="text-xs font-extrabold uppercase text-ink/60">{{ __('pages.process.steps') }}</dt>
                    <dd class="mt-3 text-4xl font-black tabular-nums text-ink">{{ str_pad((string) count($process['steps']), 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div class="bg-white p-6">
                    <dt class="text-xs font-extrabold uppercase text-ink/60">{{ __('pages.process.materials') }}</dt>
                    <dd class="mt-3 text-4xl font-black tabular-nums text-ink">{{ str_pad((string) $process['products'], 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="bg-lavender">
        <div class="container-site section-y">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <p class="text-xs font-extrabold uppercase text-brand-700">{{ __('pages.process.flow_label') }}</p>
                    <h2 data-anim="heading" class="mt-3 text-3xl font-black tracking-tight text-ink">{{ __('pages.process.flow_title') }}</h2>
                    <p class="mt-3 text-lg text-ink/75">{{ __('pages.process.flow_intro') }}</p>
                </div>
                @if ($process['steps'] !== [])
                    <button type="button" data-diagram-open class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-ink px-5 font-bold text-white hover:bg-brand-600">{{ __('pages.process.view_diagram') }}</button>
                @endif
            </div>

            @if ($process['steps'] === [])
                <p class="mt-10 rounded-lg border-2 border-dashed border-ink/30 p-8 text-lg font-semibold text-ink/70">{{ __('pages.process.empty') }}</p>
            @else
                <div class="relative mt-10">
                <span class="absolute start-[21px] top-5 bottom-5 w-0.5 -translate-x-1/2 bg-ink/15 rtl:translate-x-1/2" aria-hidden="true"><span data-flow-line class="relative block w-full bg-brand-600"><span data-flow-dot class="absolute start-1/2 bottom-0 size-3.5 -translate-x-1/2 translate-y-1/2 rounded-full bg-brand-600 ring-4 ring-brand-600/25 rtl:translate-x-1/2"></span></span></span>
                <ol class="relative space-y-6" data-process-steps>
                    @foreach ($process['steps'] as $step)
                        <li class="relative ps-16" data-flow-step>
                            <span data-flow-badge class="absolute start-0 top-0 z-10 inline-flex size-11 items-center justify-center rounded-full bg-brand-600 text-sm font-extrabold text-white ring-2 ring-brand-600 transition-colors duration-300 data-[pending]:bg-white data-[pending]:text-brand-700" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div data-flow-card class="rounded-lg bg-white p-6 ring-2 ring-transparent transition-shadow duration-300 data-[active]:shadow-xl data-[active]:ring-brand-600/60">
                                <div class="overflow-hidden"><h3 data-flow-title class="text-xl font-black text-ink" lang="en" dir="ltr">{{ $step['name'] }}</h3></div>
                                @if ($step['description'] !== '')
                                    <p data-flow-text class="mt-1 text-ink/75" lang="en" dir="ltr">{{ $step['description'] }}</p>
                                @endif
                                @if ($step['products'] !== [])
                                    <p class="mt-5 text-xs font-extrabold uppercase text-ink/60">{{ __('pages.process.materials') }}</p>
                                    <ul data-flow-chips class="mt-3 flex flex-wrap gap-2" lang="en" dir="ltr">
                                        @foreach ($step['products'] as $product)
                                            <li>
                                                @php
                                                    $detail = [
                                                        'name' => $product['name'],
                                                        'usage' => $product['description'] ?? __('pages.process.usage_text', ['name' => $product['name'], 'step' => mb_strtolower($step['name'])]),
                                                        'brochure' => $product['brochure_url'],
                                                        'video' => $product['video_url'],
                                                        'price' => $product['price'],
                                                        'tds' => $product['tds_url'],
                                                        'links' => $product['links'],
                                                    ];
                                                @endphp
                                                <button type="button" data-material-open data-product="{{ json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                                                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-ink/30 bg-white px-4 text-sm font-semibold text-ink hover:border-brand-700 hover:text-brand-700">
                                                    {{ $product['name'] }}<span aria-hidden="true" class="text-brand-600">+</span>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
                </div>
            @endif
        </div>
    </section>

    @if ($process['others'] !== [])
        <section class="bg-white">
            <div class="container-site section-y">
                <h2 data-anim="heading" class="text-2xl font-black tracking-tight text-ink">{{ __('pages.process.others') }}</h2>
                <ul class="mt-6 flex flex-wrap gap-3" lang="en" dir="ltr">
                    @foreach ($process['others'] as $other)
                        <li>
                            <a href="{{ route('landing.process.show', ['process' => $other['slug']]) }}"
                               class="inline-flex min-h-11 items-center rounded-lg border border-ink/40 bg-white px-4 font-semibold text-ink hover:border-brand-700 hover:text-brand-700">{{ $other['name'] }} →</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-landing.cta />

    @if ($process['steps'] !== [])
        <dialog data-diagram-dialog aria-labelledby="diagram-title" class="m-auto w-[min(72rem,calc(100vw-2rem))] max-w-none rounded-lg bg-white p-0 text-ink backdrop:bg-ink/70">
            <div class="flex items-start justify-between gap-4 border-b border-ink/15 p-5">
                <div>
                    <p class="text-xs font-extrabold uppercase text-brand-700">{{ __('pages.process.flow_label') }}</p>
                    <h2 id="diagram-title" class="mt-1 text-xl font-black" lang="en" dir="ltr">{{ $process['name'] }}</h2>
                </div>
                <div class="flex shrink-0 gap-2">
                    <button type="button" data-diagram-replay class="inline-flex min-h-11 items-center rounded-lg border border-ink/30 px-4 text-sm font-bold hover:border-brand-700 hover:text-brand-700">{{ __('pages.process.replay') }}</button>
                    <button type="button" data-dialog-close class="inline-flex min-h-11 items-center rounded-lg bg-ink px-4 text-sm font-bold text-white hover:bg-brand-600">{{ __('pages.process.close') }}</button>
                </div>
            </div>
            <ol data-diagram-track class="grid gap-y-8 bg-lavender p-6 sm:p-8 md:grid-cols-2 lg:grid-cols-4" lang="en" dir="ltr">
                @foreach ($process['steps'] as $step)
                    <li data-diagram-node class="relative">
                        <span data-diagram-link class="diagram-link absolute start-11 top-5 h-[3px] w-[calc(100%-2.75rem)] bg-brand-600 {{ $loop->last ? 'hidden' : '' }}" aria-hidden="true"></span>
                        <span data-diagram-badge class="relative z-10 inline-flex size-11 items-center justify-center rounded-full bg-white text-sm font-extrabold text-brand-700 ring-2 ring-brand-600 transition-colors duration-300 data-[reached]:bg-brand-600 data-[reached]:text-white">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        @if ($step['animation'] !== null)
                            <x-process.step-animation :name="$step['animation']" class="mt-4 h-24 w-32 rounded-lg bg-white" />
                        @endif
                        <h3 class="mt-3 pe-4 text-lg font-black">{{ $step['name'] }}</h3>
                        @if ($step['description'] !== '')
                            <p class="mt-1 pe-4 text-sm text-ink/75">{{ $step['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </dialog>
    @endif

    <dialog data-material-dialog data-open-label="{{ __('pages.process.open') }}" data-request-label="{{ __('pages.process.request') }}" data-contact-url="{{ route('landing.contact') }}" aria-labelledby="material-title" class="m-auto w-[min(32rem,calc(100vw-2rem))] max-w-none rounded-lg bg-white p-0 text-ink backdrop:bg-ink/70">
        <div class="flex items-start justify-between gap-4 border-b border-ink/15 p-5">
            <div>
                <p class="text-xs font-extrabold uppercase text-brand-700">{{ __('pages.process.material_detail') }}</p>
                <h2 id="material-title" data-material-name class="mt-1 text-xl font-black" lang="en" dir="ltr"></h2>
            </div>
            <button type="button" data-dialog-close class="inline-flex min-h-11 shrink-0 items-center rounded-lg bg-ink px-4 text-sm font-bold text-white hover:bg-brand-600">{{ __('pages.process.close') }}</button>
        </div>
        <dl class="divide-y divide-ink/10 p-5">
            <div class="py-3"><dt class="text-xs font-extrabold uppercase text-ink/60">{{ __('pages.process.usage') }}</dt><dd data-material-usage class="mt-1 whitespace-pre-line"></dd></div>
            @foreach (['brochure', 'video', 'ecommerce', 'tds', 'price'] as $field)
                <div class="py-3"><dt class="text-xs font-extrabold uppercase text-ink/60">{{ __("pages.process.{$field}") }}</dt><dd data-material-{{ $field }} class="mt-1 text-ink/60">–</dd></div>
            @endforeach
        </dl>
    </dialog>
@endsection
