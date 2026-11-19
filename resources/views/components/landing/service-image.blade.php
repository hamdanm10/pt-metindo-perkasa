@props(['service', 'ratio' => 'aspect-[4/3]'])

{{-- The uploaded picture of a service. A service without one shows no picture. --}}
@if ($service['image'])
    <img src="{{ $service['image'] }}" alt="" loading="lazy" decoding="async" {{ $attributes->class(['w-full object-cover', $ratio]) }}>
@endif
