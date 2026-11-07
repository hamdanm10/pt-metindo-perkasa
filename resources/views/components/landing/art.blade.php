@props(['name', 'eager' => false])

@php($path = "images/landing/{$name}.svg")

@if (is_file(public_path($path)))
    <img src="{{ asset($path) }}" alt="" aria-hidden="true" loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async" {{ $attributes }}>
@else
    {{ $slot }}
@endif
