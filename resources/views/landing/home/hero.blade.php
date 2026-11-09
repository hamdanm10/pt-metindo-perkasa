@php
    $email = config('landing.contact.email');
    $sectors = ['automotive', 'electronics', 'machinery', 'construction', 'furniture_hardware', 'oil_gas'];
@endphp

<section data-hero class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-16 pt-10 sm:px-6 sm:pt-14 lg:grid-cols-12 lg:gap-10 lg:px-8 lg:pb-24 lg:pt-16">
        <div class="lg:col-span-7">
            <h1 data-anim="hero-title" class="text-balance text-[clamp(3rem,8.2vw,7rem)] font-black uppercase leading-[0.9] tracking-tight">
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

        <div data-anim="hero-item" class="relative aspect-[5/4] overflow-hidden rounded-lg bg-lavender lg:col-span-5 lg:aspect-[4/5]" aria-hidden="true">
            <img src="{{ asset('images/brand/logo.svg') }}" alt="" width="612" height="256" class="absolute start-1/2 top-1/2 w-[120%] max-w-none -translate-y-1/2 -translate-x-1/2 rtl:translate-x-1/2">
        </div>
    </div>

    <div class="border-t border-white/20">
        <ul class="mx-auto flex max-w-7xl flex-wrap gap-x-10 gap-y-2 px-4 py-6 text-base font-semibold text-white/85 sm:px-6 lg:px-8">
            @foreach ($sectors as $sector)
                <li>{{ __("home.clients.sectors.{$sector}") }}</li>
            @endforeach
        </ul>
    </div>
</section>
