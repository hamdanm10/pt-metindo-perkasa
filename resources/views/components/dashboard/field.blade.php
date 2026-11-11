@props(['name', 'label', 'type' => 'text', 'hint' => null])

@php($id = str_replace('.', '-', $name))

<div class="field-group">
    <label for="{{ $id }}" class="field-label">{{ $label }}@if ($attributes->has('required'))<span class="text-brand-600" aria-hidden="true"> *</span>@endif</label>
    @if ($type === 'password')
        <div class="relative">
            <input id="{{ $id }}" type="password"
                   @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @enderror
                   {{ $attributes->class('field-input pe-12') }}>
            <x-dashboard.password-toggle :for="$id" />
        </div>
    @else
        <input id="{{ $id }}" type="{{ $type }}"
               @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @enderror
               {{ $attributes->class('field-input') }}>
    @endif
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-2 text-sm text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
    @enderror
</div>
