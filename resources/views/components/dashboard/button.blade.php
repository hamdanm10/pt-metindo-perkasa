@props(['variant' => 'primary', 'type' => 'button', 'href' => null])

@php
    $classes = 'inline-flex min-h-12 items-center justify-center rounded-lg px-7 text-base font-bold transition-colors '.match ($variant) {
        'secondary' => 'border-2 border-ink/25 text-ink hover:border-ink hover:bg-ink hover:text-white',
        'danger' => 'bg-ink text-white hover:bg-brand-700',
        default => 'bg-brand-600 text-white hover:bg-brand-700',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
