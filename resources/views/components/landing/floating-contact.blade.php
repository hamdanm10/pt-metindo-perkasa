@php
    $agents = app(\App\Services\Landing\LandingContact::class)->agents();
    $topics = app(\App\Services\Landing\LandingChat::class)->topics();
    $rules = [
        'welcome' => __('chat.chat.welcome'),
        'again' => __('chat.chat.again'),
        'talk' => $agents ? __('chat.chat.talk') : null,
        'menu' => __('chat.chat.menu'),
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

        <div data-chat-log class="relative flex flex-1 flex-col gap-2.5 overflow-y-auto bg-lavender p-4 pb-5" role="log" aria-live="polite"></div>

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
            <ul class="max-h-[min(24rem,calc(100dvh-14rem))] divide-y divide-ink/10 overflow-y-auto">
                @foreach ($agents as $agent)
                    <li data-agent>
                        <a href="{{ app(\App\Services\Landing\LandingContact::class)->url($agent['number']) }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 px-4 py-3 hover:bg-lavender focus-visible:outline-offset-[-3px]">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#25d366]/15 text-[#128c4a]" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="size-5" fill="currentColor"><path d="M12.04 2a9.9 9.9 0 0 0-8.4 15.1L2 22l5.05-1.6A9.9 9.9 0 1 0 12.04 2Zm0 1.8a8.1 8.1 0 1 1-4.3 14.96l-.3-.2-3 .95.97-2.9-.2-.3A8.1 8.1 0 0 1 12.04 3.8Zm-3.2 3.7c-.2 0-.5.1-.7.3-.3.3-.9.9-.9 2.2s.9 2.5 1 2.7c.1.2 1.8 2.8 4.4 3.9 2.2.9 2.6.7 3.1.7.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.6.1l-.7.9c-.1.2-.3.2-.5.1-.3-.1-1.1-.4-2-1.3-.8-.7-1.3-1.5-1.4-1.8-.2-.3 0-.4.1-.5l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4l-.7-1.7c-.2-.5-.4-.4-.6-.4Z"/></svg>
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
            <svg viewBox="0 0 24 24" class="size-7" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.4 15.1L2 22l5.05-1.6A9.9 9.9 0 1 0 12.04 2Zm0 1.8a8.1 8.1 0 1 1-4.3 14.96l-.3-.2-3 .95.97-2.9-.2-.3A8.1 8.1 0 0 1 12.04 3.8Zm-3.2 3.7c-.2 0-.5.1-.7.3-.3.3-.9.9-.9 2.2s.9 2.5 1 2.7c.1.2 1.8 2.8 4.4 3.9 2.2.9 2.6.7 3.1.7.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.6.1l-.7.9c-.1.2-.3.2-.5.1-.3-.1-1.1-.4-2-1.3-.8-.7-1.3-1.5-1.4-1.8-.2-.3 0-.4.1-.5l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4l-.7-1.7c-.2-.5-.4-.4-.6-.4Z"/></svg>
        </button>
        </div>
    @endif
</div>
@endif
