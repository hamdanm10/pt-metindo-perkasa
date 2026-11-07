{{-- Hidden by CSS until a script in <head> marks this session as new. --}}
<div id="intro" role="dialog" aria-modal="true" aria-label="PT Metindo Perkasa"
     class="fixed inset-0 z-[80] flex-col items-center justify-center gap-10 bg-brand-600 px-6 text-center text-white">
    <img data-intro-item src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="555" height="100" class="h-auto w-72 max-w-full sm:w-96">
    <button type="button" data-intro-close data-intro-item
            class="min-h-12 rounded-lg bg-white px-8 text-lg font-bold text-brand-700 focus-visible:outline-white">
        {{ __('home.intro.begin') }}
    </button>
</div>
