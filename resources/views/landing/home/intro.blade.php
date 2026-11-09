{{-- Hidden by CSS until a script in <head> marks this session as new. --}}
<div id="intro" role="dialog" aria-modal="true" aria-label="PT Metindo Perkasa"
     class="fixed inset-0 z-[80] flex-col items-center justify-center gap-10 bg-ink px-6 text-center text-white" style="--focus-ring: #ffffff">
    <img data-intro-item src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-auto w-64 max-w-full sm:w-80">
    <button type="button" data-intro-close data-intro-item
            class="min-h-12 rounded-lg bg-brand-600 px-8 text-lg font-bold text-white hover:bg-brand-700">
        {{ __('home.intro.begin') }}
    </button>
</div>
