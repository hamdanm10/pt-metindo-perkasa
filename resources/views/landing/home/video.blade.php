@php($videoId = config('landing.intro_video_id'))

@if ($videoId)
    <section class="relative bg-white">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:gap-14 lg:px-8 lg:py-28">
            <div class="lg:col-span-4">
                <h2 data-anim="heading" class="text-balance text-4xl font-black uppercase leading-[0.98] tracking-tight text-ink sm:text-5xl">
                    {{ __('home.video.title') }}
                </h2>
                <p class="mt-5 text-lg text-muted">
                    {{ __('home.video.text') }}
                </p>
            </div>

            <div class="lg:col-span-8">
                <div data-anim="media" data-video="{{ $videoId }}" data-video-title="{{ __('home.video.title') }}"
                     class="relative aspect-video overflow-hidden rounded-lg bg-ink">
                    <button type="button" data-video-play
                            class="group absolute inset-0 flex items-center justify-center gap-4 text-white focus-visible:outline-white">
                        <span class="flex size-20 items-center justify-center rounded-full bg-white text-brand-700 sm:size-24">
                            <svg viewBox="0 0 24 24" class="size-9 translate-x-0.5" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11.04-6.86a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14Z"/></svg>
                        </span>
                        <span class="sr-only">{{ __('home.video.play') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </section>
@endif
