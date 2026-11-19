@props(['name', 'ratio' => 'aspect-[4/3]', 'alt' => '', 'bare' => false, 'fallback' => null])

{{-- The photo is images/landing/photos/{name}.{ext}. With `fallback`, a missing photo is replaced by that other photo before the sample placeholder shows, so a card can use an existing picture until its own arrives. --}}
@php
    $find = fn (string $photo): ?string => collect(['webp', 'svg', 'jpg', 'jpeg', 'png'])
        ->map(fn (string $extension): string => "images/landing/photos/{$photo}.{$extension}")
        ->first(fn (string $path): bool => is_file(public_path($path)));
    $file = $find($name) ?? ($fallback ? $find($fallback) : null);
@endphp

@if ($file)
    <img src="{{ asset($file) }}" alt="{{ $alt }}" loading="lazy" decoding="async" {{ $attributes->class(['w-full object-cover', $ratio]) }}>
@elseif (config('landing.sample_content'))
    <div {{ $attributes->class(['flex w-full bg-lavender p-6 text-sm font-semibold text-ink', 'items-center justify-center text-center rounded-lg border-2 border-dashed border-ink/40' => ! $bare, 'items-start justify-start text-start' => $bare, $ratio]) }} role="img" aria-label="Photo placeholder" dir="ltr">
        [PHOTO] images/landing/photos/{{ $name }}.webp
    </div>
@endif
