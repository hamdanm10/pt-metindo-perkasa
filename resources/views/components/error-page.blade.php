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
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <style>body{margin:0;background:#f3f2ff;color:#1b1f3b}</style>
    {!! $assetTags !!}
</head>
<body class="min-h-screen bg-lavender font-sans text-ink antialiased">
    <main class="grid min-h-screen lg:grid-cols-[5fr_6fr]">
        <div class="relative flex flex-col justify-between overflow-hidden bg-ink px-6 py-8 text-white sm:px-12 lg:p-14" style="--focus-ring: #ffffff">
            <a href="{{ $home }}" class="inline-flex w-fit items-center" aria-label="PT Metindo Perkasa">
                <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-10 w-auto">
                <span class="ms-3.5 flex flex-col border-s border-white/35 ps-3.5 text-base uppercase leading-[1.18] tracking-[0.3em]"><span class="font-extrabold">Metindo</span><span>Perkasa</span></span>
            </a>

            <p class="mt-12 text-[clamp(6rem,22vw,15rem)] font-black leading-[0.85] tracking-tight lg:pb-6" aria-hidden="true" dir="ltr">{{ $code }}</p>

            <span class="absolute inset-x-0 bottom-0 h-1.5 bg-brand-600" aria-hidden="true"></span>
        </div>

        <div class="flex items-center px-6 py-14 sm:px-12 lg:px-16">
            <div class="w-full max-w-md">
                <h1 class="text-balance text-[clamp(2rem,4.2vw,3.5rem)] font-black uppercase leading-[0.98] tracking-tight">{{ $title }}</h1>
                <p class="mt-5 text-lg leading-snug text-muted">{{ $text }}</p>

                @if ((int) $code !== 503)
                    <a href="{{ $home }}" class="group mt-10 inline-flex min-h-12 items-center gap-3 rounded-lg bg-brand-600 px-7 text-base font-bold text-white transition-colors hover:bg-brand-700">
                        {{ __('errors.home') }}
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 transition-transform group-hover:translate-x-1 rtl:rotate-180 rtl:group-hover:-translate-x-1" aria-hidden="true"><path d="M4 10h12m0 0-5-5m5 5-5 5"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
