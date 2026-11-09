@php
    $slides = app(\App\Services\Landing\LandingGallery::class)->slides();
    $tones = ['bg-ink', 'bg-accent-indigo', 'bg-accent-blue', 'bg-brand-600'];
    $first = $slides[0] ?? null;
    $meta = 'text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase';
@endphp

@if ($slides)
    {{-- Full-screen photos that slide into each other by themselves every 3 seconds, with a label, a caption, and a counter below. --}}
    <section data-gallery aria-label="{{ __('home.gallery.label') }}" class="relative w-full overflow-hidden bg-lavender text-ink">
        <div class="relative h-[100svh] overflow-hidden bg-ink">
            @foreach ($slides as $slide)
                <figure data-gallery-slide data-label="{{ $slide['label'] }}" data-caption="{{ $slide['caption'] }}"
                        class="{{ $tones[$loop->index % count($tones)] }} absolute inset-0 overflow-hidden" @if (! $loop->first) style="visibility: hidden" @endif>
                    <x-landing.photo :name="'gallery-'.$slide['key']" ratio="aspect-video" bare :alt="$slide['caption']" class="absolute inset-0 size-full bg-transparent! text-white!" />
                </figure>
            @endforeach
        </div>

        <div class="container-site flex pb-[2.2vw] pt-[1.7vw]">
            <p data-gallery-label class="{{ $meta }} flex-1 overflow-hidden text-start leading-[1.7]"><span class="inline-block" data-gallery-text>{{ $first['label'] }}</span></p>
            <p data-gallery-caption class="{{ $meta }} flex-1 overflow-hidden text-center leading-[1.7]"><span class="inline-block" data-gallery-text>{{ $first['caption'] }}</span></p>
            <p class="{{ $meta }} flex-1 overflow-hidden text-end leading-[1.7]" dir="ltr"><span class="inline-block" data-gallery-count>01 / {{ str_pad((string) count($slides), 2, '0', STR_PAD_LEFT) }}</span></p>
        </div>
    </section>
@endif
