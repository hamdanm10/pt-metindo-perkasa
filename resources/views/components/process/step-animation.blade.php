@props(['name', 'class' => ''])

{{-- Decorative cartoon of one process step. Moved by resources/css/step-animation.css and still when the visitor prefers reduced motion. --}}
<svg viewBox="0 0 120 90" xmlns="http://www.w3.org/2000/svg" class="step-anim {{ $class }}" aria-hidden="true" focusable="false" data-step-animation="{{ $name }}">
    @switch($name)
        @case('degreasing')
            <rect x="12" y="8" width="96" height="10" rx="5" fill="#6E78AE"/>
            <rect x="52" y="18" width="16" height="12" fill="#6E78AE"/>
            <g fill="#7FA3E8"><circle class="sa-drop" cx="46" cy="36" r="3.5"/><circle class="sa-drop" cx="60" cy="38" r="3.5"/><circle class="sa-drop" cx="74" cy="36" r="3.5"/></g>
            <rect x="34" y="58" width="52" height="16" rx="8" fill="#98A2C4"/>
            <circle cx="46" cy="66" r="3" fill="#1B1F3B"/><circle cx="74" cy="66" r="3" fill="#1B1F3B"/>
            <path d="M18 80h84" stroke="#1B1F3B" stroke-width="3" stroke-linecap="round"/>
            @break
        @case('rinsing')
            <path d="M20 14h44a8 8 0 0 1 8 8v4" fill="none" stroke="#6E78AE" stroke-width="8" stroke-linecap="round"/>
            <rect x="66" y="24" width="12" height="10" rx="3" fill="#6E78AE"/>
            <g fill="#7FA3E8"><circle class="sa-drop" cx="72" cy="42" r="3.5"/><circle class="sa-drop" cx="72" cy="42" r="3"/><circle class="sa-drop" cx="72" cy="42" r="3.5"/></g>
            <path d="M24 56h72l-6 20a6 6 0 0 1-6 4H36a6 6 0 0 1-6-4Z" fill="#F3F2FF" stroke="#1B1F3B" stroke-width="3" stroke-linejoin="round"/>
            <path d="M31 64h58" stroke="#7FA3E8" stroke-width="6" stroke-linecap="round"/>
            @break
        @case('pickling')
            <path d="M18 40h84v32a8 8 0 0 1-8 8H26a8 8 0 0 1-8-8Z" fill="#F3F2FF" stroke="#1B1F3B" stroke-width="3" stroke-linejoin="round"/>
            <path d="M22 48h76v24a4 4 0 0 1-4 4H26a4 4 0 0 1-4-4Z" fill="#D6283A" opacity=".8"/>
            <g fill="#FFFFFF" stroke="#D6283A" stroke-width="1.5"><circle class="sa-bubble" cx="38" cy="58" r="4"/><circle class="sa-bubble" cx="58" cy="62" r="5"/><circle class="sa-bubble" cx="78" cy="58" r="4"/><circle class="sa-bubble" cx="90" cy="64" r="3"/></g>
            <text x="60" y="28" text-anchor="middle" font-size="18" font-weight="800" fill="#D6283A" font-family="sans-serif">pH</text>
            @break
        @case('plating')
            <path d="M14 36h92v36a8 8 0 0 1-8 8H22a8 8 0 0 1-8-8Z" fill="#F3F2FF" stroke="#1B1F3B" stroke-width="3" stroke-linejoin="round"/>
            <path d="M18 44h84v28a4 4 0 0 1-4 4H22a4 4 0 0 1-4-4Z" fill="#B5B0F2"/>
            <rect x="26" y="14" width="8" height="40" rx="3" fill="#D6283A"/><text x="30" y="11" text-anchor="middle" font-size="9" font-weight="800" fill="#D6283A" font-family="sans-serif">+</text>
            <rect x="86" y="14" width="8" height="40" rx="3" fill="#1B1F3B"/><text x="90" y="11" text-anchor="middle" font-size="9" font-weight="800" fill="#1B1F3B" font-family="sans-serif">−</text>
            <rect x="50" y="46" width="20" height="14" rx="4" fill="#D9DCE8" stroke="#1B1F3B" stroke-width="2"/>
            <g fill="#FFFFFF"><circle class="sa-bubble" cx="40" cy="64" r="3"/><circle class="sa-bubble" cx="60" cy="68" r="3.5"/><circle class="sa-bubble" cx="80" cy="64" r="3"/></g>
            <path class="sa-spark" d="M42 30l4 8-6-3 4 9" fill="none" stroke="#FFC933" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('passivation')
            <path d="M18 44h84v30a8 8 0 0 1-8 8H26a8 8 0 0 1-8-8Z" fill="#F3F2FF" stroke="#1B1F3B" stroke-width="3" stroke-linejoin="round"/>
            <path d="M22 52h76v22a4 4 0 0 1-4 4H26a4 4 0 0 1-4-4Z" fill="#7FA3E8" opacity=".75"/>
            <g class="sa-dip"><path d="M60 4v22" stroke="#6E78AE" stroke-width="3" stroke-linecap="round"/><rect x="46" y="26" width="28" height="18" rx="5" fill="#98A2C4" stroke="#1B1F3B" stroke-width="2"/><circle cx="54" cy="35" r="2.5" fill="#1B1F3B"/><circle cx="66" cy="35" r="2.5" fill="#1B1F3B"/></g>
            @break
        @case('drying')
            <rect x="34" y="54" width="52" height="16" rx="8" fill="#98A2C4"/>
            <circle cx="46" cy="62" r="3" fill="#1B1F3B"/><circle cx="74" cy="62" r="3" fill="#1B1F3B"/>
            <g fill="none" stroke="#D6283A" stroke-width="3.5" stroke-linecap="round"><path class="sa-wave" d="M40 44c4-4 8 4 12 0"/><path class="sa-wave" d="M56 40c4-4 8 4 12 0"/><path class="sa-wave" d="M72 44c4-4 8 4 12 0"/></g>
            <rect x="14" y="76" width="92" height="6" rx="3" fill="#1B1F3B"/>
            <rect x="12" y="14" width="22" height="18" rx="5" fill="#6E78AE"/><path d="M34 20h10l-4 6 4 6H34" fill="#6E78AE"/>
            @break
        @case('baking')
            <rect x="14" y="14" width="92" height="66" rx="10" fill="#6E78AE" stroke="#1B1F3B" stroke-width="3"/>
            <rect x="26" y="28" width="68" height="40" rx="6" fill="#1B1F3B"/>
            <rect class="sa-glow" x="26" y="28" width="68" height="40" rx="6" fill="#D6283A"/>
            <rect x="48" y="48" width="24" height="12" rx="4" fill="#98A2C4"/>
            <circle cx="30" cy="21" r="3" fill="#F3F2FF"/><circle cx="42" cy="21" r="3" fill="#F3F2FF"/>
            @break
        @case('inspection')
            <rect x="26" y="50" width="68" height="20" rx="10" fill="#98A2C4"/>
            <circle cx="42" cy="60" r="3.5" fill="#1B1F3B"/><circle cx="78" cy="60" r="3.5" fill="#1B1F3B"/>
            <path class="sa-shine" d="M54 52l4-4 4 4-4 4Z" fill="#FFFFFF"/>
            <g class="sa-scan"><circle cx="60" cy="52" r="15" fill="#F3F2FF" fill-opacity=".55" stroke="#1B1F3B" stroke-width="4"/><path d="M71 63l14 14" stroke="#1B1F3B" stroke-width="6" stroke-linecap="round"/></g>
            <rect x="14" y="76" width="92" height="6" rx="3" fill="#1B1F3B"/>
            @break
    @endswitch
</svg>
