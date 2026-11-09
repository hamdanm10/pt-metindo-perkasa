@props(['kind'])

{{-- Decorative graphics for the About headline, sized in em so they follow the headline. Still pictures for now. --}}
<svg data-about-art {{ $attributes->class(['hidden h-[0.705em] w-[3.69em] shrink-0 rounded-lg md:block']) }} viewBox="0 0 481 92" fill="none" aria-hidden="true" focusable="false">
    @if ($kind === 'bar')
        <rect width="481" height="92" fill="var(--color-brand-600)"/>
        <path d="M0 0H289C309 18 309 74 289 92H0Z" fill="var(--color-accent-indigo)"/>
        <path d="M0 0H188C208 18 208 74 188 92H0Z" fill="var(--color-ink)"/>
    @elseif ($kind === 'chart')
        <rect width="481" height="92" fill="var(--color-accent-blue)" fill-opacity="0.18"/>
        <path d="M0 92V84L85 62H250L340 30H420L481 8V92Z" fill="var(--color-accent-blue)"/>
    @else
        <defs><clipPath id="about-hex-clip"><rect width="481" height="92" rx="8"/></clipPath></defs>
        <g clip-path="url(#about-hex-clip)">
            <rect width="481" height="92" fill="var(--color-brand-300)"/>
            @foreach ([-48, 72, 192, 312, 432] as $x)
                <polygon points="{{ $x }},46 {{ $x + 26 }},0 {{ $x + 86 }},0 {{ $x + 112 }},46 {{ $x + 86 }},92 {{ $x + 26 }},92" fill="var(--color-brand-600)"/>
            @endforeach
        </g>
    @endif
</svg>
