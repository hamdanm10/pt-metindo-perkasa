@extends('landing.layouts.app')

@section('title', __('landing.nav.contact'))

@section('content')
    <x-landing.page-header :title="__('pages.contact.title')" :intro="__('pages.contact.intro')" />

    <section class="bg-white pb-20 lg:pb-28">
        <div class="mx-auto max-w-6xl px-4 pt-16 sm:px-6 lg:px-8 lg:pt-24">
            @if ($contact)
                <dl class="grid gap-x-16 sm:grid-cols-2">
                    @isset($contact['address'])
                        <div class="border-t border-ink/20 py-6">
                            <dt class="font-semibold text-muted">{{ __('pages.contact.address') }}</dt>
                            <dd class="mt-2 text-2xl font-bold text-ink" dir="auto">{{ $contact['address'] }}</dd>
                        </div>
                    @endisset
                    @isset($contact['hours'])
                        <div class="border-t border-ink/20 py-6">
                            <dt class="font-semibold text-muted">{{ __('pages.contact.hours') }}</dt>
                            <dd class="mt-2 text-2xl font-bold text-ink" dir="auto">{{ $contact['hours'] }}</dd>
                        </div>
                    @endisset
                    @isset($contact['phone'])
                        <div class="border-t border-ink/20 py-6">
                            <dt class="font-semibold text-muted">{{ __('pages.contact.phone') }}</dt>
                            <dd class="mt-2 text-2xl font-bold text-ink" dir="ltr">
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $contact['phone']) }}" class="inline-flex min-h-11 items-center hover:text-brand-700">{{ $contact['phone'] }}</a>
                            </dd>
                        </div>
                    @endisset
                    @isset($contact['email'])
                        <div class="border-t border-ink/20 py-6">
                            <dt class="font-semibold text-muted">{{ __('pages.contact.email') }}</dt>
                            <dd class="mt-2 break-all text-2xl font-bold text-ink" dir="ltr">
                                <a href="mailto:{{ $contact['email'] }}" class="inline-flex min-h-11 items-center hover:text-brand-700">{{ $contact['email'] }}</a>
                            </dd>
                        </div>
                    @endisset
                </dl>

                @isset($contact['whatsapp'])
                    <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener noreferrer"
                       class="mt-10 inline-flex min-h-12 items-center rounded-lg bg-ink px-7 font-bold text-white hover:bg-brand-700">
                        {{ __('pages.contact.whatsapp') }}
                    </a>
                @endisset
            @else
                <p class="text-xl text-muted">{{ __('pages.contact.empty') }}</p>
            @endif
        </div>
    </section>
@endsection
