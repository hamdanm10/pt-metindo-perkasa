@props(['kind', 'fluid' => false, 'onBrand' => false])

@php($uid = 'art-'.$kind.'-'.substr(md5(uniqid('', true)), 0, 6))

{{-- Decorative graphics for the About headline, sized in em so they follow the headline. They are still pictures at rest; on hover (about.js) the chart flips, the bands slide, and the hexagons turn, as on the reference. --}}
<svg data-about-art {{ $attributes->class($fluid ? ['block h-auto w-full rounded-lg'] : ['hidden h-[0.705em] w-[3.69em] shrink-0 rounded-lg md:block']) }} viewBox="0 0 481 92" fill="none" aria-hidden="true" focusable="false">
    @if ($kind === 'bar')
        <defs><clipPath id="{{ $uid }}"><rect width="481" height="92" rx="8"/></clipPath></defs>
        <g clip-path="url(#{{ $uid }})">
            {{-- Three bands repeat every 481 units. Each band has a round right edge and a matching hollow left edge, so the bands meet without gaps and wrap around when they slide. At rest the first band starts 20 units left of the box, so its hollow edge is out of sight. --}}
            <g data-art-bar>
                @foreach ([-481, 0] as $start)
                    @foreach ([[-20, 188, 'ink'], [188, 289, 'accent-indigo'], [289, 461, $onBrand ? 'lavender' : 'brand-600']] as [$from, $to, $color])
                        <path d="M{{ $start + $from }} 0H{{ $start + $to }}C{{ $start + $to + 20 }} 18 {{ $start + $to + 20 }} 74 {{ $start + $to }} 92H{{ $start + $from }}C{{ $start + $from + 20 }} 74 {{ $start + $from + 20 }} 18 {{ $start + $from }} 0Z" fill="var(--color-{{ $color }})"/>
                    @endforeach
                @endforeach
            </g>
            </g>
        </g>
    @elseif ($kind === 'chart')
        <rect width="481" height="92" fill="{{ $onBrand ? 'var(--color-brand-700)' : 'var(--color-accent-blue)' }}" fill-opacity="{{ $onBrand ? 1 : 0.18 }}"/>
        <path data-art-chart d="M0 92V84L85 62H250L340 30H420L481 8V92Z" fill="{{ $onBrand ? 'var(--color-ink)' : 'var(--color-accent-blue)' }}"/>
    @else
        <defs><clipPath id="{{ $uid }}"><rect width="481" height="92" rx="8"/></clipPath></defs>
        <g clip-path="url(#{{ $uid }})">
            <rect width="481" height="92" fill="{{ $onBrand ? 'var(--color-brand-700)' : 'var(--color-brand-300)' }}"/>
            <g data-art-hex>
                @foreach ([-48, 72, 192, 312, 432, 552] as $x)
                    <polygon points="{{ $x }},46 {{ $x + 26 }},0 {{ $x + 86 }},0 {{ $x + 112 }},46 {{ $x + 86 }},92 {{ $x + 26 }},92" fill="{{ $onBrand ? 'var(--color-brand-300)' : 'var(--color-brand-600)' }}"/>
                @endforeach
            </g>
        </g>
    @endif
</svg>
