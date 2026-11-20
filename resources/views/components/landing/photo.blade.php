@props(['name', 'ratio' => 'aspect-[4/3]', 'alt' => '', 'bare' => false, 'fallback' => null, 'src' => null])

{{-- The photo is images/landing/photos/{name}.{ext}.  With `fallback`, a missing photo is replaced by that other photo. With `src`, that picture (a URL the admin uploaded) is used instead of the file by name. --}}
@php
    $find = fn (string $photo): ?string => collect(['webp', 'svg', 'jpg', 'jpeg', 'png'])
        ->map(fn (string $extension): string => "images/landing/photos/{$photo}.{$extension}")
        ->first(fn (string $path): bool => is_file(public_path($path)));
    $file = $find($name) ?? ($fallback ? $find($fallback) : null);
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" decoding="async" {{ $attributes->class(['w-full object-cover', $ratio]) }}>
@elseif ($file)
    <img src="{{ asset($file) }}" alt="{{ $alt }}" loading="lazy" decoding="async" {{ $attributes->class(['w-full object-cover', $ratio]) }}>
@endif
