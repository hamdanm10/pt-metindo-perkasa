@php($steps = ['enquiry', 'spec_review', 'process_supply', 'delivery'])

{{-- How we work: four steps on a line that is drawn as the page scrolls. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site section-y">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <h2 data-anim="grow" class="max-w-[10em] text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.services.how.title') }}</h2>
                <p data-anim="heading" class="max-w-[26em] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.45] text-white/80">{{ __('pages.services.how.intro') }}</p>
            </div>

            <ol data-svc-steps class="relative mt-[4vw] grid gap-10 max-lg:mt-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-[2vw]">
                <span data-svc-line class="pointer-events-none absolute start-0 top-[clamp(1.25rem,1.6vw,2rem)] hidden h-px w-full origin-left bg-white/40 lg:block" aria-hidden="true"></span>
                @foreach ($steps as $step)
                    <li data-svc-step class="relative">
                        <span class="relative grid size-[clamp(2.5rem,3.2vw,4rem)] place-items-center rounded-full bg-brand-600 text-[clamp(0.85rem,1vw,1.25rem)] font-black text-white">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-[1.6vw] text-[clamp(1.25rem,1.7vw,2.1rem)] font-black uppercase tracking-tight max-lg:mt-5">{{ __("pages.services.how.steps.{$step}.title") }}</h3>
                        <p class="mt-[0.6vw] text-[clamp(0.95rem,1.05vw,1.35rem)] leading-snug text-white/75 max-lg:mt-2">{{ __("pages.services.how.steps.{$step}.text") }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
