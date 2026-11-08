@props(['name', 'label'])

@php($id = str_replace('.', '-', $name))

<div>
    <label for="{{ $id }}" class="block text-sm font-semibold">{{ $label }}</label>
    <select id="{{ $id }}"
            @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
            {{ $attributes->class('mt-2 block min-h-12 w-full rounded-lg border border-ink/50 bg-white px-4 text-base text-ink') }}>
        {{ $slot }}
    </select>
    @error($name)
        <p id="{{ $id }}-error" class="mt-2 text-sm font-semibold text-brand-700" role="alert">{{ $message }}</p>
    @enderror
</div>
