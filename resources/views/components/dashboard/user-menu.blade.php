@props(['user', 'area'])

@php
    $role = __('dashboard.roles.'.$user->role->value);
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))->implode('');
@endphp

<details class="relative" data-dismissible>
    <summary class="dd-summary inline-flex min-h-11 cursor-pointer list-none items-center gap-3 rounded-lg border-2 border-ink/15 bg-lavender ps-1 pe-1 text-ink transition-colors hover:border-ink/40 sm:pe-4 [&::-webkit-details-marker]:hidden"
             aria-label="{{ __('dashboard.account_menu') }}">
        <span class="flex size-8 items-center justify-center rounded-md bg-brand-600 text-sm font-extrabold text-white" aria-hidden="true">{{ $initials }}</span>
        <span class="hidden text-start leading-tight sm:block">
            <span class="block text-sm font-bold">{{ $user->name }}</span>
            <span class="block text-xs font-semibold text-muted">{{ $role }}</span>
        </span>
        <svg viewBox="0 0 20 20" class="dd-chevron hidden size-4 sm:block" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
    </summary>
    <div class="dd-panel absolute end-0 top-full z-40 mt-2 w-64 rounded-lg border border-ink/10 bg-white p-2 text-ink shadow-lg">
        <div class="px-3 py-3">
            <p class="font-bold">{{ $user->name }}</p>
            <p class="mt-0.5 break-all text-sm text-muted" dir="ltr">{{ $user->email }}</p>
            <span class="mt-2 inline-flex items-center rounded-md bg-ink px-2.5 py-1 text-xs font-extrabold uppercase tracking-wide text-white">{{ $role }}</span>
        </div>
        <form method="POST" action="{{ route($area.'.logout') }}" class="border-t border-ink/10 pt-2">
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center rounded-md px-3 text-start text-sm font-bold text-ink/80 hover:bg-lavender hover:text-ink">{{ __('auth.logout') }}</button>
        </form>
    </div>
</details>
