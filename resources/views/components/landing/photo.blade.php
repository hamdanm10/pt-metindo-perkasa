@props(['name', 'ratio' => 'aspect-[4/3]', 'alt' => ''])

@php
    $file = collect(['webp', 'jpg', 'jpeg', 'png'])
        ->map(fn (string $extension): string => "images/landing/photos/{$name}.{$extension}")
        ->first(fn (string $path): bool => is_file(public_path($path)));
@endphp

@if ($file)
    <img src="{{ asset($file) }}" alt="{{ $alt }}" loading="lazy" decoding="async" {{ $attributes->class(['w-full object-cover', $ratio]) }}>
@elseif (config('landing.sample_content'))
    <div {{ $attributes->class(['flex w-full items-center justify-center rounded-3xl border-2 border-dashed border-ink/40 bg-lavender p-6 text-center text-sm font-semibold text-ink', $ratio]) }} role="img" aria-label="Photo placeholder">
        [PHOTO] images/landing/photos/{{ $name }}.webp
    </div>
@endif
