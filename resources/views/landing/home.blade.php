@extends('landing.layouts.app')

@push('head')
    <script>
        try {
            if (!sessionStorage.getItem('intro-seen')) {
                document.documentElement.classList.add('intro-pending');
            }
        } catch (e) {}
    </script>
@endpush

@section('content')
    @include('landing.home.intro')
    @include('landing.home.hero')
    @include('landing.home.video')
    @include('landing.home.catalog')
    @include('landing.home.services')
    @include('landing.home.clients')
    @include('landing.home.articles')
    <x-landing.cta />
@endsection
