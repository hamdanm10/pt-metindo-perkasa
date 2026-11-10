@extends('layouts.auth')

@section('body')
    <main class="grid min-h-screen lg:grid-cols-[5fr_6fr]">
        <div class="relative flex flex-col justify-between overflow-hidden bg-ink px-6 py-8 text-white sm:px-12 lg:p-14" style="--focus-ring: #ffffff">
            <a href="{{ route('landing.home', ['locale' => 'en']) }}" class="brand-lockup inline-flex w-fit items-center" aria-label="{{ __('auth.back_to_site') }}">
                <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-10 w-auto">
                <span class="brand-lockup__name"><span class="font-extrabold">Metindo</span> <span>Perkasa</span></span>
            </a>

            <div class="login-rise max-lg:mt-12 lg:pb-6">
                <p class="text-[clamp(3rem,7.4vw,8.5rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('auth.panel_title') }}</p>
                <p class="mt-6 max-w-[26em] text-lg leading-snug text-white/80">{{ __('auth.panel_text') }}</p>
            </div>

            <span class="absolute inset-x-0 bottom-0 h-1.5 bg-brand-600" aria-hidden="true"></span>
        </div>

        <div class="flex flex-col bg-lavender px-6 py-6 sm:px-12 lg:px-16">
            <div class="flex items-center justify-end">
                <x-dashboard.locale-switcher :action="$localeAction" />
            </div>

            <div class="flex flex-1 items-center justify-center py-12">
                <div class="w-full max-w-md">
                    <h1 class="text-4xl font-black uppercase leading-none tracking-tight">{{ $title }}</h1>
                    <p class="mt-3 text-base text-muted">{{ __('auth.intro') }}</p>

                    <form method="POST" action="{{ $action }}" class="mt-10 space-y-8" novalidate data-login-form>
                        @csrf

                        <div class="field-group">
                            <label for="email" class="field-label">{{ __('auth.email') }}</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email" class="field-input"
                                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            @error('email')
                                <p id="email-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="password" class="field-label">{{ __('auth.password') }}</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required autocomplete="current-password" class="field-input pe-12"
                                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                                <button type="button" data-password-toggle="password" aria-pressed="false" aria-label="{{ __('auth.show_password') }}"
                                        data-show="{{ __('auth.show_password') }}" data-hide="{{ __('auth.hide_password') }}"
                                        class="absolute bottom-1 end-0 inline-flex size-11 items-center justify-center text-muted hover:text-ink">
                                    <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.7 8.4 2 12 2 12s3.5 7 10 7c1.7 0 3.2-.4 4.5-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                                </button>
                            </div>
                            <p data-caps hidden class="mt-2 text-sm font-bold text-ink" role="status">{{ __('auth.caps_lock') }}</p>
                            @error('password')
                                <p id="password-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <label class="flex min-h-11 w-fit cursor-pointer items-center gap-3 text-sm font-bold">
                            <input type="checkbox" name="remember" value="1" class="size-5 rounded border-ink/40 accent-brand-600">
                            {{ __('auth.remember') }}
                        </label>

                        <button type="submit" data-busy-label="{{ __('auth.submitting') }}"
                                class="login-submit group inline-flex min-h-12 items-center gap-3 rounded-lg bg-brand-600 px-7 text-base font-bold text-white transition-colors hover:bg-brand-700">
                            <span data-submit-text>{{ __('auth.submit') }}</span>
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 transition-transform group-hover:translate-x-1 rtl:rotate-180" aria-hidden="true"><path d="M4 10h12m0 0-5-5m5 5-5 5"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <p class="pb-2 text-xs text-muted">© {{ now()->year }} PT Metindo Perkasa</p>
        </div>
    </main>
@endsection
