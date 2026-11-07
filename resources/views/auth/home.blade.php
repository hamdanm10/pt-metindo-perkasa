@extends('layouts.auth', ['title' => 'Dashboard'])

@section('body')
    <main class="flex min-h-screen items-center justify-center px-6">
        <div class="w-full max-w-md text-center">
            <h1 class="text-3xl font-extrabold">{{ __('auth.signed_in_as', ['name' => auth()->user()?->name]) }}</h1>
            <p class="mt-3 text-muted">{{ __('auth.dashboard_soon') }}</p>

            <form method="POST" action="{{ $logout }}" class="mt-8">
                @csrf
                <button type="submit" class="min-h-12 rounded-lg bg-ink px-6 font-bold text-white hover:bg-brand-700">{{ __('auth.logout') }}</button>
            </form>
        </div>
    </main>
@endsection
