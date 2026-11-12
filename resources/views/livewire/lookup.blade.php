<div x-data="{ open: false }"
     x-on:keydown.escape="if (open) { open = false; $wire.opened = false; $refs.trigger.focus() }"
     x-on:click.outside="if (open) { open = false; $wire.opened = false }"
     class="relative">
    <button type="button" x-ref="trigger" id="{{ $inputId }}"
            role="combobox" aria-haspopup="listbox" aria-controls="{{ $inputId }}-list" x-bind:aria-expanded="open.toString()"
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
            x-on:click="open = ! open; if (open) { $wire.openPanel(); $nextTick(() => $refs.search.focus()) } else { $wire.opened = false }"
            class="field-input field-select text-start">
        <span class="block truncate {{ $label === null ? 'text-muted' : '' }}">{{ $label ?? $placeholder }}</span>
    </button>

    <div x-show="open" x-cloak
         class="absolute inset-x-0 z-30 mt-1 rounded-lg border border-ink/15 bg-white p-2">
        <input type="search" x-ref="search" wire:model.live.debounce.300ms="search" autocomplete="off"
               aria-label="{{ __('dashboard.lookup.search') }}" aria-controls="{{ $inputId }}-list"
               placeholder="{{ __('dashboard.lookup.search') }}"
               x-on:keydown.arrow-down.prevent="$root.querySelector('[role=option]')?.focus()"
               class="field-input">

        <ul id="{{ $inputId }}-list" role="listbox" class="mt-2 max-h-64 overflow-y-auto">
            @if ($emptyLabel !== '' && $search === '')
                <li role="presentation">
                    <button type="button" role="option" aria-selected="{{ $value === '' ? 'true' : 'false' }}"
                            wire:click="clear" x-on:click="open = false; $nextTick(() => $refs.trigger.focus())"
                            x-on:keydown.arrow-down.prevent="$event.target.closest('li')?.nextElementSibling?.querySelector('[role=option]')?.focus()"
                            x-on:keydown.arrow-up.prevent="$refs.search.focus()"
                            class="block w-full rounded-md px-3 py-2 text-start text-base text-muted hover:bg-lavender focus-visible:bg-lavender {{ $value === '' ? 'bg-lavender font-bold' : '' }}">{{ $emptyLabel }}</button>
                </li>
            @endif
            @foreach ($results['items'] as $item)
                <li role="presentation" wire:key="option-{{ $item['id'] }}">
                    <button type="button" role="option" aria-selected="{{ (string) $item['id'] === $value ? 'true' : 'false' }}"
                            wire:click="select('{{ $item['id'] }}')" x-on:click="open = false; $nextTick(() => $refs.trigger.focus())"
                            x-on:keydown.arrow-down.prevent="$event.target.closest('li')?.nextElementSibling?.querySelector('[role=option]')?.focus()"
                            x-on:keydown.arrow-up.prevent="($event.target.closest('li')?.previousElementSibling?.querySelector('[role=option]') ?? $refs.search).focus()"
                            class="block w-full rounded-md px-3 py-2 text-start text-base hover:bg-lavender focus-visible:bg-lavender {{ (string) $item['id'] === $value ? 'bg-lavender font-bold' : '' }}">
                        <span class="block truncate">{{ $item['label'] }}</span>
                        @if ($item['hint'])
                            <span class="block truncate text-xs font-normal text-muted">{{ $item['hint'] }}</span>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        @if ($opened && $results['items'] === [])
            <p class="px-3 py-2 text-sm text-muted" role="status">{{ __('dashboard.lookup.no_results') }}</p>
        @endif
        @if ($results['more'])
            <p class="px-3 pt-2 text-xs text-muted" role="status">{{ __('dashboard.lookup.more', ['count' => $limit]) }}</p>
        @endif
        <p wire:loading wire:target="search" class="px-3 pt-2 text-xs text-muted" role="status">{{ __('dashboard.lookup.searching') }}</p>
    </div>
</div>
