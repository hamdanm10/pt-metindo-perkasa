{{--
    The one button of the landing page, built from the reference: 44 px high at 1440 px, 8 px corners,
    bold 14.4 px label with a thick arrow. On hover (pointer devices, without reduced motion) a
    fill rises from below and leaves through the top, the label rolls up word by word, and the
    arrow steps back. See buttons.js. Without that it is a plain button.

    variant: primary (crimson, indigo fill), light (white), outline (white line, for dark backgrounds), dark (ink)
    as: a, button, span, or summary. Defaults to a link when there is an href, else a span.
--}}
@props(['href' => null, 'variant' => 'primary', 'arrow' => true, 'as' => null])

@php
    $tag = $as ?? ($href ? 'a' : 'span');
    $look = match ($variant) {
        'light' => ['class' => 'bg-white text-ink', 'fill' => 'var(--color-ink)', 'text' => '#ffffff'],
        'outline' => ['class' => 'text-white ring-2 ring-inset ring-white', 'fill' => '#ffffff', 'text' => '#1b1f3b'],
        'dark' => ['class' => 'bg-ink text-white', 'fill' => 'var(--color-brand-600)', 'text' => '#ffffff'],
        default => ['class' => 'bg-brand-600 text-white', 'fill' => 'var(--color-accent-indigo)', 'text' => '#ffffff'],
    };
    $words = preg_split('/\s+/', trim((string) $slot), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $copy = fn (): string => collect($words)
        ->map(fn (string $word): string => '<span data-btn-word class="inline-block">'.$word.'</span>')
        ->implode(' ');
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['btn', $look['class']]) }} data-btn data-hover-text="{{ $look['text'] }}">
    <span data-btn-fill class="btn__fill" style="background: {{ $look['fill'] }}" aria-hidden="true"></span>
    <span class="btn__content">
        <span class="btn__label">
            <span data-btn-copy>{!! $copy() !!}</span>
            <span data-btn-copy aria-hidden="true" class="invisible">{!! $copy() !!}</span>
        </span>
        @if ($arrow)
            <svg data-btn-arrow class="btn__arrow rtl:-scale-x-100" viewBox="0 0 21 20" fill="none" aria-hidden="true"><path d="M10.0691 2L17.9004 10.1014L10.0691 17.6626" stroke="currentColor" stroke-width="4"/><path d="M0 9.83154H16.7814" stroke="currentColor" stroke-width="4"/></svg>
        @endif
    </span>
</{{ $tag }}>
