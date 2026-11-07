@props(['title', 'intro' => null])

<section class="overflow-hidden bg-lavender">
    <div class="mx-auto max-w-6xl px-4 pb-16 pt-12 sm:px-6 sm:pb-20 sm:pt-16 lg:px-8 lg:pb-24 lg:pt-20">
        {{ $slot }}
        <h1 class="max-w-4xl text-balance text-4xl font-extrabold leading-[1.05] tracking-tight text-ink sm:text-6xl">{{ $title }}</h1>
        @if ($intro)
            <p class="mt-6 max-w-2xl text-lg text-muted sm:text-xl">{{ $intro }}</p>
        @endif
    </div>
</section>
