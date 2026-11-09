@php($services = ['electroplating', 'chemical_supply', 'consultation'])

<section class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site section-y">
        <div class="max-w-3xl">
            <h2 data-anim="grow" class="text-[clamp(2.25rem,5vw,6rem)] font-black uppercase leading-[0.98] tracking-tight">{{ __('home.services.title') }}</h2>
            <p class="mt-5 text-lg text-white/85 sm:text-xl">{{ __('home.services.intro') }}</p>
        </div>

        <div data-accordion data-anim="rows" class="mt-14">
            @foreach ($services as $service)
                <div data-acc-item class="border-t border-white/20">
                    <h3>
                        <button type="button" data-acc-button id="service-{{ $loop->iteration }}-button" aria-expanded="true" aria-controls="service-{{ $loop->iteration }}-panel"
                                class="group flex min-h-24 w-full items-center gap-6 py-6 text-start lg:gap-10">
                            <span class="w-16 shrink-0 text-4xl font-black text-brand-400 lg:w-24 lg:text-5xl" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="flex-1 text-2xl font-bold sm:text-4xl">{{ __("home.services.items.{$service}.title") }}</span>
                            <span class="plus relative size-8 shrink-0" aria-hidden="true"></span>
                        </button>
                    </h3>
                    <div data-acc-panel id="service-{{ $loop->iteration }}-panel" role="region" aria-labelledby="service-{{ $loop->iteration }}-button">
                        <div class="flex flex-col gap-6 pb-10 ps-[5.5rem] lg:flex-row lg:items-center lg:justify-between lg:ps-[8.5rem]">
                            <p class="max-w-xl text-lg text-white/85">{{ __("home.services.items.{$service}.text") }}</p>
                            <a href="{{ route('landing.services') }}" class="inline-flex min-h-12 items-center gap-2 self-start rounded-lg bg-brand-600 px-6 font-bold text-white hover:bg-brand-700">
                                {{ __('home.services.all') }}<x-landing.arrow />
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
