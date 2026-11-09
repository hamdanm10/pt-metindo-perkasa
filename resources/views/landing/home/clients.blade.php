@php($sectors = ['automotive', 'electronics', 'machinery', 'construction', 'furniture_hardware', 'oil_gas'])

<section class="bg-lavender">
    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="max-w-3xl">
            <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,6vw,5rem)] font-black uppercase leading-[0.98] tracking-tight text-ink">{{ __('home.clients.title') }}</h2>
            <p class="mt-5 text-lg text-muted sm:text-xl">{{ __('home.clients.text') }}</p>
        </div>

        <h3 class="mt-14 text-xl font-bold text-ink">{{ __('home.clients.clients') }}</h3>
        <ul data-anim="rows" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($sectors as $sector)
                <li class="flex min-h-28 items-center rounded-lg border border-ink/20 bg-white px-6 text-xl font-bold text-ink">
                    {{ __("home.clients.sectors.{$sector}") }}
                </li>
            @endforeach
        </ul>

        <h3 class="mt-14 text-xl font-bold text-ink">{{ __('home.clients.partners') }}</h3>
        <ul data-anim="rows" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (config('landing.partners') as $partner)
                <li class="flex min-h-28 items-center rounded-lg border border-ink/20 bg-white px-6 text-xl font-bold text-ink" lang="en" dir="ltr">{{ $partner }}</li>
            @endforeach
        </ul>
    </div>
</section>
