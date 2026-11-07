@props(['stacked' => false])

@php
    $route = request()->route();
    $current = app()->getLocale();
@endphp

<nav aria-label="{{ __('landing.language') }}">
    <ul class="{{ $stacked ? 'flex flex-col' : 'flex flex-wrap items-center gap-x-1 gap-y-1' }}">
        @foreach (config('locales.landing') as $code)
            @php($locale = config("locales.locales.{$code}"))
            <li>
                <a href="{{ route($route->getName(), array_merge($route->parameters(), ['locale' => $code])) }}"
                   lang="{{ $locale['html_lang'] }}"
                   hreflang="{{ $locale['html_lang'] }}"
                   @if ($code === $current) aria-current="true" @endif
                   class="inline-flex min-h-11 items-center rounded px-3 text-sm {{ $code === $current ? 'font-bold text-brand-700 underline decoration-2 underline-offset-8' : 'text-ink hover:text-brand-700' }}">
                    {{ $locale['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
