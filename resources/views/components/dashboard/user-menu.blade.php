@props(['user', 'area'])

@php
    $role = __('dashboard.roles.'.$user->role->value);
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))->implode('');
@endphp

<details class="relative" data-dismissible>
    <summary class="inline-flex min-h-11 cursor-pointer list-none items-center gap-3 rounded-lg px-1 hover:bg-lavender sm:px-2 [&::-webkit-details-marker]:hidden"
             aria-label="{{ __('dashboard.account_menu') }}">
        <span class="flex size-9 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white" aria-hidden="true">{{ $initials }}</span>
        <span class="hidden text-start leading-tight sm:block">
            <span class="block text-sm font-bold">{{ $user->name }}</span>
            <span class="block text-xs font-semibold text-muted">{{ $role }}</span>
        </span>
        <svg viewBox="0 0 20 20" class="hidden size-4 sm:block" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
    </summary>
    <div class="absolute end-0 top-full z-40 mt-2 w-64 rounded-2xl bg-white p-2 shadow-xl ring-1 ring-ink/10">
        <div class="px-3 py-3">
            <p class="font-bold">{{ $user->name }}</p>
            <p class="mt-0.5 break-all text-sm text-muted" dir="ltr">{{ $user->email }}</p>
            <span class="mt-2 inline-flex items-center rounded-md bg-lavender px-2.5 py-1 text-xs font-bold ring-1 ring-ink/20">{{ $role }}</span>
        </div>
        <form method="POST" action="{{ route($area.'.logout') }}" class="border-t border-ink/10 pt-2">
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center rounded-lg px-3 text-start text-sm font-bold hover:bg-lavender">{{ __('auth.logout') }}</button>
        </form>
    </div>
</details>
