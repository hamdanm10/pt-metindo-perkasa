@php($email = config('landing.contact.email'))

<section data-hero class="overflow-hidden bg-lavender">
    <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 pb-24 pt-14 sm:px-6 sm:pt-20 lg:grid-cols-12 lg:gap-8 lg:px-8 lg:pb-32 lg:pt-24">
        <div class="lg:col-span-7">
            <h1 data-anim="hero-title" class="text-balance text-[clamp(2.75rem,8vw,6rem)] font-extrabold leading-[0.95] tracking-tight text-ink">
                {{ __('home.hero.title') }}
            </h1>
            <p data-anim="hero-item" class="mt-8 max-w-xl text-lg text-muted sm:text-xl">
                {{ __('home.hero.text') }}
            </p>
            <div data-anim="hero-item" class="mt-10 flex flex-wrap gap-3">
                <a href="#catalog" class="inline-flex min-h-12 items-center rounded-lg bg-brand-600 px-6 font-bold text-white hover:bg-brand-700">
                    {{ __('home.hero.explore') }}
                </a>
                @if ($email)
                    <a href="mailto:{{ $email }}" class="inline-flex min-h-12 items-center rounded-lg border-2 border-ink px-6 font-bold text-ink hover:bg-ink hover:text-white">
                        {{ __('home.hero.contact') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Stacked plates stand in for plated layers on a metal sheet. Mirrored in RTL. --}}
        <div class="relative mx-auto aspect-square w-full max-w-sm lg:col-span-5 lg:max-w-none rtl:-scale-x-100" aria-hidden="true" data-anim="hero-art">
            <x-landing.art name="hero-waves" class="absolute inset-0 h-full w-full object-contain" />
            <div data-plate class="absolute inset-[8%] -rotate-12 rounded-[2.5rem] bg-ink"></div>
            <div data-plate class="absolute inset-[8%] -rotate-6 translate-x-[4%] translate-y-[3%] rounded-[2.5rem] bg-brand-600"></div>
            <div data-plate class="absolute inset-[8%] translate-x-[8%] translate-y-[6%] rotate-3 rounded-[2.5rem] border border-ink/10 bg-gradient-to-br from-white via-lavender to-brand-100 shadow-xl"></div>
        </div>
    </div>
</section>
