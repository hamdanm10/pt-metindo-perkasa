<footer class="bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <img src="{{ asset('images/brand/logo-white.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-7 w-auto self-start">
        <p class="text-sm text-white/80">
            &copy; {{ now()->year }} PT Metindo Perkasa. {{ __('landing.footer.rights') }}
        </p>
    </div>
</footer>
