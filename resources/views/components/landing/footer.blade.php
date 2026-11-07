@php
    $menu = app(\App\Services\Landing\LandingContent::class)->menu();
    $email = config('landing.contact.email');
    $phone = config('landing.contact.phone');
@endphp

<footer class="cut-top bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-5">
            <img src="{{ asset('images/brand/logo-white.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-8 w-auto">
            <p class="mt-5 max-w-md text-white/80">{{ __('landing.footer.description') }}</p>
        </div>

        <nav class="lg:col-span-3" aria-label="{{ __('landing.menu') }}">
            <ul class="space-y-1">
                @foreach ($menu as $item)
                    <li><a href="{{ route($item['route']) }}" class="inline-flex min-h-11 items-center underline-offset-4 hover:underline">{{ __($item['label']) }}</a></li>
                @endforeach
            </ul>
        </nav>

        @if ($email || $phone)
            <div class="lg:col-span-4">
                <h2 class="text-lg font-bold">{{ __('landing.footer.contact') }}</h2>
                <ul class="mt-4 space-y-1">
                    @if ($email)
                        <li><a href="mailto:{{ $email }}" class="inline-flex min-h-11 items-center underline underline-offset-4" dir="ltr">{{ $email }}</a></li>
                    @endif
                    @if ($phone)
                        <li><a href="tel:{{ preg_replace('/[^+\d]/', '', $phone) }}" class="inline-flex min-h-11 items-center underline underline-offset-4" dir="ltr">{{ $phone }}</a></li>
                    @endif
                </ul>
            </div>
        @endif
    </div>

    <div class="border-t border-white/20">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-white/80 sm:flex-row sm:justify-between sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} PT Metindo Perkasa. {{ __('landing.footer.rights') }}</p>
            <p>{{ __('landing.footer.tagline') }}</p>
        </div>
    </div>
</footer>
