@props(['service', 'ratio' => 'aspect-[4/3]'])

{{-- The uploaded picture of a service. Without one, the sample placeholder shows while the site is in sample mode. --}}
@if ($service['image'])
    <img src="{{ $service['image'] }}" alt="" loading="lazy" decoding="async" {{ $attributes->class(['w-full object-cover', $ratio]) }}>
@else
    <x-landing.photo :name="'service-'.$service['slug']" :ratio="$ratio" bare {{ $attributes }} />
@endif
