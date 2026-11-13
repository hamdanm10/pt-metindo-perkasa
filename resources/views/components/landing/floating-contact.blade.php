@php
    $agents = app(\App\Services\Landing\LandingContact::class)->agents();
    $topics = app(\App\Services\Landing\LandingChat::class)->topics();
    $rules = [
        'welcome' => __('chat.chat.welcome'),
        'again' => __('chat.chat.again'),
        'topics' => $topics,
    ];
    // Each panel opens right above its own button: its right edge lines up with the button's. On small screens it spans above both.
    $panel = 'absolute bottom-full end-0 mb-3 origin-bottom-right overflow-hidden rounded-lg bg-white text-ink shadow-[0_20px_60px_-15px_rgba(27,31,59,0.45)] ring-1 ring-ink/10 max-sm:fixed max-sm:bottom-[5.5rem] max-sm:end-4 max-sm:mb-0';
    $round = 'group grid size-14 place-items-center rounded-full text-white shadow-lg transition-transform duration-300 hover:scale-105 focus-visible:outline-offset-4 sm:size-[3.75rem]';
@endphp

{{-- Two floating buttons side by side at the bottom right. The chat opens a rule-based assistant that is answered by tapping option bubbles (the questions, answers and follow-up questions are written by the admin per language, no server round trip). It is left out while the language has no questions. WhatsApp opens the list of customer service numbers. Each panel opens above its own button. Opened and closed by contact-widgets.js. --}}
@if ($topics !== [] || $agents)
<div data-widgets class="fixed bottom-4 end-4 z-40 flex items-end gap-3 sm:bottom-6 sm:end-6" style="--focus-ring: #1b1f3b">
    @if ($topics !== [])
    <div data-chat-rules="{{ json_encode($rules, JSON_UNESCAPED_UNICODE) }}" hidden></div>

    <div class="relative">
    <section id="chat-panel" data-panel="chat" hidden role="dialog" aria-labelledby="chat-title" class="{{ $panel }} flex h-[28rem] max-h-[calc(100dvh-9rem)] w-[min(22rem,calc(100vw-2rem))] flex-col">
        <div class="flex items-start justify-between gap-3 bg-brand-600 px-4 py-3 text-white" style="--focus-ring: #ffffff">
            <div class="min-w-0">
                <h2 id="chat-title" class="text-base font-bold">{{ __('chat.chat.title') }}</h2>
                <p class="mt-0.5 text-xs text-white/85">{{ __('chat.chat.subtitle') }}</p>
            </div>
            <button type="button" data-widget-close aria-label="{{ __('chat.close') }}" class="grid size-8 shrink-0 place-items-center rounded-lg hover:bg-white/20">
                <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg>
            </button>
        </div>

        <div data-chat-log data-lenis-prevent class="relative flex flex-1 flex-col gap-2.5 overflow-y-auto overscroll-contain bg-lavender p-4 pb-5" role="log" aria-live="polite"></div>

    </section>
    <button type="button" data-widget-toggle="chat" aria-expanded="false" aria-controls="chat-panel" aria-label="{{ __('chat.open_chat') }}" class="{{ $round }} bg-brand-600 hover:bg-brand-700">
        {{-- A speech bubble with three dots. The dots take the button colour, so they show as holes. --}}
        <svg viewBox="0 0 24 24" class="size-7" aria-hidden="true"><path fill="currentColor" d="M12 2.5c-5.5 0-10 3.8-10 8.6 0 2.4 1.1 4.6 3 6.1-.2 1.2-.8 2.4-1.7 3.4-.3.4-.1 1 .5 1 2.3-.2 4-1.1 5.2-1.9.9.2 2 .3 3 .3 5.5 0 10-3.8 10-8.6S17.5 2.5 12 2.5Z"/><circle cx="7.6" cy="11.1" r="1.35" class="fill-brand-600 transition-colors group-hover:fill-brand-700"/><circle cx="12" cy="11.1" r="1.35" class="fill-brand-600 transition-colors group-hover:fill-brand-700"/><circle cx="16.4" cy="11.1" r="1.35" class="fill-brand-600 transition-colors group-hover:fill-brand-700"/></svg>
    </button>
    </div>
    @endif

    @if ($agents)
        <div class="relative">
        <section id="whatsapp-panel" data-panel="whatsapp" hidden role="dialog" aria-labelledby="whatsapp-title" class="{{ $panel }} w-[min(20rem,calc(100vw-2rem))]">
            <div class="flex items-start justify-between gap-3 bg-[#128c4a] px-4 py-3 text-white" style="--focus-ring: #ffffff">
                <div class="min-w-0">
                    <h2 id="whatsapp-title" class="text-base font-bold">{{ __('chat.whatsapp.title') }}</h2>
                    <p class="mt-0.5 text-xs text-white/90">{{ __('chat.whatsapp.hint') }}</p>
                </div>
                <button type="button" data-widget-close aria-label="{{ __('chat.close') }}" class="grid size-8 shrink-0 place-items-center rounded-lg hover:bg-white/20">
                    <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg>
                </button>
            </div>
            <ul data-lenis-prevent class="max-h-[min(24rem,calc(100dvh-14rem))] divide-y divide-ink/10 overflow-y-auto overscroll-contain">
                @foreach ($agents as $agent)
                    <li data-agent>
                        <a href="{{ app(\App\Services\Landing\LandingContact::class)->url($agent['number']) }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 px-4 py-3 hover:bg-lavender focus-visible:outline-offset-[-3px]">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#25d366]/15 text-[#128c4a]" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="size-5" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold">{{ $agent['name'] }}</span>
                                <span class="block text-xs text-ink/65" dir="ltr">{{ $agent['display'] }}</span>
                            </span>
                            <span class="rounded-full bg-[#128c4a] px-3 py-1 text-xs font-bold text-white transition-transform group-hover:scale-105">{{ __('chat.whatsapp.chat') }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
        <button type="button" data-widget-toggle="whatsapp" aria-expanded="false" aria-controls="whatsapp-panel" aria-label="{{ __('chat.open_whatsapp') }}" class="{{ $round }} bg-[#25d366]">
            <svg viewBox="0 0 24 24" class="size-7" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </button>
        </div>
    @endif
</div>
@endif
