@php($services = ['electroplating', 'chemical_supply', 'consultation'])

<section class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="container-site section-y">
        <div class="max-w-3xl">
            <h2 data-anim="heading" class="text-[clamp(2.25rem,5vw,6rem)] font-black uppercase leading-[0.98] tracking-tight">{{ __('home.services.title') }}</h2>
            <p class="mt-5 text-lg text-white/85 sm:text-xl">{{ __('home.services.intro') }}</p>
        </div>

        <div data-anim="rows" class="mt-14">
            @foreach ($services as $service)
                <div class="grid gap-6 border-t border-white/20 py-10 lg:grid-cols-12 lg:gap-10">
                    <span class="text-5xl font-black text-brand-400 lg:col-span-2" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="lg:col-span-6">
                        <h3 class="text-2xl font-bold sm:text-4xl">{{ __("home.services.items.{$service}.title") }}</h3>
                        <p class="mt-4 max-w-xl text-lg text-white/85">{{ __("home.services.items.{$service}.text") }}</p>
                    </div>
                    <div class="lg:col-span-4 lg:text-end">
                        <a href="{{ route('landing.services') }}" class="inline-flex min-h-12 items-center gap-2 rounded-lg bg-brand-600 px-6 font-bold text-white hover:bg-brand-700">
                            {{ __('home.services.all') }}<x-landing.arrow />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
