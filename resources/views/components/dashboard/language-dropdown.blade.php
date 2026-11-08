@props(['action'])

@php
    $current = app()->getLocale();
    $currentLocale = config("locales.locales.{$current}");
@endphp

<details class="relative" data-dismissible>
    <summary class="inline-flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-lg px-3 text-sm font-bold hover:bg-lavender [&::-webkit-details-marker]:hidden"
             aria-label="{{ __('dashboard.language') }}">
        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z"/></svg>
        <span class="hidden sm:inline" lang="{{ $currentLocale['html_lang'] }}">{{ $currentLocale['name'] }}</span>
        <span class="sm:hidden">{{ strtoupper($current) }}</span>
        <svg viewBox="0 0 20 20" class="size-4" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
    </summary>
    <div class="absolute end-0 top-full z-40 mt-2 w-52 rounded-2xl bg-white p-2 shadow-xl ring-1 ring-ink/10">
        <form method="POST" action="{{ $action }}" aria-label="{{ __('dashboard.language') }}">
            @csrf
            @foreach (config('locales.dashboard') as $code)
                @php($locale = config("locales.locales.{$code}"))
                <button type="submit" name="locale" value="{{ $code }}" lang="{{ $locale['html_lang'] }}"
                        @if ($code === $current) aria-current="true" @endif
                        class="flex min-h-11 w-full items-center justify-between rounded-lg px-3 text-start text-sm font-semibold hover:bg-lavender aria-[current=true]:bg-lavender aria-[current=true]:font-bold aria-[current=true]:text-brand-700">
                    <span>{{ $locale['name'] }}</span>
                    @if ($code === $current)
                        <svg viewBox="0 0 20 20" class="size-4" fill="currentColor" aria-hidden="true"><path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z"/></svg>
                    @endif
                </button>
            @endforeach
        </form>
    </div>
</details>
