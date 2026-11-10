@props(['title'])

<div class="mb-8 lg:mb-10">
    <h1 class="text-[clamp(2rem,4.2vw,3.5rem)] font-black uppercase leading-[0.98] tracking-tight">{{ $title }}</h1>
    {{ $slot }}
</div>
