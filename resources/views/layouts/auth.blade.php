<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | PT Metindo Perkasa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/dashboard.css', 'resources/css/fonts/latin.css'])
</head>
<body class="min-h-screen bg-white font-sans text-ink antialiased">
    @yield('body')
</body>
</html>
