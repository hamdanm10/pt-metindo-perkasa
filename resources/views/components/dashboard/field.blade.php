@props(['name', 'label', 'type' => 'text', 'hint' => null])

@php($id = str_replace('.', '-', $name))

<div class="field-group">
    <label for="{{ $id }}" class="field-label">{{ $label }}</label>
    <input id="{{ $id }}" type="{{ $type }}"
           @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @enderror
           {{ $attributes->class('field-input') }}>
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-2 text-sm text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
    @enderror
</div>
