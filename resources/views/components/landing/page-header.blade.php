@props(['title', 'intro' => null])

<section class="cut-bottom relative overflow-hidden bg-lavender">
    <x-landing.art name="slash-pattern" eager class="pointer-events-none absolute -top-6 -end-32 hidden h-[115%] w-auto max-w-none lg:block rtl:-scale-x-100" />
    <div class="relative mx-auto max-w-6xl px-4 pb-12 pt-12 sm:px-6 sm:pb-16 sm:pt-16 lg:px-8 lg:pb-16 lg:pt-20">
        {{ $slot }}
        <h1 data-anim="hero-title" class="max-w-4xl text-balance text-5xl font-extrabold leading-[0.95] tracking-tighter text-ink sm:text-7xl">{{ $title }}</h1>
        @if ($intro)
            <p data-anim="hero-item" class="mt-6 max-w-2xl text-lg text-muted sm:text-xl">{{ $intro }}</p>
        @endif
    </div>
</section>
