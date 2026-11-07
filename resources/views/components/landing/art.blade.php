@props(['name'])

@php($path = "images/landing/{$name}.svg")

@if (is_file(public_path($path)))
    <img src="{{ asset($path) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" {{ $attributes }}>
@endif
