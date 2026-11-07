@php($email = config('landing.contact.email'))

@if ($email)
    <section class="bg-white pb-20 pt-16 lg:pb-28 lg:pt-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-brand-600 px-8 py-14 text-white sm:px-14 sm:py-20 lg:rounded-ss-[6rem]">
                <h2 class="max-w-2xl text-balance text-3xl font-bold leading-tight sm:text-5xl">{{ __('landing.cta.title') }}</h2>
                <p class="mt-5 max-w-xl text-lg text-white">{{ __('landing.cta.text') }}</p>
                <a href="mailto:{{ $email }}" class="mt-9 inline-flex min-h-12 items-center rounded-lg bg-white px-7 font-bold text-brand-700 hover:bg-lavender focus-visible:outline-white">
                    {{ __('landing.cta.button') }}
                </a>
            </div>
        </div>
    </section>
@endif
