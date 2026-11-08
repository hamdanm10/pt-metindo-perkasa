@props(['code'])

@php
    $context = app(\App\Services\Errors\ErrorContext::class)->resolve(request(), (int) $code);
    $locale = $context['locale'];
    app()->setLocale($locale);

    $meta = config("locales.locales.{$locale}");
    $landing = $context['area'] === 'landing';

    $home = $landing
        ? url("/{$locale}")
        : url('/'.$context['dashboardArea'].'/dashboard');

    $assets = ['resources/css/'.($landing ? 'landing' : 'dashboard').'.css', 'resources/css/fonts/latin.css'];

    if ($landing && in_array($locale, ['zh', 'ja', 'ar'], true)) {
        $assets[] = "resources/css/fonts/{$locale}.css";
    }

    try {
        $assetTags = app(\Illuminate\Foundation\Vite::class)($assets)->toHtml();
    } catch (\Throwable) {
        $assetTags = '';
    }

    $title = __("errors.{$code}.title");
    $text = __("errors.{$code}.text");
@endphp
<!DOCTYPE html>
<html lang="{{ $meta['html_lang'] }}" dir="{{ $meta['dir'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $code }} | {{ $title }} | PT Metindo Perkasa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <style>body{margin:0;font-family:system-ui,sans-serif;background:#f3f2ff;color:#1b1f3b}</style>
    {!! $assetTags !!}
</head>
<body class="min-h-screen bg-lavender font-sans text-ink antialiased">
    <main class="relative flex min-h-screen items-center overflow-hidden">
        @if ($landing)
            <div class="hero-wedge absolute inset-y-0 end-0 hidden w-2/5 bg-accent-indigo lg:block" aria-hidden="true"></div>
            <div class="absolute -bottom-16 end-1/4 hidden size-44 rounded-full bg-brand-600 lg:block" aria-hidden="true"></div>
        @endif

        <div class="relative mx-auto w-full max-w-5xl px-6 py-16 lg:px-8">
            <a href="{{ $home }}" class="inline-flex rounded">
                <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-8 w-auto">
            </a>

            <p class="mt-12 text-[clamp(6rem,24vw,13rem)] font-extrabold leading-[0.85] tracking-tighter text-brand-600 rtl:text-right" aria-hidden="true" dir="ltr">{{ $code }}</p>

            <h1 class="mt-6 max-w-xl text-balance text-3xl font-extrabold leading-tight sm:text-5xl">{{ $title }}</h1>
            <p class="mt-4 max-w-md text-lg text-muted">{{ $text }}</p>

            @if ((int) $code !== 503)
                <a href="{{ $home }}" class="mt-10 inline-flex min-h-12 items-center rounded-lg bg-brand-600 px-7 text-base font-bold text-white hover:bg-brand-700">{{ __('errors.home') }}</a>
            @endif
        </div>
    </main>
</body>
</html>
