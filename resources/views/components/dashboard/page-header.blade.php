@props(['title'])

<div class="mb-8 lg:mb-10">
    <span class="mb-4 block h-1.5 w-12 -skew-x-[20deg] rounded-sm bg-brand-600" aria-hidden="true"></span>
    <h1 class="text-[clamp(2rem,4.2vw,3.5rem)] font-black uppercase leading-[0.98] tracking-tight">{{ $title }}</h1>
    {{ $slot }}
</div>
