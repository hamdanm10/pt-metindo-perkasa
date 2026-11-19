@props(['class' => ''])

{{-- Decorative hero scene: the professor runs a plating line. Transparent, drawn for the dark hero, moved by resources/js/landing/motion/professor.js (parts found by data-part). --}}
<svg viewBox="0 0 1200 1410" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}" aria-hidden="true" focusable="false" data-professor-scene="">
    <g data-part="frame">
        <rect x="56" y="104" width="28" height="990" rx="10" fill="#3A4276"/>
        <rect x="1116" y="104" width="28" height="990" rx="10" fill="#3A4276"/>
        <rect x="40" y="70" width="1120" height="34" rx="17" fill="#6E78AE"/>
        <rect x="52" y="78" width="1096" height="8" rx="4" fill="#98A2C4" opacity=".7"/>
        <circle data-part="lamp-left" cx="70" cy="46" r="15" fill="#D6283A"/>
        <circle data-part="lamp-right" cx="1130" cy="46" r="15" fill="#B5B0F2"/>
        <rect x="64" y="58" width="12" height="14" fill="#6E78AE"/><rect x="1124" y="58" width="12" height="14" fill="#6E78AE"/>
    </g>

    <g data-part="dials">
        <circle cx="70" cy="330" r="42" fill="#FFFFFF" stroke="#98A2C4" stroke-width="8"/>
        <path d="M44 350a30 30 0 0 1 8-30M96 350a30 30 0 0 0-8-30" fill="none" stroke="#D6283A" stroke-width="6" stroke-linecap="round" opacity=".0"/>
        <path d="M44 322l6 4M96 322l-6 4M70 296v8" stroke="#98A2C4" stroke-width="4" stroke-linecap="round"/>
        <g data-part="dial-left" transform="translate(70 330) rotate(-40)"><path d="M0 6V-30" stroke="#D6283A" stroke-width="6" stroke-linecap="round"/><circle r="5" fill="#1B1F3B"/></g>
        <circle cx="1130" cy="640" r="42" fill="#FFFFFF" stroke="#98A2C4" stroke-width="8"/>
        <path d="M1104 632l6 4M1156 632l-6 4M1130 606v8" stroke="#98A2C4" stroke-width="4" stroke-linecap="round"/>
        <g data-part="dial-right" transform="translate(1130 640) rotate(30)"><path d="M0 6V-30" stroke="#7FA3E8" stroke-width="6" stroke-linecap="round"/><circle r="5" fill="#1B1F3B"/></g>
    </g>

    <!-- professor -->
    <g data-part="professor" transform="translate(600 1400) scale(1.2) translate(-600 -1400)">
        <path d="M400 1200V960Q400 842 520 842H680Q800 842 800 960V1200Z" fill="#FFFFFF"/>
        <path d="M552 842L600 976 648 842Z" fill="#D9DCE8"/>
        <path d="M600 990V1200" stroke="#D9DCE8" stroke-width="5"/>
        <path d="M588 862h24l10 118-22 46-22-46Z" fill="#D6283A"/>
        <rect x="568" y="790" width="64" height="64" rx="18" fill="#E8B894"/>
        <g data-part="head" transform="rotate(0 600 800)">
            <ellipse cx="498" cy="665" rx="40" ry="62" fill="#F3F2FF"/>
            <ellipse cx="702" cy="665" rx="40" ry="62" fill="#F3F2FF"/>
            <ellipse cx="600" cy="600" rx="106" ry="62" fill="#F3F2FF"/>
            <ellipse cx="500" cy="708" rx="17" ry="27" fill="#F0C4A2"/>
            <ellipse cx="700" cy="708" rx="17" ry="27" fill="#F0C4A2"/>
            <rect x="500" y="596" width="200" height="214" rx="84" fill="#F7D3B5"/>
            <rect x="486" y="566" width="228" height="24" rx="12" fill="#6D5BD0"/>
            <circle cx="562" cy="574" r="33" fill="#CFE0FF" stroke="#6D5BD0" stroke-width="9"/>
            <circle cx="638" cy="574" r="33" fill="#CFE0FF" stroke="#6D5BD0" stroke-width="9"/>
            <path d="M546 562q10-14 26-12" fill="none" stroke="#FFFFFF" stroke-width="7" stroke-linecap="round"/>
            <path d="M622 562q10-14 26-12" fill="none" stroke="#FFFFFF" stroke-width="7" stroke-linecap="round"/>
            <path d="M524 656q32-24 66-4M610 652q34-20 66 4" fill="none" stroke="#D9DCE8" stroke-width="13" stroke-linecap="round"/>
            <g data-part="eye-left" transform="translate(556 706)"><circle r="30" fill="#FFFFFF"/><circle data-part="pupil-left" r="13" fill="#1B1F3B"/></g>
            <g data-part="eye-right" transform="translate(644 706)"><circle r="30" fill="#FFFFFF"/><circle data-part="pupil-right" r="13" fill="#1B1F3B"/></g>
            <circle cx="556" cy="706" r="37" fill="none" stroke="#6D5BD0" stroke-width="8"/>
            <circle cx="644" cy="706" r="37" fill="none" stroke="#6D5BD0" stroke-width="8"/>
            <path d="M591 702q9-9 18 0" fill="none" stroke="#6D5BD0" stroke-width="8" stroke-linecap="round"/>
            <ellipse cx="600" cy="752" rx="20" ry="26" fill="#EDBE9B"/>
            <path d="M512 772q0 76 88 86 88-10 88-86-34 22-88 14-54 8-88-14Z" fill="#F3F2FF"/>
            <path d="M538 780q32-32 62-6 30-26 62 6-30 22-62 8-32 14-62-8Z" fill="#FFFFFF"/>
            <path d="M568 818q32 26 64 0" fill="none" stroke="#D6283A" stroke-width="10" stroke-linecap="round"/>
        </g>
        <g data-part="console">
            <rect x="512" y="1150" width="176" height="260" rx="20" fill="#D9DCE8"/>
            <circle cx="560" cy="1228" r="25" fill="#D6283A"/>
            <circle cx="640" cy="1228" r="32" fill="#FFFFFF" stroke="#98A2C4" stroke-width="6"/>
            <g data-part="needle" transform="translate(640 1228) rotate(-50)"><path d="M0 4V-24" stroke="#D6283A" stroke-width="6" stroke-linecap="round"/><circle r="5" fill="#1B1F3B"/></g>
            <circle data-part="light-a" cx="560" cy="1290" r="10" fill="#F2B84B"/>
            <circle data-part="light-b" cx="596" cy="1290" r="10" fill="#B5B0F2"/>
            <rect x="534" y="1330" width="132" height="12" rx="6" fill="#98A2C4"/>
            <rect x="534" y="1356" width="132" height="12" rx="6" fill="#98A2C4"/>
        </g>
        <g data-part="lever" transform="translate(600 1160) rotate(0)">
            <rect x="-8" y="-136" width="16" height="136" rx="8" fill="#98A2C4"/>
            <circle cy="-136" r="21" fill="#6D5BD0"/>
            <circle r="24" fill="#6E78AE"/>
        </g>
        <g data-part="clipboard" transform="translate(430 1030) rotate(-8)">
            <rect x="-4" y="-120" width="104" height="146" rx="12" fill="#B6874C"/>
            <rect x="6" y="-108" width="84" height="122" rx="6" fill="#FFFFFF"/>
            <rect x="32" y="-124" width="32" height="20" rx="6" fill="#98A2C4"/>
            <path d="M18-80h60M18-60h60M18-40h46M18-20h56" stroke="#B5B0F2" stroke-width="6" stroke-linecap="round"/>
        </g>
        <g data-part="arm-left" transform="translate(430 890) rotate(154.8)">
        <rect x="-32" y="-32" width="204" height="64" rx="32" fill="#DAD7F5"/>
        <g data-part="fore-left" transform="translate(140 0) rotate(-122.4)">
            <rect x="-32" y="-30" width="158" height="60" rx="30" fill="#DAD7F5"/>
            <rect x="116" y="-34" width="24" height="68" rx="9" fill="#D9DCE8"/>
            <g transform="translate(156 0)">
                <ellipse cx="14" cy="0" rx="46" ry="38" fill="#D6283A"/>
                <ellipse cx="2" cy="-36" rx="18" ry="24" fill="#D6283A" transform="rotate(-18 2 -36)"/>
                <path d="M26-16q22 4 30 18M28 6q22 4 26 20" fill="none" stroke="#B01022" stroke-width="5" stroke-linecap="round" opacity=".55"/>
            </g>
        </g>
    </g>
        <g data-part="arm-right" transform="translate(770 890) rotate(98.2)">
        <rect x="-32" y="-32" width="204" height="64" rx="32" fill="#DAD7F5"/>
        <g data-part="fore-right" transform="translate(140 0) rotate(-276.5)">
            <rect x="-32" y="-30" width="158" height="60" rx="30" fill="#DAD7F5"/>
            <rect x="116" y="-34" width="24" height="68" rx="9" fill="#D9DCE8"/>
            <g transform="translate(156 0)">
                <ellipse cx="14" cy="0" rx="46" ry="38" fill="#D6283A"/>
                <ellipse cx="2" cy="-36" rx="18" ry="24" fill="#D6283A" transform="rotate(-18 2 -36)"/>
                <path d="M26-16q22 4 30 18M28 6q22 4 26 20" fill="none" stroke="#B01022" stroke-width="5" stroke-linecap="round" opacity=".55"/>
            </g>
        </g>
    </g>
    </g>

    <g data-part="tank-1">
        <rect x="40" y="1090" width="420" height="300" rx="26" fill="#1F4FB5"/>
        <rect x="54" y="1140" width="392" height="238" rx="16" fill="#3E73E8"/>
    </g>
    <g data-part="tank-2">
        <rect x="740" y="1090" width="420" height="300" rx="26" fill="#6D5BD0"/>
        <rect x="754" y="1140" width="392" height="238" rx="16" fill="#D9962A"/>
    </g>

    <g data-part="carriage" transform="translate(250 120)">
        <circle cx="-46" cy="-26" r="13" fill="#D9DCE8"/><circle cx="46" cy="-26" r="13" fill="#D9DCE8"/>
        <rect x="-80" y="-24" width="160" height="48" rx="14" fill="#D6283A"/>
        <circle data-part="light-c" cx="0" cy="0" r="8" fill="#F3F2FF"/>
    </g>
    <line data-part="rope" x1="250" y1="144" x2="250" y2="284" stroke="#D9DCE8" stroke-width="6" stroke-linecap="round"/>
    <g data-part="rack" transform="translate(250 300) rotate(0)">
        <circle cy="-16" r="10" fill="none" stroke="#D9DCE8" stroke-width="6"/>
        <rect x="-100" y="-8" width="200" height="16" rx="8" fill="#D9DCE8"/>
        <path d="M-72 8V62M0 8V58M72 8V62" stroke="#D9DCE8" stroke-width="5" stroke-linecap="round"/>
        <g data-part="plated" style="color:#98A2C4">
            <path transform="translate(-72 98)" d="M27.2 -6.5 L35.7 -4.8 L35.7 4.8 L27.2 6.5 L23.9 14.6 L28.6 21.8 L21.8 28.6 L14.6 23.9 L6.5 27.2 L4.8 35.7 L-4.8 35.7 L-6.5 27.2 L-14.6 23.9 L-21.8 28.6 L-28.6 21.8 L-23.9 14.6 L-27.2 6.5 L-35.7 4.8 L-35.7 -4.8 L-27.2 -6.5 L-23.9 -14.6 L-28.6 -21.8 L-21.8 -28.6 L-14.6 -23.9 L-6.5 -27.2 L-4.8 -35.7 L4.8 -35.7 L6.5 -27.2 L14.6 -23.9 L21.8 -28.6 L28.6 -21.8 L23.9 -14.6Z M10 0a10 10 0 1 0 -20 0a10 10 0 1 0 20 0Z" fill="currentColor" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill-rule="evenodd"/>
            <path d="M-24 58h48a14 14 0 0 1 14 14v70a14 14 0 0 1-14 14h-48a14 14 0 0 1-14-14V72a14 14 0 0 1 14-14ZM0 80a8 8 0 1 0 0 .1ZM0 124a8 8 0 1 0 0 .1Z" fill="currentColor" fill-rule="evenodd" transform="translate(0 0)"/>
            <path transform="translate(72 98)" d="M28.6 16.5 L0 33 L-28.6 16.5 L-28.6 -16.5 L-0 -33 L28.6 -16.5Z M12 0a12 12 0 1 0 -24 0a12 12 0 1 0 24 0Z" fill="currentColor" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill-rule="evenodd"/>
        </g>
        <g transform="translate(-72 98)"><path data-part="spark" d="M0-30C3-9 9-3 30 0 9 3 3 9 0 30-3 9-9 3-30 0-9-3-3-9 0-30Z" fill="#FFFFFF" transform="scale(0)"/></g>
        <g transform="translate(72 70)"><path data-part="spark" d="M0-30C3-9 9-3 30 0 9 3 3 9 0 30-3 9-9 3-30 0-9-3-3-9 0-30Z" fill="#F2B84B" transform="scale(0)"/></g>
        <g transform="translate(0 150)"><path data-part="spark" d="M0-30C3-9 9-3 30 0 9 3 3 9 0 30-3 9-9 3-30 0-9-3-3-9 0-30Z" fill="#FFFFFF" transform="scale(0)"/></g>
    </g>

    <rect x="54" y="1140" width="392" height="238" rx="16" fill="#7FA3E8" opacity=".5"/>
    <rect x="754" y="1140" width="392" height="238" rx="16" fill="#F2B84B" opacity=".5"/>
    <rect x="26" y="1072" width="448" height="28" rx="14" fill="#B5B0F2"/>
    <rect x="76" y="1000" width="28" height="372" rx="12" fill="#C9CFDD"/>
    <rect x="68" y="984" width="44" height="26" rx="9" fill="#D6283A"/>
    <rect x="396" y="1000" width="28" height="372" rx="12" fill="#C9CFDD"/>
    <rect x="388" y="984" width="44" height="26" rx="9" fill="#7FA3E8"/>
    <rect x="726" y="1072" width="448" height="28" rx="14" fill="#B5B0F2"/>
    <rect x="776" y="1000" width="28" height="372" rx="12" fill="#C9CFDD"/>
    <rect x="768" y="984" width="44" height="26" rx="9" fill="#D6283A"/>
    <rect x="1096" y="1000" width="28" height="372" rx="12" fill="#C9CFDD"/>
    <rect x="1088" y="984" width="44" height="26" rx="9" fill="#7FA3E8"/>
    <path d="M410 996C410 1090 440 1180 492 1236" fill="none" stroke="#B5B0F2" stroke-width="12" stroke-linecap="round" transform="translate(0 0)"/>
    <path data-part="current-1" d="M410 996C410 1090 440 1180 492 1236" fill="none" stroke="#F2B84B" stroke-width="6" stroke-linecap="round" stroke-dasharray="4 26" opacity="0"/>
    <path d="M790 996C790 1090 760 1180 708 1236" fill="none" stroke="#B5B0F2" stroke-width="12" stroke-linecap="round" transform="translate(0 0)"/>
    <path data-part="current-2" d="M790 996C790 1090 760 1180 708 1236" fill="none" stroke="#F2B84B" stroke-width="6" stroke-linecap="round" stroke-dasharray="4 26" opacity="0"/>
    <g data-part="bubbles-1" fill="#F3F2FF" opacity=".9"><circle cx="100" cy="1360" r="10"/><circle cx="146.7" cy="1340" r="7"/><circle cx="193.3" cy="1370" r="13"/><circle cx="240" cy="1330" r="8"/><circle cx="286.7" cy="1355" r="11"/><circle cx="333.3" cy="1345" r="6"/></g>
    <g data-part="bubbles-2" fill="#F3F2FF" opacity=".9"><circle cx="800" cy="1360" r="10"/><circle cx="846.7" cy="1340" r="7"/><circle cx="893.3" cy="1370" r="13"/><circle cx="940" cy="1330" r="8"/><circle cx="986.7" cy="1355" r="11"/><circle cx="1033.3" cy="1345" r="6"/></g>
</svg>
