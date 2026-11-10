{{-- Hidden by CSS until a script in <head> marks this session as new. The home page is already in the HTML behind it. --}}
<div id="intro" role="dialog" aria-modal="true" aria-label="PT Metindo Perkasa" tabindex="-1"
     class="fixed inset-0 z-[80] flex-col bg-brand-600 text-white outline-none" style="--focus-ring: #ffffff">
    <div class="container-site flex h-full flex-col pb-[clamp(1rem,2.2vw,2.75rem)] pt-[clamp(1rem,1.8vw,2.25rem)]">
        {{-- Top: the monogram on the left and the languages as plain text on the right. --}}
        <div class="flex items-center justify-between">
            <img data-intro-item data-intro-logo src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-[clamp(2rem,3.2vw,4rem)] w-auto">
            <nav data-intro-item aria-label="{{ __('landing.language') }}">
                <ul class="flex items-center gap-[clamp(0.9rem,1.6vw,2rem)] text-[clamp(0.8rem,0.95vw,1.15rem)] font-extrabold uppercase">
                    @foreach (config('locales.landing') as $code)
                        @php($locale = config("locales.locales.{$code}"))
                        <li>
                            <a href="{{ route('landing.home', ['locale' => $code]) }}" lang="{{ $locale['html_lang'] }}" hreflang="{{ $locale['html_lang'] }}" title="{{ $locale['name'] }}" @if ($code === app()->getLocale()) aria-current="true" @endif
                               class="inline-flex min-h-11 items-center {{ $code === app()->getLocale() ? '' : 'text-white/70 hover:text-white' }}"><span class="uline">{{ $code }}</span></a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        {{-- The headline of the home page, very large, each line rising out of its own mask. --}}
        <p class="flex flex-1 flex-col justify-end pb-[clamp(1.25rem,2.5vw,3rem)] text-[clamp(3.25rem,11.5vw,14rem)] font-black uppercase leading-[0.88] tracking-tight" aria-hidden="true">
            @foreach (explode(' ', __('home.hero.title')) as $word)
                <span class="block overflow-hidden"><span data-intro-word class="block">{{ $word }}</span></span>
            @endforeach
        </p>

        {{-- The way in: one wide bar. --}}
        <button type="button" data-intro-close data-intro-item
                class="intro-bar group flex h-[clamp(4rem,7.5vw,9.5rem)] w-full items-center justify-between rounded-lg bg-ink px-[clamp(1.25rem,2.6vw,3.25rem)] text-start text-white focus-visible:outline-offset-4">
            <span class="relative block overflow-hidden text-[clamp(1.15rem,3vw,3.75rem)] font-black uppercase leading-none">
                <span class="block transition-transform duration-500 [transition-timing-function:var(--header-ease)] group-hover:-translate-y-full">{{ __('home.intro.begin') }}</span>
                <span class="absolute inset-0 block translate-y-full text-brand-300 transition-transform duration-500 [transition-timing-function:var(--header-ease)] group-hover:translate-y-0" aria-hidden="true">{{ __('home.intro.begin') }}</span>
            </span>
            <svg viewBox="0 0 21 20" fill="none" class="size-[clamp(1.5rem,3vw,3.75rem)] shrink-0 transition-transform duration-500 [transition-timing-function:var(--header-ease)] group-hover:translate-x-2 rtl:-scale-x-100 rtl:group-hover:-translate-x-2" aria-hidden="true"><path d="M10.0691 2L17.9004 10.1014L10.0691 17.6626" stroke="currentColor" stroke-width="3"/><path d="M0 9.83154H16.7814" stroke="currentColor" stroke-width="3"/></svg>
        </button>
    </div>
</div>
