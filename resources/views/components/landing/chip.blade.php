@props(['kind', 'dark' => false])

@php
    // Flat brand colours, the same as the graphics in the About headline (x-landing.about-art).
    $c = fn (string $token): string => "var(--color-{$token})";
    $uid = 'chip-'.$kind.'-'.substr(md5(uniqid('', true)), 0, 6);
    $square = in_array($kind, ['grid', 'rings'], true);
@endphp

{{-- A decorative chip in the visual language of the About headline graphics: a rounded box of flat brand colour holding bands, a stepped chart, hexagons, pills, a grid, rings or stripes. Sized by the classes passed in; the drawing is cropped, never stretched. `dark` swaps the colours for the ink sections. Hidden from assistive technology. --}}
<svg {{ $attributes->class(['block overflow-hidden rounded-lg']) }} viewBox="{{ $square ? '0 0 200 200' : '0 0 481 92' }}" preserveAspectRatio="xMidYMid slice" fill="none" aria-hidden="true" focusable="false" data-chip="{{ $kind }}">
    @switch($kind)
        @case('bands')
            {{-- Three bands with a round right edge and a matching hollow left edge, repeated every 481 units so they can slide without a seam. --}}
            <rect width="481" height="92" fill="{{ $c($dark ? 'brand-600' : 'ink') }}"/>
            <g class="chip-slide-bands">
                @foreach ([-481, 0] as $start)
                    @foreach ([[-20, 188, $dark ? 'lavender' : 'ink'], [188, 289, 'accent-indigo'], [289, 461, 'brand-600']] as [$from, $to, $color])
                        <path d="M{{ $start + $from }} 0H{{ $start + $to }}C{{ $start + $to + 20 }} 18 {{ $start + $to + 20 }} 74 {{ $start + $to }} 92H{{ $start + $from }}C{{ $start + $from + 20 }} 74 {{ $start + $from + 20 }} 18 {{ $start + $from }} 0Z" fill="{{ $c($color) }}"/>
                    @endforeach
                @endforeach
            </g>
            @break

        @case('chart')
            <rect width="481" height="92" fill="{{ $c('accent-blue') }}" fill-opacity="{{ $dark ? 0.35 : 0.18 }}"/>
            <path d="M0 92V84L85 62H250L340 30H420L481 8V92Z" fill="{{ $c($dark ? 'lavender' : 'accent-blue') }}"/>
            @break

        @case('hexagons')
            <rect width="481" height="92" fill="{{ $c($dark ? 'accent-indigo' : 'brand-300') }}"/>
            <g class="chip-slide-hex">
                @foreach ([-168, -48, 72, 192, 312, 432, 552] as $x)
                    <polygon points="{{ $x }},46 {{ $x + 26 }},0 {{ $x + 86 }},0 {{ $x + 112 }},46 {{ $x + 86 }},92 {{ $x + 26 }},92" fill="{{ $c($dark ? 'lavender' : 'brand-600') }}"/>
                @endforeach
            </g>
            @break

        @case('pills')
            {{-- Three bars of different lengths, like a level meter. They sit in the middle band, so a chip flatter than the drawing still shows all three. --}}
            <rect width="481" height="92" fill="{{ $dark ? $c('lavender') : '#FFFFFF' }}" fill-opacity="{{ $dark ? 0.1 : 1 }}"/>
            @foreach ([[25, 330, 'ink'], [40, 220, 'brand-600'], [55, 400, 'accent-indigo']] as [$y, $width, $color])
                <rect class="chip-pill" x="16" y="{{ $y }}" width="{{ $width }}" height="12" rx="6" fill="{{ $c($dark && $color === 'ink' ? 'lavender' : $color) }}" style="animation-delay: -{{ $loop->index * 1.3 }}s"/>
            @endforeach
            @break

        @case('grid')
            @foreach ([['ink', 'brand-600', 'accent-indigo'], ['accent-blue', 'brand-300', 'ink'], ['brand-600', 'accent-indigo', 'accent-blue']] as $row => $colors)
                @foreach ($colors as $col => $color)
                    <rect x="{{ $col * 70 }}" y="{{ $row * 70 }}" width="60" height="60" rx="10" fill="{{ $c($dark && $color === 'ink' ? 'lavender' : $color) }}"/>
                @endforeach
            @endforeach
            @break

        @case('rings')
            {{-- Quarter circles from one corner, like a cut through plated layers. --}}
            <defs><clipPath id="{{ $uid }}"><rect width="200" height="200"/></clipPath></defs>
            <g clip-path="url(#{{ $uid }})">
                <rect width="200" height="200" fill="{{ $c($dark ? 'accent-indigo' : 'brand-300') }}"/>
                @foreach ([[200, 'brand-600'], [150, $dark ? 'lavender' : 'ink'], [100, 'accent-blue'], [50, 'brand-600']] as [$radius, $color])
                    <circle cx="0" cy="200" r="{{ $radius }}" fill="{{ $c($color) }}"/>
                @endforeach
            </g>
            @break

        @case('stripes')
            <defs><clipPath id="{{ $uid }}"><rect width="481" height="92"/></clipPath></defs>
            <g clip-path="url(#{{ $uid }})">
                <rect width="481" height="92" fill="{{ $c($dark ? 'lavender' : 'ink') }}"/>
                <g class="chip-slide-stripes">
                    @foreach (range(-6, 12) as $i)
                        <path d="M{{ $i * 48 }} 92L{{ $i * 48 + 46 }} 0H{{ $i * 48 + 70 }}L{{ $i * 48 + 24 }} 92Z" fill="{{ $c('brand-600') }}"/>
                    @endforeach
                </g>
            </g>
            @break
    @endswitch
</svg>
