@extends('layouts.auth')

@section('body')
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)]">
        <div class="relative hidden overflow-hidden bg-ink text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16" aria-hidden="true">
            <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="relative z-10 h-10 w-auto self-start">

            <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="pointer-events-none absolute -end-24 bottom-24 w-[46rem] max-w-none opacity-[0.07]">
            <span class="absolute inset-x-0 bottom-0 h-2 bg-brand-600"></span>

            <div class="relative z-10 max-w-md pb-6">
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-brand-300">PT Metindo Perkasa</p>
                <p class="mt-5 font-display text-5xl font-black leading-[1.02] tracking-tight xl:text-6xl">{{ __('auth.panel_title') }}</p>
                <p class="mt-5 text-lg leading-snug text-white/70">{{ __('auth.panel_text') }}</p>
            </div>
        </div>

        <div class="relative flex flex-col px-6 py-6 sm:px-12">
            <div class="flex items-center justify-between">
                <a href="{{ route('landing.home', ['locale' => 'en']) }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-bold text-ink hover:text-brand-700">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 rtl:rotate-180" aria-hidden="true"><path d="M16 10H4m0 0 5-5m-5 5 5 5"/></svg>
                    {{ __('auth.back_to_site') }}
                </a>
                <x-dashboard.locale-switcher :action="$localeAction" />
            </div>

            <div class="flex flex-1 items-center justify-center py-10">
                <div class="login-rise w-full max-w-sm">
                    <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="612" height="256" class="mb-10 h-9 w-auto lg:hidden">

                    <h1 class="text-4xl font-extrabold leading-tight tracking-tight">{{ $title }}</h1>
                    <p class="mt-3 text-base text-muted">{{ __('auth.intro') }}</p>

                    <form method="POST" action="{{ $action }}" class="mt-9 space-y-6" novalidate>
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-semibold">{{ __('auth.email') }}</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                   class="login-field mt-2 block min-h-12 w-full rounded-lg border bg-white px-4 text-base text-ink {{ $errors->has('email') ? 'border-brand-600' : 'border-ink/30' }}">
                            @error('email')
                                <p id="email-error" class="mt-2 flex items-start gap-2 text-sm font-semibold text-brand-700" role="alert">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold">{{ __('auth.password') }}</label>
                            <div class="relative mt-2">
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                       class="login-field block min-h-12 w-full rounded-lg border bg-white ps-4 pe-14 text-base text-ink {{ $errors->has('password') ? 'border-brand-600' : 'border-ink/30' }}">
                                <button type="button" data-password-toggle="password" aria-pressed="false" aria-label="{{ __('auth.show_password') }}"
                                        data-show="{{ __('auth.show_password') }}" data-hide="{{ __('auth.hide_password') }}"
                                        class="absolute inset-y-0 end-0 inline-flex w-12 items-center justify-center rounded-e-lg text-muted hover:text-ink">
                                    <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.7 8.4 2 12 2 12s3.5 7 10 7c1.7 0 3.2-.4 4.5-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="mt-2 text-sm font-semibold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <label class="flex min-h-11 w-fit items-center gap-3 text-sm font-semibold">
                            <input type="checkbox" name="remember" value="1" class="size-5 rounded border-ink/50 accent-brand-600">
                            {{ __('auth.remember') }}
                        </label>

                        <button type="submit" class="login-submit group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 text-base font-bold text-white hover:bg-brand-700">
                            {{ __('auth.submit') }}
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 transition-transform group-hover:translate-x-1 rtl:rotate-180" aria-hidden="true"><path d="M4 10h12m0 0-5-5m5 5-5 5"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
