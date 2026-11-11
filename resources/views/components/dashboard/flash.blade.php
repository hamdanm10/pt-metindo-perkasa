@php
    $success = session('status');
    $failure = $errors->first('active');
@endphp

@if ($success || $failure)
    {{-- Notifications are toasts at the top right: they slide in, close by themselves after a few seconds (not while the pointer is on them), and can be closed with the button. --}}
    <div class="pointer-events-none fixed end-4 top-20 z-[60] mt-0! flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3 lg:end-8">
        @foreach ([['success', $success], ['error', $failure]] as [$kind, $message])
            @if ($message)
                <div data-toast data-timeout="{{ $kind === 'error' ? 8000 : 5000 }}" role="{{ $kind === 'error' ? 'alert' : 'status' }}"
                     class="toast pointer-events-auto flex items-start gap-3 rounded-lg bg-ink p-4 pe-2 text-sm font-bold text-white shadow-lg ring-1 ring-white/10" style="--focus-ring: #ffffff">
                    @if ($kind === 'error')
                        <svg viewBox="0 0 20 20" class="mt-0.5 size-5 shrink-0 text-brand-400" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                    @else
                        <svg viewBox="0 0 20 20" class="mt-0.5 size-5 shrink-0 text-brand-300" fill="currentColor" aria-hidden="true"><path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z"/></svg>
                    @endif
                    <p class="flex-1 py-0.5 leading-snug">{{ $message }}</p>
                    <button type="button" data-toast-close class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-white/70 hover:bg-white/10 hover:text-white" aria-label="{{ __('dashboard.close_notification') }}">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                    <span class="toast__bar absolute inset-x-0 bottom-0 h-1 origin-left rounded-b-lg {{ $kind === 'error' ? 'bg-brand-600' : 'bg-brand-300' }}" aria-hidden="true" style="animation-duration: {{ $kind === 'error' ? 8000 : 5000 }}ms"></span>
                </div>
            @endif
        @endforeach
    </div>
@endif
