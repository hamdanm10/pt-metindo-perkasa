@props(['title'])

<div class="mb-8">
    <h1 class="text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">{{ $title }}</h1>
    {{ $slot }}
</div>
