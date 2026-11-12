<div>
    <x-dashboard.page-header :title="__('inquiries.show_title')" :description="__('inquiries.asked_on', ['date' => $inquiry->asked_at->isoFormat('LL')])" />

    <x-dashboard.flash />

    <x-dashboard.card class="max-w-3xl">
        @if ($inquiry->service || $inquiry->product)
            <ul class="mb-5 flex flex-wrap items-center gap-2">
                <li class="text-sm font-bold text-muted">{{ __('inquiries.related') }}</li>
                @if ($inquiry->service)
                    <li><x-dashboard.badge tone="muted">{{ $inquiry->service->name }}</x-dashboard.badge></li>
                @endif
                @if ($inquiry->product)
                    <li><x-dashboard.badge tone="muted">{{ $inquiry->product->name }}</x-dashboard.badge></li>
                @endif
            </ul>
        @endif

        @if ($staffArea)
            <p class="mb-3 text-sm font-bold text-muted">{{ __('inquiries.fields.customer') }}: <span class="text-ink">{{ $inquiry->customer->name }}</span></p>
        @endif

        <h2 class="text-xl font-black leading-snug">{{ $inquiry->question }}</h2>

        <div class="mt-6 border-t border-ink/10 pt-6">
            <h3 class="field-label">{{ __('inquiries.answer_heading') }}</h3>
            <p class="mt-2 whitespace-pre-line text-base leading-relaxed">{{ $inquiry->answer }}</p>
        </div>

        @if ($inquiry->images->isNotEmpty())
            <div class="mt-8 border-t border-ink/10 pt-6">
                <h3 class="field-label">{{ __('inquiries.pictures') }}</h3>
                <ul class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($inquiry->images as $image)
                        <li>
                            <a href="{{ route($area.'.inquiry-images.show', $image) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-lg border border-ink/10 bg-lavender">
                                <img src="{{ route($area.'.inquiry-images.show', $image) }}" alt="" loading="lazy" decoding="async" class="aspect-[4/3] w-full object-cover">
                                <span class="sr-only">{{ __('inquiries.open_picture', ['number' => $loop->iteration]) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($staffArea)
            <p class="mt-8 text-sm text-muted">{{ __('inquiries.added_by', ['name' => $inquiry->creator?->name ?? __('inquiries.unknown_author')]) }}</p>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            <x-dashboard.button variant="secondary" :href="route($area.'.inquiries.index')">{{ __('inquiries.back') }}</x-dashboard.button>
            @if ($canUpdate)
                <x-dashboard.button :href="route('app.inquiries.edit', $inquiry)">{{ __('inquiries.edit') }}</x-dashboard.button>
            @endif
        </div>
    </x-dashboard.card>
</div>
