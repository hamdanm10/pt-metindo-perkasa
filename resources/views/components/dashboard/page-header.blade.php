@props(['title', 'description' => null])

<div class="mb-8 lg:mb-10">
    <h1 class="text-[clamp(1.5rem,2.2vw,2rem)] font-black uppercase leading-tight tracking-tight">{{ $title }}</h1>
    @if ($description)
        <p class="mt-2 max-w-2xl text-base leading-snug text-muted">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
