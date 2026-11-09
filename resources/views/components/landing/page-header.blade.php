@props(['title', 'intro' => null])

<section class="relative overflow-hidden bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site relative pb-14 pt-10 sm:pb-20 sm:pt-14 lg:pb-24 lg:pt-20">
        {{ $slot }}
        <h1 data-anim="hero-title" class="max-w-5xl text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ $title }}</h1>
        @if ($intro)
            <p data-anim="hero-item" class="mt-6 max-w-2xl text-lg text-white/85 sm:text-xl">{{ $intro }}</p>
        @endif
    </div>
</section>
