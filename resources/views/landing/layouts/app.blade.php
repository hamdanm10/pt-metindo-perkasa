@php
    $code = app()->getLocale();
    $locale = config("locales.locales.{$code}");
    $route = request()->route();
    $urlFor = fn (string $target) => route($route->getName(), array_merge($route->parameters(), ['locale' => $target]));
    $assets = ['resources/css/landing.css', 'resources/css/fonts/latin.css', 'resources/js/landing.js'];

    if (in_array($code, ['zh', 'ja', 'ar'], true)) {
        $assets[] = "resources/css/fonts/{$code}.css";
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale['html_lang'] }}" dir="{{ $locale['dir'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- yieldContent returns text that @section already escaped once. --}}
    <title>{!! $__env->hasSection('title') ? trim($__env->yieldContent('title')).' | PT Metindo Perkasa' : 'PT Metindo Perkasa' !!}</title>
    <link rel="canonical" href="{{ $urlFor($code) }}">
    @foreach (config('locales.landing') as $target)
        <link rel="alternate" hreflang="{{ config("locales.locales.{$target}.html_lang") }}" href="{{ $urlFor($target) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $urlFor(config('locales.default')) }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <script>
        try {
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.documentElement.classList.add('anim-hide');
                setTimeout(function () {
                    if (!window.__animReady) document.documentElement.classList.remove('anim-hide');
                }, 3500);
            }
        } catch (e) {}
    </script>
    @vite($assets)
    @stack('head')
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-ink focus:ring-2 focus:ring-accent-blue">
        {{ __('landing.skip_to_content') }}
    </a>

    <x-landing.header />

    <main id="main" class="flex-1 overflow-x-clip">
        @yield('content')
    </main>

    <x-landing.footer />
</body>
</html>
