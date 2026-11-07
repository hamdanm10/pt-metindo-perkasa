@php($sectors = ['automotive', 'electronics', 'machinery', 'construction', 'furniture_hardware', 'oil_gas'])

<section class="cut-top bg-white">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="max-w-2xl">
            <h2 data-anim="heading" class="text-3xl font-bold text-ink sm:text-5xl lg:text-6xl">{{ __('home.clients.title') }}</h2>
            <p class="mt-4 text-lg text-muted">{{ __('home.clients.text') }}</p>
        </div>

        <div class="mt-14 grid gap-14 lg:grid-cols-12 lg:gap-10">
            <div class="lg:col-span-7">
                <h3 class="text-xl font-bold text-ink">{{ __('home.clients.clients') }}</h3>
                <ul data-anim="rows" class="mt-6 flex flex-wrap gap-3">
                    @foreach ($sectors as $sector)
                        <li class="rounded-lg border border-ink/30 px-5 py-3 text-lg font-semibold text-ink">
                            {{ __("home.clients.sectors.{$sector}") }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-5">
                <h3 class="text-xl font-bold text-ink">{{ __('home.clients.partners') }}</h3>
                <ul data-anim="rows" class="mt-6 space-y-3">
                    @foreach (config('landing.partners') as $partner)
                        <li class="border-b border-ink/15 pb-3 text-2xl font-bold text-ink" lang="en" dir="ltr">{{ $partner }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
