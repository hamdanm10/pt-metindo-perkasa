{{-- Share buttons: copy the link (articles.js shows the confirmation) and share it on WhatsApp. No third-party scripts. --}}
<div class="flex items-center gap-2" role="group" aria-label="{{ __('articles.share.title') }}">
    <button type="button" data-copy-link="{{ url()->current() }}" data-copied="{{ __('articles.share.copied') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-ink/25 px-4 text-xs font-bold transition-colors hover:border-ink hover:bg-ink hover:text-white">
        <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.5 11.5a3.5 3.5 0 0 0 5 0l2.5-2.5a3.5 3.5 0 0 0-5-5l-1 1"/><path d="M11.5 8.5a3.5 3.5 0 0 0-5 0L4 11a3.5 3.5 0 0 0 5 5l1-1"/></svg>
        <span data-copy-label>{{ __('articles.share.copy') }}</span>
    </button>
    <a href="https://wa.me/?text={{ rawurlencode($title.' '.url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-ink/25 px-4 text-xs font-bold transition-colors hover:border-[#128c4a] hover:bg-[#128c4a] hover:text-white">
        <svg viewBox="0 0 24 24" class="size-4" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.4 15.1L2 22l5.05-1.6A9.9 9.9 0 1 0 12.04 2Zm0 1.8a8.1 8.1 0 1 1-4.3 14.96l-.3-.2-3 .95.97-2.9-.2-.3A8.1 8.1 0 0 1 12.04 3.8Z"/></svg>
        <span>{{ __('articles.share.whatsapp') }}</span>
    </a>
</div>
