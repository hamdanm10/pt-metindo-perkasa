@extends('layouts.auth')

@section('body')
    <main class="relative min-h-screen overflow-hidden">
        {{-- Dark band with a spec-sheet grid; the card overlaps its lower edge. --}}
        <div class="login-band-in absolute inset-x-0 top-0 h-[26rem] bg-ink sm:h-[30rem] lg:h-[max(41rem,68vh)]" aria-hidden="true">
            <div class="login-grid absolute inset-0"></div>
            <span class="absolute inset-x-0 bottom-0 h-1.5 bg-brand-600"></span>
        </div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-5 py-6 sm:px-8">
            <header class="flex items-center justify-between gap-4 text-white" style="--focus-ring: #ffffff">
                <a href="{{ route('landing.home', ['locale' => 'en']) }}" class="inline-flex min-h-11 items-center gap-4" aria-label="{{ __('auth.back_to_site') }}">
                    <img src="{{ asset('images/brand/logo-white.svg') }}" alt="PT Metindo Perkasa" width="612" height="256" class="h-9 w-auto">
                </a>
                <x-dashboard.locale-switcher :action="$localeAction" />
            </header>

            <div class="flex flex-1 items-center py-10">
                <div class="grid w-full items-center gap-10 lg:grid-cols-[1fr_28rem] lg:gap-20">
                    <div class="hidden text-white lg:block" style="--focus-ring: #ffffff">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand-300">PT Metindo Perkasa</p>
                        <p class="mt-5 text-6xl font-bold leading-[0.98] tracking-[-0.04em] xl:text-7xl">{{ __('auth.panel_title') }}</p>
                        <p class="mt-6 max-w-md text-lg leading-snug text-white/70">{{ __('auth.panel_text') }}</p>
                    </div>

                    <div class="login-rise rounded-2xl bg-white p-7 shadow-[0_30px_80px_-30px_rgb(27_31_59/0.45)] ring-1 ring-ink/5 sm:p-10">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700 lg:hidden">PT Metindo Perkasa</p>
                        <h1 class="mt-2 text-3xl font-bold leading-tight lg:mt-0">{{ $title }}</h1>
                        <p class="mt-2 text-base text-muted">{{ __('auth.intro') }}</p>

                        <form method="POST" action="{{ $action }}" class="mt-8 space-y-5" novalidate data-login-form>
                            @csrf

                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-muted">{{ __('auth.email') }}</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email"
                                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                       class="login-field mt-2 block min-h-[3.25rem] w-full rounded-xl border-2 bg-lavender/60 px-4 text-base font-medium text-ink {{ $errors->has('email') ? 'border-brand-600' : 'border-transparent' }}">
                                @error('email')
                                    <p id="email-error" class="mt-2 flex items-start gap-2 text-sm font-semibold text-brand-700" role="alert">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-muted">{{ __('auth.password') }}</label>
                                <div class="relative mt-2">
                                    <input id="password" name="password" type="password" required autocomplete="current-password"
                                           @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                           class="login-field block min-h-[3.25rem] w-full rounded-xl border-2 bg-lavender/60 ps-4 pe-14 text-base font-medium text-ink {{ $errors->has('password') ? 'border-brand-600' : 'border-transparent' }}">
                                    <button type="button" data-password-toggle="password" aria-pressed="false" aria-label="{{ __('auth.show_password') }}"
                                            data-show="{{ __('auth.show_password') }}" data-hide="{{ __('auth.hide_password') }}"
                                            class="absolute inset-y-0 end-0 inline-flex w-12 items-center justify-center rounded-e-xl text-muted hover:text-ink">
                                        <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.7 8.4 2 12 2 12s3.5 7 10 7c1.7 0 3.2-.4 4.5-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                                    </button>
                                </div>
                                <p data-caps hidden class="mt-2 flex items-center gap-2 text-sm font-semibold text-ink" role="status">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-accent-indigo" aria-hidden="true"><path d="M10 2 3 10h4v4h6v-4h4L10 2Zm-3 14h6v2H7v-2Z"/></svg>
                                    {{ __('auth.caps_lock') }}
                                </p>
                                @error('password')
                                    <p id="password-error" class="mt-2 text-sm font-semibold text-brand-700" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex min-h-11 w-fit cursor-pointer items-center gap-3 text-sm font-semibold">
                                <input type="checkbox" name="remember" value="1" class="size-5 rounded-md border-ink/40 accent-ink">
                                {{ __('auth.remember') }}
                            </label>

                            <button type="submit" data-label="{{ __('auth.submit') }}" data-busy-label="{{ __('auth.submitting') }}"
                                    class="login-submit group inline-flex min-h-14 w-full items-center justify-between rounded-xl bg-ink px-6 text-base font-bold text-white transition-colors hover:bg-brand-600">
                                <span data-submit-text>{{ __('auth.submit') }}</span>
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 transition-transform group-hover:translate-x-1 rtl:rotate-180" aria-hidden="true"><path d="M4 10h12m0 0-5-5m5 5-5 5"/></svg>
                            </button>
                        </form>

                        <a href="{{ route('landing.home', ['locale' => 'en']) }}" class="mt-6 inline-flex min-h-11 items-center gap-2 text-sm font-bold text-muted hover:text-ink">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 rtl:rotate-180" aria-hidden="true"><path d="M16 10H4m0 0 5-5m-5 5 5 5"/></svg>
                            {{ __('auth.back_to_site') }}
                        </a>
                    </div>
                </div>
            </div>

            <p class="pb-2 text-center text-xs text-muted">© {{ now()->year }} PT Metindo Perkasa</p>
        </div>
    </main>
@endsection
