@php($videoId = config('landing.intro_video_id'))

@if ($videoId)
    <section class="bg-white">
        <div class="container-site grid items-center gap-10 py-16 lg:grid-cols-12 lg:gap-[4vw] lg:py-[clamp(4rem,7vw,7rem)]">
            <div class="lg:col-span-5">
                <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,4vw,4.75rem)] font-black uppercase leading-[0.98] tracking-tight text-ink">
                    {{ __('home.video.title') }}
                </h2>
                <p class="mt-5 text-lg text-muted sm:text-xl">
                    {{ __('home.video.text') }}
                </p>
            </div>

            {{-- The thumbnail is images/landing/photos/video-thumbnail.{webp,jpg,jpeg,png}. Replace the file to change it. --}}
            <div data-anim="card" data-video="{{ $videoId }}" data-video-title="{{ __('home.video.title') }}"
                 class="group relative aspect-video overflow-hidden rounded-lg bg-ink lg:col-span-7">
                <x-landing.photo name="video-thumbnail" ratio="aspect-video" bare class="absolute inset-0 h-full transition-transform duration-700 ease-out group-hover:scale-105 motion-reduce:transition-none" />
                <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/10 to-transparent" aria-hidden="true"></div>

                <button type="button" data-video-play
                        class="absolute inset-0 flex flex-col items-center justify-center gap-4 text-white focus-visible:outline-white">
                    <span class="flex size-16 items-center justify-center rounded-full bg-white text-brand-700 shadow-xl transition-transform duration-300 group-hover:scale-110 sm:size-20 motion-reduce:transition-none">
                        <svg viewBox="0 0 24 24" class="size-7 translate-x-0.5 sm:size-9" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11.04-6.86a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14Z"/></svg>
                    </span>
                    <span class="rounded-lg bg-ink/70 px-4 py-2 text-sm font-bold sm:text-base">{{ __('home.video.play') }}</span>
                </button>
            </div>
        </div>
    </section>
@endif
