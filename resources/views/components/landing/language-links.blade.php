@props(['stacked' => false, 'alternates' => null, 'dark' => false, 'dropdown' => false])

@php
    $route = request()->route();
    $current = app()->getLocale();
@endphp

<nav aria-label="{{ __('landing.language') }}">
    @if ($dropdown)
        {{-- The list of the header dropdown: the code in the accent colour, the name beside it, and a crimson dot on the current language. --}}
        <ul class="lang-list flex flex-col">
            @foreach (config('locales.landing') as $code)
                @php($locale = config("locales.locales.{$code}"))
                <li style="--i: {{ $loop->index }}">
                    <a href="{{ $alternates === null ? route($route->getName(), array_merge($route->parameters(), ['locale' => $code])) : ($alternates[$code] ?? route('landing.home', ['locale' => $code])) }}"
                       lang="{{ $locale['html_lang'] }}" hreflang="{{ $locale['html_lang'] }}" @if ($code === $current) aria-current="true" @endif
                       class="lang-item group flex min-h-11 items-center gap-3 rounded-md px-3 text-[clamp(0.8rem,0.95vw,1.15rem)] font-semibold text-white/80 transition-colors duration-300 hover:text-white focus-visible:text-white aria-[current=true]:font-bold aria-[current=true]:text-white">
                        <span class="w-6 text-[0.8em] font-extrabold uppercase text-brand-300" dir="ltr">{{ $code }}</span>
                        <span class="flex-1 transition-transform duration-500 [transition-timing-function:var(--header-ease)] group-hover:translate-x-1 rtl:group-hover:-translate-x-1"><span class="uline">{{ $locale['name'] }}</span></span>
                        <span class="size-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity duration-300 group-aria-[current=true]:opacity-100" aria-hidden="true"></span>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
    <ul class="{{ $stacked ? 'flex flex-col' : 'flex flex-wrap items-center gap-x-1 gap-y-1' }}">
        @foreach (config('locales.landing') as $code)
            @php($locale = config("locales.locales.{$code}"))
            <li>
                <a href="{{ $alternates === null ? route($route->getName(), array_merge($route->parameters(), ['locale' => $code])) : ($alternates[$code] ?? route('landing.home', ['locale' => $code])) }}"
                   lang="{{ $locale['html_lang'] }}"
                   hreflang="{{ $locale['html_lang'] }}"
                   @if ($code === $current) aria-current="true" @endif
                   class="{{ $dark ? 'flex min-h-11 items-center rounded-lg border border-white/25 px-4 text-sm font-semibold text-white hover:bg-white/10 aria-[current=true]:border-white aria-[current=true]:bg-white aria-[current=true]:font-bold aria-[current=true]:text-ink' : 'flex min-h-11 items-center rounded-lg px-3 text-sm font-semibold text-ink hover:bg-lavender aria-[current=true]:bg-lavender aria-[current=true]:font-bold aria-[current=true]:text-brand-700' }}">
                    {{ $locale['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
    @endif
</nav>
