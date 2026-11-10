@if (session('status'))
    <div role="status" class="mb-6 flex items-center gap-3 rounded-lg bg-ink px-5 py-4 text-sm font-bold text-white">
        <svg viewBox="0 0 20 20" class="size-5 shrink-0 text-brand-300" fill="currentColor" aria-hidden="true"><path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z"/></svg>
        {{ session('status') }}
    </div>
@endif
