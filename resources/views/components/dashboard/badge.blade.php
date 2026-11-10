@props(['tone' => 'muted'])

<span {{ $attributes->class([
    'inline-flex items-center rounded-md px-2.5 py-1 text-xs font-extrabold uppercase tracking-wide',
    'bg-brand-600 text-white' => $tone === 'active',
    'bg-ink/10 text-ink' => $tone === 'muted',
    'bg-ink text-white' => $tone === 'role',
]) }}>{{ $slot }}</span>
