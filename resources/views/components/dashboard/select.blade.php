@props(['name', 'label'])

@php($id = str_replace('.', '-', $name))

<div class="field-group">
    <label for="{{ $id }}" class="field-label">{{ $label }}@if ($attributes->has('required'))<span class="text-brand-600" aria-hidden="true"> *</span>@endif</label>
    <select id="{{ $id }}"
            @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
            {{ $attributes->class('field-input field-select') }}>
        {{ $slot }}
    </select>
    @error($name)
        <p id="{{ $id }}-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
    @enderror
</div>
