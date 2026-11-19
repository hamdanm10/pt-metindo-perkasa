<section data-hero class="flex min-h-svh flex-col justify-center bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site grid w-full items-center gap-12 pb-16 pt-10 sm:pt-14 lg:grid-cols-[minmax(0,1.17fr)_minmax(0,1fr)] lg:gap-[3.4vw] lg:py-12">
        <div>
            <h1 data-anim="hero-title" class="text-balance text-[clamp(3rem,7.2vw,10rem)] font-black uppercase leading-[0.9] tracking-tight">
                {{ __('home.hero.title') }}
            </h1>
            <p data-anim="hero-item" class="mt-8 max-w-xl text-lg text-white/85 sm:text-xl">
                {{ __('home.hero.text') }}
            </p>
            <div data-anim="hero-item" class="mt-10 flex flex-wrap gap-4">
                <x-landing.button href="#catalog">{{ __('home.hero.explore') }}</x-landing.button>
                <x-landing.button :href="route('landing.contact')" variant="outline">{{ __('home.hero.contact') }}</x-landing.button>
            </div>
        </div>

        <div data-anim="hero-item" data-hero-panel class="relative aspect-[1200/1410] w-full max-w-[40rem] justify-self-center lg:max-h-[calc(100svh-var(--header-h)-9rem)] lg:max-w-none" aria-hidden="true">
            <x-landing.professor-scene class="size-full" />
        </div>
    </div>
</section>
