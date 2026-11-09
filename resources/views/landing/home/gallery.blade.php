@php
    $slides = app(\App\Services\Landing\LandingGallery::class)->slides();
    $tones = ['bg-ink', 'bg-accent-indigo', 'bg-accent-blue', 'bg-brand-600'];
    $first = $slides[0] ?? null;
    $meta = 'text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase';
@endphp

@if ($slides)
    {{-- Full-screen photos that slide into each other by themselves every 3 seconds, with the city, the company, and the local time below. --}}
    <section data-gallery aria-label="{{ __('home.gallery.label') }}" class="relative w-full overflow-hidden bg-lavender text-ink">
        <div class="relative h-[100svh] overflow-hidden bg-ink">
            @foreach ($slides as $slide)
                <figure data-gallery-slide data-city="{{ $slide['city'] }}" data-name="{{ $slide['name'] }}" data-timezone="{{ $slide['timezone'] }}"
                        class="{{ $tones[$loop->index % count($tones)] }} absolute inset-0 overflow-hidden" @if (! $loop->first) style="visibility: hidden" @endif>
                    <x-landing.photo :name="'gallery-'.$slide['key']" ratio="aspect-video" bare :alt="$slide['name']" class="absolute inset-0 size-full bg-transparent! text-white!" />
                </figure>
            @endforeach
        </div>

        <div class="container-site flex pb-[2.2vw] pt-[1.7vw]">
            <p data-gallery-city class="{{ $meta }} flex-1 overflow-hidden text-start leading-[1.7]"><span class="inline-block" data-gallery-text lang="en" dir="ltr">{{ $first['city'] }}</span></p>
            <p data-gallery-name class="{{ $meta }} flex-1 overflow-hidden text-center leading-[1.7]"><span class="inline-block" data-gallery-text lang="en" dir="ltr">{{ $first['name'] }}</span></p>
            <p class="{{ $meta }} flex-1 overflow-hidden text-end leading-[1.7]"><span class="inline-block" data-gallery-time dir="ltr">{{ $first['time'] }}</span></p>
        </div>
    </section>
@endif
