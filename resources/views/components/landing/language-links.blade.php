@props(['stacked' => false, 'alternates' => null, 'dark' => false])

@php
    $route = request()->route();
    $current = app()->getLocale();
@endphp

<nav aria-label="{{ __('landing.language') }}">
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
</nav>
