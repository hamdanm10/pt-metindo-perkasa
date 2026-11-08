@props(['action'])

@php($current = app()->getLocale())

<form method="POST" action="{{ $action }}" role="group" aria-label="{{ __('dashboard.language') }}" {{ $attributes->class('inline-flex items-center rounded-lg bg-lavender p-1') }}>
    @csrf
    @foreach (config('locales.dashboard') as $code)
        @php($locale = config("locales.locales.{$code}"))
        <button type="submit" name="locale" value="{{ $code }}" lang="{{ $locale['html_lang'] }}" aria-label="{{ $locale['name'] }}"
                @if ($code === $current) aria-current="true" @endif
                class="min-h-11 min-w-11 rounded-md px-3 text-sm font-bold {{ $code === $current ? 'bg-ink text-white' : 'text-ink hover:bg-white' }}">
            {{ strtoupper($code) }}
        </button>
    @endforeach
</form>
