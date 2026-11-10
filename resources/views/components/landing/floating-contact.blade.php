@php
    $agents = app(\App\Services\Landing\LandingContact::class)->agents();
    $rules = [
        'welcome' => __('chat.chat.welcome'),
        'fallback' => __('chat.chat.fallback'),
        'you' => __('chat.chat.you'),
        'bot' => __('chat.chat.bot'),
        'topics' => app(\App\Services\Landing\LandingChat::class)->topics(),
    ];
    $panel = 'absolute bottom-full end-0 mb-3 origin-bottom-right overflow-hidden rounded-lg bg-white text-ink shadow-[0_20px_60px_-15px_rgba(27,31,59,0.45)] ring-1 ring-ink/10';
    $round = 'grid size-14 place-items-center rounded-full text-white shadow-lg transition-transform duration-300 hover:scale-105 focus-visible:outline-offset-4 sm:size-[3.75rem]';
@endphp

{{-- Two floating buttons at the bottom right. The chat opens a rule-based assistant (answers come from the site's own content, no server). WhatsApp opens the list of customer service numbers. Opened and closed by contact-widgets.js. --}}
<div data-widgets class="fixed bottom-4 end-4 z-40 flex flex-col items-end gap-3 sm:bottom-6 sm:end-6" style="--focus-ring: #1b1f3b">
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

        <div data-chat-log class="flex flex-1 flex-col gap-2.5 overflow-y-auto bg-lavender p-4" role="log" aria-live="polite"></div>

        <div class="border-t border-ink/10 bg-white px-3 pt-3">
            <p class="sr-only">{{ __('chat.chat.topics') }}</p>
            <ul data-chat-topics class="flex gap-1.5 overflow-x-auto pb-2"></ul>
            <form data-chat-form class="mt-3 flex gap-2 pb-3">
                <label class="sr-only" for="chat-input">{{ __('chat.chat.placeholder') }}</label>
                <input id="chat-input" data-chat-input type="text" autocomplete="off" maxlength="200" placeholder="{{ __('chat.chat.placeholder') }}" class="min-w-0 flex-1 rounded-lg border border-ink/25 px-3 py-2 text-sm focus-visible:outline-offset-1">
                <button type="submit" class="rounded-lg bg-ink px-4 text-sm font-bold text-white hover:bg-brand-600">{{ __('chat.chat.send') }}</button>
            </form>
        </div>
    </section>

    @if ($agents)
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
    @endif

    <div data-chat-rules="{{ json_encode($rules, JSON_UNESCAPED_UNICODE) }}" hidden></div>

    <button type="button" data-widget-toggle="chat" aria-expanded="false" aria-controls="chat-panel" aria-label="{{ __('chat.open_chat') }}" class="{{ $round }} bg-brand-600 hover:bg-brand-700">
        <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.5-4.6A8 8 0 1 1 21 12Z"/><path d="M8.5 11h.01M12 11h.01M15.5 11h.01"/></svg>
    </button>

    @if ($agents)
        <button type="button" data-widget-toggle="whatsapp" aria-expanded="false" aria-controls="whatsapp-panel" aria-label="{{ __('chat.open_whatsapp') }}" class="{{ $round }} bg-[#25d366]">
            <svg viewBox="0 0 24 24" class="size-7" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.4 15.1L2 22l5.05-1.6A9.9 9.9 0 1 0 12.04 2Zm0 1.8a8.1 8.1 0 1 1-4.3 14.96l-.3-.2-3 .95.97-2.9-.2-.3A8.1 8.1 0 0 1 12.04 3.8Zm-3.2 3.7c-.2 0-.5.1-.7.3-.3.3-.9.9-.9 2.2s.9 2.5 1 2.7c.1.2 1.8 2.8 4.4 3.9 2.2.9 2.6.7 3.1.7.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.6.1l-.7.9c-.1.2-.3.2-.5.1-.3-.1-1.1-.4-2-1.3-.8-.7-1.3-1.5-1.4-1.8-.2-.3 0-.4.1-.5l.4-.5c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4l-.7-1.7c-.2-.5-.4-.4-.6-.4Z"/></svg>
        </button>
    @endif
</div>
