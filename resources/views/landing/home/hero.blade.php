@php($email = config('landing.contact.email'))

<section data-hero class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site grid items-center gap-12 pb-16 pt-10 sm:pt-14 lg:grid-cols-[minmax(0,1.17fr)_minmax(0,1fr)] lg:gap-[3.4vw] lg:pb-24 lg:pt-16">
        <div>
            <h1 data-anim="hero-title" class="text-balance text-[clamp(3rem,7.2vw,10rem)] font-black uppercase leading-[0.9] tracking-tight">
                {{ __('home.hero.title') }}
            </h1>
            <p data-anim="hero-item" class="mt-8 max-w-xl text-lg text-white/85 sm:text-xl">
                {{ __('home.hero.text') }}
            </p>
            <div data-anim="hero-item" class="mt-10 flex flex-wrap gap-3">
                <a href="#catalog" class="inline-flex min-h-12 items-center gap-2 rounded-lg bg-brand-600 px-6 font-bold text-white hover:bg-brand-700">
                    {{ __('home.hero.explore') }}<x-landing.arrow />
                </a>
                @if ($email)
                    <a href="mailto:{{ $email }}" class="inline-flex min-h-12 items-center rounded-lg border-2 border-white px-6 font-bold text-white hover:bg-white hover:text-ink">
                        {{ __('home.hero.contact') }}
                    </a>
                @endif
            </div>
        </div>

        <div data-anim="hero-item" data-hero-panel class="relative aspect-[5/4] overflow-hidden rounded-lg bg-lavender lg:aspect-square" aria-hidden="true">
            <img src="{{ asset('images/brand/logo.svg') }}" alt="" width="612" height="256" class="absolute start-1/2 top-1/2 w-[120%] max-w-none -translate-y-1/2 -translate-x-1/2 rtl:translate-x-1/2">
        </div>
    </div>
</section>
