@props(['slug'])

<span {{ $attributes->class('flex size-14 shrink-0 items-center justify-center rounded-xl bg-white p-2') }} aria-hidden="true">
    <x-landing.art :name="'icon-'.$slug" class="size-full object-contain">
        <span class="h-7 w-2 -skew-x-[20deg] rounded-full bg-brand-600"></span>
    </x-landing.art>
</span>
