@php($services = ['electroplating', 'chemical_supply', 'consultation'])

<section data-services class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site pb-10 pt-16 lg:pb-[4.5vw] lg:pt-[clamp(4rem,9vw,10rem)]">
        <h2 data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase">{{ __('home.services.title') }}</h2>
        <p data-anim="heading" class="mt-5 max-w-[80rem] text-balance text-[clamp(2rem,5.44vw,7.5rem)] font-bold leading-none">{{ __('home.services.intro') }}</p>
    </div>

    {{-- On wide screens the track is tall and the list below sticks to the screen while the scroll opens the services one by one. Everywhere else every service is simply open. --}}
    <div data-services-track class="relative">
        <div data-services-pin class="bg-ink">
            @foreach ($services as $service)
                <div data-acc-item class="border-t border-white/20">
                    <h3 class="container-site flex min-h-20 items-center text-[clamp(1.5rem,2.11vw,3rem)] font-bold lg:h-[clamp(4.5rem,6.25vw,7rem)]">
                        {{ __("home.services.items.{$service}.title") }}
                    </h3>
                    <div data-acc-panel>
                        <div data-acc-inner class="container-site flex flex-col gap-8 pb-12 lg:flex-row lg:items-start lg:justify-between lg:pb-[3.5vw]">
                            <div class="max-w-[55rem] lg:w-[55%]">
                                <p class="text-[clamp(1rem,1.22vw,1.5rem)] leading-snug text-white/90">{{ __("home.services.items.{$service}.text") }}</p>
                                <a href="{{ route('landing.services') }}" class="mt-8 inline-flex min-h-11 items-center gap-2 rounded-lg bg-brand-600 px-6 font-bold text-white hover:bg-brand-700">
                                    {{ __('home.services.more') }}<x-landing.arrow />
                                </a>
                            </div>

                            <div class="relative aspect-video w-full shrink-0 overflow-hidden rounded-lg bg-white/10 lg:w-[32%]" aria-hidden="true">
                                <x-landing.photo :name="'service-'.str_replace('_', '-', $service)" ratio="aspect-video" bare class="absolute inset-0 size-full bg-transparent! text-white/80!" />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
