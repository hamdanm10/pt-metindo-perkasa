@php($services = ['electroplating', 'chemical_supply', 'consultation'])

<section class="bg-lavender">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold text-ink sm:text-4xl">{{ __('home.services.title') }}</h2>
            <p class="mt-4 text-lg text-muted">{{ __('home.services.intro') }}</p>
        </div>

        <div class="mt-12 grid gap-5 lg:grid-cols-5">
            @foreach ($services as $service)
                @if ($loop->first)
                    <div class="rounded-3xl bg-brand-600 p-8 text-white sm:p-10 lg:col-span-3 lg:row-span-2 lg:rounded-ss-[5rem] lg:p-12">
                        <h3 class="text-3xl font-bold sm:text-4xl">{{ __("home.services.items.{$service}.title") }}</h3>
                        <p class="mt-5 max-w-md text-lg text-white">{{ __("home.services.items.{$service}.text") }}</p>
                    </div>
                @else
                    <div class="rounded-3xl bg-white p-8 lg:col-span-2">
                        <h3 class="text-xl font-bold text-ink sm:text-2xl">{{ __("home.services.items.{$service}.title") }}</h3>
                        <p class="mt-3 text-muted">{{ __("home.services.items.{$service}.text") }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
