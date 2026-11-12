@props(['name', 'label', 'required' => false])

@php($id = str_replace('.', '-', $name))

{{-- The label and the error message around a searchable dropdown (the dropdown itself is the `lookup` Livewire component, passed in the slot). --}}
<div class="field-group">
    <label for="{{ $id }}" class="field-label">{{ $label }}@if ($required)<span class="text-brand-600" aria-hidden="true"> *</span>@endif</label>
    {{ $slot }}
    @error($name)
        <p id="{{ $id }}-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
    @enderror
</div>
