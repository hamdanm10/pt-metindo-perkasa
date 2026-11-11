@extends('landing.layouts.app')

@section('title', __('landing.nav.contact'))

@section('content')
    @php
        $email = $contact['email'] ?? null;
        $phone = $contact['phone'] ?? null;
        $phoneLink = $phone ? preg_replace('/[^+\d]/', '', $phone) : null;
        $address = $contact['address'] ?? null;
        $hours = $contact['hours'] ?? null;
        $first = $agents[0] ?? null;
        $canSend = $first !== null || $email !== null;
        $tile = 'contact-tile group relative flex min-h-[clamp(6.5rem,8vw,10rem)] flex-col justify-between overflow-hidden rounded-lg border border-ink/15 bg-white p-[clamp(1.1rem,1.6vw,2rem)] transition-colors duration-500 hover:border-ink hover:bg-ink hover:text-white focus-within:border-ink focus-within:bg-ink focus-within:text-white';
        $label = 'text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-brand-600 transition-colors duration-500 group-hover:text-brand-300 group-focus-within:text-brand-300';
        $value = 'mt-3 block text-[clamp(1.15rem,1.7vw,2.1rem)] font-black leading-tight tracking-tight';
        $copy = 'absolute end-[clamp(1rem,1.4vw,1.75rem)] top-[clamp(0.9rem,1.2vw,1.5rem)] z-10 inline-flex min-h-9 items-center rounded-full border border-current/30 px-3 text-xs font-bold transition-colors hover:bg-white hover:text-ink';
    @endphp

    {{-- Opening: the headline, the intro, and the quickest ways to reach us as large buttons. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(2.5rem,6vw,7.5rem)]">
            <h1 data-anim="hero-title" class="max-w-[12em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('pages.contact.title') }}</h1>
            <div class="mt-[3vw] grid items-end gap-8 max-lg:mt-8 lg:grid-cols-[1fr_auto]">
                <p data-anim="hero-item" class="max-w-[34em] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85">{{ __('pages.contact.intro') }}</p>
                @if ($contact)
                    <div data-anim="hero-item" class="flex flex-wrap gap-4">
                        @isset($contact['whatsapp'])
                            <x-landing.button :href="'https://wa.me/'.$contact['whatsapp']" target="_blank" rel="noopener noreferrer">{{ __('pages.contact.whatsapp') }}</x-landing.button>
                        @endisset
                        @if ($phoneLink)
                            <x-landing.button :href="'tel:'.$phoneLink" variant="outline" :arrow="false">{{ __('pages.contact.call') }}</x-landing.button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($contact)
        <section class="bg-lavender text-ink">
            <div class="container-site section-y grid items-start gap-[2.5vw] max-lg:gap-10 lg:grid-cols-[5fr_7fr]">
                {{-- The details, as tiles. The ones that can be used turn dark on hover, and the whole tile is the link. --}}
                <ul data-contact-tiles class="grid gap-[1.2vw] max-lg:gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    @if ($phone)
                        <li class="{{ $tile }}">
                            <div>
                                <p class="{{ $label }}">{{ __('pages.contact.phone') }}</p>
                                <a href="tel:{{ $phoneLink }}" class="{{ $value }} before:absolute before:inset-0" dir="ltr">{{ $phone }}</a>
                            </div>
                            <button type="button" data-copy-link="{{ $phone }}" data-copied="{{ __('pages.contact.copied') }}" class="{{ $copy }}"><span data-copy-label>{{ __('pages.contact.copy') }}</span></button>
                        </li>
                    @endif
                    @if ($email)
                        <li class="{{ $tile }}">
                            <div>
                                <p class="{{ $label }}">{{ __('pages.contact.email') }}</p>
                                <a href="mailto:{{ $email }}" class="{{ $value }} break-all before:absolute before:inset-0" dir="ltr">{{ $email }}</a>
                            </div>
                            <button type="button" data-copy-link="{{ $email }}" data-copied="{{ __('pages.contact.copied') }}" class="{{ $copy }}"><span data-copy-label>{{ __('pages.contact.copy') }}</span></button>
                        </li>
                    @endif
                    @if ($address)
                        <li class="{{ $tile }}">
                            <div>
                                <p class="{{ $label }}">{{ __('pages.contact.address') }}</p>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($address) }}" target="_blank" rel="noopener noreferrer" class="{{ $value }} before:absolute before:inset-0" dir="auto">{{ $address }}</a>
                            </div>
                            <span class="inline-flex items-center gap-2 text-xs font-bold"><span class="uline">{{ __('pages.contact.open_map') }}</span><span aria-hidden="true" class="transition-transform duration-500 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1">↗</span></span>
                        </li>
                    @endif
                    @if ($hours)
                        <li class="{{ $tile }}">
                            <div>
                                <p class="{{ $label }}">{{ __('pages.contact.hours') }}</p>
                                <p class="{{ $value }}" dir="auto">{{ $hours }}</p>
                            </div>
                        </li>
                    @endif
                </ul>

                {{-- The message: written for the visitor in WhatsApp or the email app. Nothing is kept on this site. --}}
                @if ($canSend)
                    <div data-contact-card class="rounded-lg bg-white p-[clamp(1.25rem,2.6vw,3.25rem)] shadow-[0_30px_80px_-40px_rgba(27,31,59,0.45)]">
                        <h2 class="text-[clamp(1.5rem,2.6vw,3.25rem)] font-black uppercase leading-[0.98] tracking-tight">{{ __('pages.contact.form.title') }}</h2>
                        <p class="mt-[0.8vw] max-w-[34em] text-[clamp(0.9rem,1.05vw,1.3rem)] leading-snug text-ink/70 max-lg:mt-3">{{ __('pages.contact.form.intro') }}</p>

                        <form data-contact-form novalidate class="mt-[2vw] space-y-[1.6vw] max-lg:mt-6 max-lg:space-y-6"
                              data-greeting="{{ __('pages.contact.compose.greeting') }}" data-name-label="{{ __('pages.contact.compose.name') }}" data-email-label="{{ __('pages.contact.compose.email') }}" data-subject="{{ __('pages.contact.compose.subject') }}"
                              data-mail="{{ $email }}" data-required="{{ __('pages.contact.form.required') }}" data-invalid="{{ __('pages.contact.form.invalid_email') }}">
                            @foreach ([['name', 'text', 'name', 'name'], ['email', 'email', 'email', 'email']] as [$field, $type, $key, $auto])
                                <div class="field">
                                    <label for="contact-{{ $field }}" class="field__label">{{ __("pages.contact.form.{$key}") }}<span class="text-brand-600" aria-hidden="true"> *</span></label>
                                    <div class="field__control"><input id="contact-{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $auto }}" required aria-describedby="contact-{{ $field }}-error" class="field__input" @if ($field === 'email') dir="ltr" @endif></div>
                                    <p id="contact-{{ $field }}-error" data-error class="field__error" role="alert"></p>
                                </div>
                            @endforeach
                            <div class="field">
                                <label for="contact-message" class="field__label">{{ __('pages.contact.form.message') }}<span class="text-brand-600" aria-hidden="true"> *</span></label>
                                <div class="field__control"><textarea id="contact-message" name="message" rows="5" required aria-describedby="contact-message-error" class="field__input resize-none"></textarea></div>
                                <p id="contact-message-error" data-error class="field__error" role="alert"></p>
                            </div>

                            @if (count($agents) > 1)
                                <fieldset class="field">
                                    <legend class="field__label">{{ __('pages.contact.form.send_to') }}</legend>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($agents as $agent)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="agent" value="{{ $agent['number'] }}" class="peer sr-only" @checked($loop->first)>
                                                <span class="inline-flex min-h-11 items-center rounded-full border border-ink/25 px-4 text-sm font-bold transition-colors peer-checked:border-ink peer-checked:bg-ink peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2">{{ $agent['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endif

                            <div class="flex flex-wrap items-center gap-4 pt-2">
                                @if ($first)
                                    <x-landing.button :href="'https://wa.me/'.$first['number']" target="_blank" rel="noopener noreferrer" data-send="whatsapp" data-wa="{{ $first['number'] }}">{{ __('pages.contact.form.whatsapp') }}</x-landing.button>
                                @endif
                                @if ($email)
                                    <x-landing.button :href="'mailto:'.$email" variant="dark" data-send="mail">{{ __('pages.contact.form.mail') }}</x-landing.button>
                                @endif
                            </div>
                            <p data-form-status role="status" class="min-h-6 text-sm font-semibold text-brand-700"></p>
                            <p data-done hidden>{{ __('pages.contact.form.done') }}</p>
                        </form>
                    </div>
                @endif
            </div>
        </section>
    @else
        <section class="bg-lavender text-ink">
            <div class="container-site section-y">
                <p class="text-xl text-ink/70">{{ __('pages.contact.empty') }}</p>
            </div>
        </section>
    @endif

    {{-- Where to find us: the pins of the group on the map of Indonesia (sample locations from the config, shown in every mode). --}}
    @if ($subsidiaries)
        <section class="bg-ink text-white" style="--focus-ring: #ffffff">
            <div class="container-site section-y">
                <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-5">
                    <h2 data-anim="grow" class="text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.contact.find') }}</h2>
                    <p data-anim="heading" class="max-w-[30em] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.45] text-white/80">{{ __('pages.about.subsidiaries.intro') }}</p>
                </div>
                @if ($sample)
                <p class="mt-5 inline-block rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold">{{ __('landing.sample_note') }}</p>
                @endif

                <div data-map-stage class="mt-[3vw] max-lg:mt-8">
                    <x-landing.indonesia-map :label="__('pages.about.subsidiaries.map')">
                        @foreach ($subsidiaries as $subsidiary)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $subsidiary['lat'] }},{{ $subsidiary['lng'] }}" target="_blank" rel="noopener noreferrer"
                               data-pin="{{ $subsidiary['key'] }}" aria-label="{{ $subsidiary['name'] }}, {{ ucfirst($subsidiary['key']) }} — {{ __('pages.about.subsidiaries.open_map') }}"
                               style="left: {{ $subsidiary['left'] }}%; top: {{ $subsidiary['top'] }}%"
                               class="map-pin group absolute z-10 -translate-x-1/2 -translate-y-full rounded-full p-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                                <span class="map-pin__ring" aria-hidden="true"></span>
                                <svg viewBox="0 0 24 24" fill="currentColor" class="map-pin__icon relative size-6 text-brand-500 drop-shadow sm:size-8 lg:size-10" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z"/>
                                </svg>
                                <span class="map-pin__label" lang="en" dir="ltr" aria-hidden="true">{{ ucfirst($subsidiary['key']) }}</span>
                            </a>
                        @endforeach
                    </x-landing.indonesia-map>
                </div>
            </div>
        </section>
    @endif
@endsection
