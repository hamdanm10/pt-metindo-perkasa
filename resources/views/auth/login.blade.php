@extends('layouts.auth')

@section('body')
    <main class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-brand-600 lg:block" aria-hidden="true">
            <div class="absolute inset-y-0 end-0 w-2/3 bg-ink" style="clip-path: polygon(30% 0, 100% 0, 100% 100%, 0 100%)"></div>
            <div class="absolute -bottom-20 start-1/4 size-72 rounded-full bg-accent-indigo"></div>
            <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="555" height="100" class="absolute start-12 top-12 h-9 w-auto">
        </div>

        <div class="flex items-center justify-center px-6 py-12 sm:px-12">
            <div class="w-full max-w-sm">
                <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="mb-10 h-9 w-auto lg:hidden">

                <h1 class="text-4xl font-extrabold leading-tight tracking-tight">{{ $title }}</h1>

                <form method="POST" action="{{ $action }}" class="mt-10 space-y-6" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold">{{ __('auth.email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                               class="mt-2 block min-h-12 w-full rounded-lg border border-ink/50 bg-white px-4 text-base text-ink">
                        @error('email')
                            <p id="email-error" class="mt-2 text-sm font-semibold text-brand-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold">{{ __('auth.password') }}</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                               class="mt-2 block min-h-12 w-full rounded-lg border border-ink/50 bg-white px-4 text-base text-ink">
                        @error('password')
                            <p id="password-error" class="mt-2 text-sm font-semibold text-brand-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex min-h-11 items-center gap-3 text-sm font-semibold">
                        <input type="checkbox" name="remember" value="1" class="size-5 rounded border-ink/50 accent-brand-600">
                        {{ __('auth.remember') }}
                    </label>

                    <button type="submit" class="min-h-12 w-full rounded-lg bg-brand-600 px-6 text-base font-bold text-white hover:bg-brand-700">
                        {{ __('auth.submit') }}
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection
