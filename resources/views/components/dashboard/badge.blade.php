@props(['tone' => 'muted'])

<span {{ $attributes->class([
    'inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold',
    'bg-accent-blue text-white' => $tone === 'active',
    'bg-ink/10 text-ink' => $tone === 'muted',
    'bg-lavender text-ink ring-1 ring-ink/20' => $tone === 'role',
]) }}>{{ $slot }}</span>
