@props(['action'])

@php
    $current = app()->getLocale();
    $currentLocale = config("locales.locales.{$current}");
@endphp

<details class="relative" data-dismissible>
    <summary class="dd-summary inline-flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-lg bg-ink px-4 text-sm font-bold text-white transition-colors hover:bg-ink/85 [&::-webkit-details-marker]:hidden"
             style="--focus-ring: #ffffff" aria-label="{{ __('dashboard.language') }}">
        <span class="hidden sm:inline" lang="{{ $currentLocale['html_lang'] }}">{{ $currentLocale['name'] }}</span>
        <span class="sm:hidden">{{ strtoupper($current) }}</span>
        <svg viewBox="0 0 20 20" class="dd-chevron size-4" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
    </summary>
    <div class="dd-panel absolute end-0 top-full z-40 mt-2 w-52 rounded-lg bg-ink p-2 text-white shadow-lg" style="--focus-ring: #ffffff">
        <form method="POST" action="{{ $action }}" aria-label="{{ __('dashboard.language') }}">
            @csrf
            @foreach (config('locales.dashboard') as $code)
                @php($locale = config("locales.locales.{$code}"))
                <button type="submit" name="locale" value="{{ $code }}" lang="{{ $locale['html_lang'] }}"
                        @if ($code === $current) aria-current="true" @endif
                        class="flex min-h-11 w-full items-center justify-between rounded-md px-3 text-start text-sm font-bold text-white/80 hover:bg-white/10 hover:text-white aria-[current=true]:bg-white/10 aria-[current=true]:text-white">
                    <span>{{ $locale['name'] }}</span>
                    @if ($code === $current)
                        <svg viewBox="0 0 20 20" class="size-4 text-brand-300" fill="currentColor" aria-hidden="true"><path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z"/></svg>
                    @endif
                </button>
            @endforeach
        </form>
    </div>
</details>
