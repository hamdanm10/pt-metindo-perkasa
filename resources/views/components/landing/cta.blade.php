@php($email = config('landing.contact.email'))

@if ($email)
    <section class="bg-brand-600 text-white" style="--focus-ring: #ffffff">
        <div class="container-site section-y">
            <h2 data-anim="grow" class="max-w-4xl text-balance text-[clamp(2.5rem,7vw,5.5rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('landing.cta.title') }}</h2>
            <p class="mt-6 max-w-xl text-lg sm:text-xl">{{ __('landing.cta.text') }}</p>
            <a href="mailto:{{ $email }}" class="mt-10 inline-flex min-h-12 items-center gap-2 rounded-lg bg-white px-7 font-bold text-ink hover:bg-lavender">
                {{ __('landing.cta.button') }}<x-landing.arrow />
            </a>
        </div>
    </section>
@endif
