@php($email = config('landing.contact.email'))

<section data-hero class="relative overflow-hidden bg-lavender">
    <div data-anim="hero-wedge" class="hero-wedge absolute inset-y-0 end-0 hidden w-[46%] bg-accent-indigo lg:block" aria-hidden="true"></div>
    <div class="absolute -bottom-16 start-[46%] hidden size-44 rounded-full bg-brand-600 lg:block" aria-hidden="true"></div>
    <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 pb-24 pt-14 sm:px-6 sm:pt-20 lg:grid-cols-12 lg:gap-8 lg:px-8 lg:pb-32 lg:pt-24">
        <div class="lg:col-span-7">
            <h1 data-anim="hero-title" class="text-balance text-[clamp(3rem,9vw,7rem)] font-extrabold leading-[0.9] tracking-tighter text-ink">
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

        <div class="relative mx-auto aspect-square w-full max-w-sm overflow-hidden rounded-[2rem] bg-accent-indigo lg:col-span-5 lg:max-w-none lg:overflow-visible lg:rounded-none lg:bg-transparent rtl:-scale-x-100" aria-hidden="true" data-anim="hero-art">
            <x-landing.art name="hero-waves" eager class="absolute inset-0 size-full scale-105 object-contain lg:scale-150" data-plate>
                {{-- Fallback until the vector art exists: stacked plates for plated layers. --}}
                <div data-plate class="absolute inset-[8%] -rotate-12 rounded-[2.5rem] bg-ink"></div>
                <div data-plate class="absolute inset-[8%] -rotate-6 translate-x-[4%] translate-y-[3%] rounded-[2.5rem] bg-brand-600"></div>
                <div data-plate class="absolute inset-[8%] translate-x-[8%] translate-y-[6%] rotate-3 rounded-[2.5rem] border border-ink/10 bg-gradient-to-br from-white via-lavender to-brand-100 shadow-xl"></div>
            </x-landing.art>
        </div>
    </div>
</section>
