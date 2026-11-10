@php
    $content = app(\App\Services\Landing\LandingContent::class);
    $menu = $content->menu();
    $offices = $content->subsidiaries();
    $contact = config('landing.contact');
    $email = $contact['email'] ?? null;
    $phone = $contact['phone'] ?? null;
    $address = $contact['address'] ?? null;
    $hours = $contact['hours'] ?? null;
    $heading = 'text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-brand-300';
    $link = 'text-[clamp(0.75rem,0.833vw,1.05rem)] leading-[1.35]';
    $label = 'text-[clamp(0.75rem,0.833vw,1.05rem)] text-white/55';
@endphp

{{-- Footer, as on the reference: a name across the full width, a line with a back-to-top badge on it, the company text with a button, then the links and contact details in columns. The reference also has a newsletter form and a logo marquee; neither is here. --}}
<footer class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site">
        <div class="py-[clamp(2.5rem,6.67vw,8rem)]" lang="en" dir="ltr">
            <svg class="footer-wordmark block h-auto w-full" viewBox="0 0 1344 92" role="img" aria-label="PT Metindo Perkasa" focusable="false">
                <text x="0" y="88" textLength="1344" lengthAdjust="spacingAndGlyphs" font-size="126" font-weight="800" fill="currentColor" style="text-transform: uppercase">Metindo Perkasa</text>
            </svg>
        </div>
    </div>

    <div class="relative flex items-center">
        <hr class="w-full border-0 border-t border-white/20">
        <button type="button" data-to-top aria-label="{{ __('landing.footer.top') }}" class="footer-top absolute start-1/2 top-1/2 flex h-[clamp(3.5rem,5.55vw,7rem)] w-[clamp(3.1rem,5vw,6.3rem)] -translate-y-1/2 items-center justify-center bg-brand-600 hover:bg-brand-700 ltr:-translate-x-1/2 rtl:translate-x-1/2">
            <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-[38%] w-auto">
        </button>
    </div>

    <div class="container-site">
        <div class="mt-[clamp(3rem,6.67vw,8rem)] grid gap-8 lg:grid-cols-[53.57%_1fr]">
            <div>
                <p class="max-w-[38.9vw] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.42] max-lg:max-w-xl">{{ __('landing.footer.description') }}</p>
                <x-landing.button :href="route('landing.contact')" class="mt-[1.7vw] max-lg:mt-6">{{ __('landing.talk') }}</x-landing.button>
            </div>
        </div>

        <div class="mt-[clamp(2.5rem,4.45vw,5.5rem)] grid gap-y-10 lg:grid-cols-[25fr_25fr_43.33fr]">
            <nav aria-label="{{ __('landing.menu') }}">
                <h2 class="{{ $heading }}">{{ __('landing.footer.menu') }}</h2>
                <ul class="mt-[0.9vw] space-y-[0.35vw] max-lg:mt-3 max-lg:space-y-2">
                    @foreach ($menu as $item)
                        <li><a href="{{ route($item['route']) }}" class="{{ $link }} inline-flex"><span class="uline">{{ __($item['label']) }}</span></a></li>
                    @endforeach
                </ul>
            </nav>

            <div class="max-lg:hidden"></div>

            @if ($email || $phone || $address || $hours || $offices)
                <div>
                    <h2 class="{{ $heading }}">{{ __('landing.footer.contact') }}</h2>
                    <dl class="mt-[0.9vw] grid gap-x-[1.5vw] gap-y-[1.6vw] max-lg:mt-3 max-lg:gap-y-5 sm:grid-cols-2">
                        @if ($email)
                            <div><dt class="{{ $label }}">{{ __('pages.contact.email') }}</dt><dd><a href="mailto:{{ $email }}" class="{{ $link }}" dir="ltr"><span class="uline">{{ $email }}</span></a></dd></div>
                        @endif
                        @if ($phone)
                            <div><dt class="{{ $label }}">{{ __('pages.contact.phone') }}</dt><dd><a href="tel:{{ preg_replace('/[^+\d]/', '', $phone) }}" class="{{ $link }}" dir="ltr"><span class="uline">{{ $phone }}</span></a></dd></div>
                        @endif
                        @if ($address)
                            <div><dt class="{{ $label }}">{{ __('pages.contact.address') }}</dt><dd class="{{ $link }}">{{ $address }}</dd></div>
                        @endif
                        @if ($hours)
                            <div><dt class="{{ $label }}">{{ __('pages.contact.hours') }}</dt><dd class="{{ $link }}">{{ $hours }}</dd></div>
                        @endif
                        @foreach ($offices as $office)
                            <div lang="en" dir="ltr"><dt class="{{ $label }}">{{ ucfirst($office['key']) }}</dt><dd class="{{ $link }}">{{ $office['name'] }}</dd></div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>

        <div class="mt-[clamp(2.5rem,5.5vw,6.5rem)] flex flex-col gap-2 pb-[clamp(1.5rem,2.8vw,3.5rem)] {{ $label }} sm:flex-row sm:justify-between">
            <p>&copy; {{ now()->year }} PT Metindo Perkasa. {{ __('landing.footer.rights') }}</p>
            <p>{{ __('landing.footer.tagline') }}</p>
        </div>
    </div>
</footer>
