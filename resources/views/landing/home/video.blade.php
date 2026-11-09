@php($videoId = config('landing.intro_video_id'))

@if ($videoId)
    <section class="bg-white">
        <div class="container-site section-y">
            <div class="grid gap-6 lg:grid-cols-12 lg:items-end lg:gap-10">
                <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,5vw,6rem)] font-black uppercase leading-[0.98] tracking-tight text-ink lg:col-span-8">
                    {{ __('home.video.title') }}
                </h2>
                <p class="text-lg text-muted sm:text-xl lg:col-span-4">
                    {{ __('home.video.text') }}
                </p>
            </div>

            {{-- The thumbnail is images/landing/photos/video-thumbnail.{webp,jpg,jpeg,png}. Replace the file to change it. --}}
            <div data-anim="card" data-video="{{ $videoId }}" data-video-title="{{ __('home.video.title') }}"
                 class="group relative mt-10 aspect-video overflow-hidden rounded-lg bg-ink lg:mt-14">
                <x-landing.photo name="video-thumbnail" ratio="aspect-video" bare class="absolute inset-0 h-full transition-transform duration-700 ease-out group-hover:scale-105 motion-reduce:transition-none" />
                <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/10 to-transparent" aria-hidden="true"></div>

                <button type="button" data-video-play
                        class="absolute inset-0 flex flex-col items-center justify-center gap-4 text-white focus-visible:outline-white">
                    <span class="flex size-20 items-center justify-center rounded-full bg-white text-brand-700 shadow-xl transition-transform duration-300 group-hover:scale-110 sm:size-28 motion-reduce:transition-none">
                        <svg viewBox="0 0 24 24" class="size-8 translate-x-0.5 sm:size-11" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11.04-6.86a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14Z"/></svg>
                    </span>
                    <span class="rounded-lg bg-ink/70 px-4 py-2 text-base font-bold sm:text-lg">{{ __('home.video.play') }}</span>
                </button>
            </div>
        </div>
    </section>
@endif
