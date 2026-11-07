@extends('landing.layouts.app')

@section('content')
    {{-- Full home page lands in phase L2. --}}
    <section class="bg-lavender">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <h1 class="max-w-3xl text-balance text-4xl font-bold leading-tight text-ink sm:text-5xl">
                {{ __('landing.home.heading') }}
            </h1>
        </div>
    </section>
@endsection
