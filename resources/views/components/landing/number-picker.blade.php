{{--
    A button for the customer service numbers the admin set: WhatsApp (mode "whatsapp") or call (mode "call").
    One number: a plain link. Several: the button opens a dialog that lists every number with its name and a button
    for each; without JavaScript the button still opens the first number. Nothing is rendered without numbers.
    Opened by number-picker.js.
--}}
@props(['agents' => [], 'mode' => 'whatsapp', 'variant' => 'primary', 'arrow' => true])

@php
    $contact = app(\App\Services\Landing\LandingContact::class);
    $whatsapp = $mode === 'whatsapp';
    $link = fn (array $agent): string => $whatsapp ? $contact->url($agent['number']) : $contact->tel($agent['number']);
    $external = $whatsapp ? ['target' => '_blank', 'rel' => 'noopener noreferrer'] : [];
    $key = $whatsapp ? 'chat.whatsapp' : 'chat.call';
    $id = 'picker-'.\Illuminate\Support\Str::random(8);
@endphp

@if ($agents)
    @if (count($agents) === 1)
        <x-landing.button :href="$link($agents[0])" :variant="$variant" :arrow="$arrow" {{ $attributes->merge($external) }}>{{ $slot }}</x-landing.button>
    @else
        <x-landing.button :href="$link($agents[0])" :variant="$variant" :arrow="$arrow" {{ $attributes->merge($external) }} data-number-picker="{{ $id }}" aria-haspopup="dialog">{{ $slot }}</x-landing.button>

        <dialog id="{{ $id }}" data-number-dialog aria-labelledby="{{ $id }}-title" class="m-auto w-[min(26rem,calc(100vw-2rem))] max-w-none overflow-hidden rounded-lg bg-white p-0 text-ink shadow-[0_30px_80px_-20px_rgba(27,31,59,0.55)] backdrop:bg-ink/70" style="--focus-ring: #1b1f3b">
            <div class="flex items-start justify-between gap-4 px-5 py-4 text-white {{ $whatsapp ? 'bg-[#128c4a]' : 'bg-brand-600' }}" style="--focus-ring: #ffffff">
                <div class="min-w-0">
                    <h2 id="{{ $id }}-title" class="text-lg font-black">{{ __("{$key}.title") }}</h2>
                    <p class="mt-1 text-sm text-white/90">{{ __("{$key}.hint") }}</p>
                </div>
                <button type="button" data-number-close aria-label="{{ __('chat.close') }}" class="grid size-9 shrink-0 place-items-center rounded-lg hover:bg-white/20">
                    <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg>
                </button>
            </div>
            <ul class="divide-y divide-ink/10">
                @foreach ($agents as $agent)
                    <li>
                        <a href="{{ $link($agent) }}" @foreach ($external as $name => $value) {{ $name }}="{{ $value }}" @endforeach class="group flex items-center gap-3 px-5 py-4 hover:bg-lavender focus-visible:outline-offset-[-3px]">
                            <span class="grid size-11 shrink-0 place-items-center rounded-full {{ $whatsapp ? 'bg-[#25d366]/15 text-[#128c4a]' : 'bg-brand-600/10 text-brand-700' }}" aria-hidden="true">
                                @if ($whatsapp)
                                    <svg viewBox="0 0 24 24" class="size-5" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" class="size-5" fill="currentColor"><path d="M6.6 10.8a15.1 15.1 0 006.6 6.6l2.2-2.2a1 1 0 011-.25 11.4 11.4 0 003.6.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.6a1 1 0 01-.25 1z"/></svg>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-base font-bold">{{ $agent['name'] }}</span>
                                <span class="block text-sm text-ink/65" dir="ltr">{{ $agent['display'] }}</span>
                            </span>
                            <span class="rounded-full px-3.5 py-1.5 text-xs font-bold text-white transition-transform group-hover:scale-105 {{ $whatsapp ? 'bg-[#128c4a]' : 'bg-brand-600' }}">{{ $whatsapp ? __('chat.whatsapp.chat') : __('chat.call.action') }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </dialog>
    @endif
@endif
