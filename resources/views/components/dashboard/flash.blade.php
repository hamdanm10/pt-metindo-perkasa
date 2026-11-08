@if (session('status'))
    <div role="status" class="mb-6 rounded-lg bg-white px-5 py-4 text-sm font-bold text-ink ring-1 ring-accent-blue">
        {{ session('status') }}
    </div>
@endif
