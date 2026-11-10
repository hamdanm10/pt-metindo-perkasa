{{-- Hidden by CSS until a script in <head> marks this session as new. The home page is already in the HTML behind it. --}}
<div id="intro" role="dialog" aria-modal="true" aria-label="PT Metindo Perkasa"
     tabindex="-1" class="fixed inset-0 z-[80] flex-col items-center justify-center overflow-hidden bg-ink px-6 text-center text-white outline-none" style="--focus-ring: #ffffff">
    {{-- Soft crimson and indigo light drifting behind everything, and the monogram very large and faint. --}}
    <div data-intro-light class="pointer-events-none absolute -start-[15vw] -top-[20vw] size-[55vw] rounded-full bg-brand-600/25 blur-[7vw]" aria-hidden="true"></div>
    <div data-intro-light class="pointer-events-none absolute -bottom-[25vw] -end-[15vw] size-[50vw] rounded-full bg-accent-indigo/30 blur-[7vw]" aria-hidden="true"></div>
    <img data-intro-mark src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="pointer-events-none absolute left-1/2 top-1/2 w-[110vw] max-w-none -translate-x-1/2 -translate-y-1/2 opacity-[0.04]" aria-hidden="true">

    <div class="relative flex flex-1 flex-col items-center justify-center">
        <div class="flex items-center gap-[clamp(0.9rem,1.6vw,2rem)]">
            <img data-intro-item data-intro-logo src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-[clamp(3.25rem,7vw,8rem)] w-auto">
            <span data-intro-item data-intro-line class="h-[clamp(3rem,6.4vw,7.5rem)] w-px origin-top bg-white/40" aria-hidden="true"></span>
            <span data-intro-item data-intro-name class="flex flex-col text-start text-[clamp(1.1rem,2.7vw,3.25rem)] uppercase leading-[1.15]">
                <span class="overflow-hidden"><span data-intro-word class="inline-block font-extrabold tracking-[0.3em]">Metindo</span></span>
                <span class="overflow-hidden"><span data-intro-word class="inline-block font-normal tracking-[0.3em]">Perkasa</span></span>
            </span>
        </div>

        <p data-intro-item data-intro-tagline class="mt-[clamp(1.5rem,3vw,3.5rem)] text-[clamp(0.7rem,0.95vw,1.1rem)] font-extrabold uppercase tracking-[0.3em] text-brand-300">{{ __('landing.footer.tagline') }}</p>

        <div data-intro-item data-intro-action class="relative mt-[clamp(2rem,4vw,4.5rem)] flex flex-col items-center gap-4">
            <span class="relative">
                <span data-intro-ring class="pointer-events-none absolute inset-0 rounded-lg border-2 border-brand-300" aria-hidden="true"></span>
                <x-landing.button as="button" type="button" data-intro-close variant="light">{{ __('home.intro.begin') }}</x-landing.button>
            </span>
            <span class="text-[clamp(0.7rem,0.8vw,0.95rem)] text-white/55 max-sm:hidden">{{ __('home.intro.hint') }}</span>
        </div>
    </div>

    <div data-intro-item data-intro-languages class="relative pb-[clamp(1.25rem,2.5vw,2.5rem)]">
        <x-landing.language-links dark />
    </div>
</div>
