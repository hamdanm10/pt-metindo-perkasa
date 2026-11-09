import gsap from 'gsap';

/**
 * Hover of the landing button, as measured on the reference. The fill rises from below in about
 * 0.6 seconds (`expo.out`) and leaves through the top when the pointer goes. The label has two
 * copies: the shown one rolls up 32 px while the other comes from 32 px below, word by word
 * (0.04 seconds apart, 1 second, `expo.out`). The same roll runs on enter and on leave, always
 * upwards. The arrow steps 4 px back. Only for pointer devices and without reduced motion.
 */
const hover = window.matchMedia('(hover: hover)').matches;
const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (hover && !calm) {
    document.documentElement.classList.add('btn-motion');

    document.querySelectorAll('[data-btn]').forEach((button) => {
        const fill = button.querySelector('[data-btn-fill]');
        const arrow = button.querySelector('[data-btn-arrow]');
        const copies = Array.from(button.querySelectorAll('[data-btn-copy]')).map((copy) => Array.from(copy.querySelectorAll('[data-btn-word]')));
        const hoverColor = button.dataset.hoverText;
        const rest = getComputedStyle(button).color;
        let shown = 0;

        if (!fill || copies.length < 2) {
            return;
        }

        gsap.set(fill, { yPercent: 100 });
        gsap.set(copies[1], { y: 32 });

        const roll = () => {
            const leaving = copies[shown];

            shown = 1 - shown;

            gsap.set(copies[shown], { y: 32 });
            gsap.to(leaving, { y: -32, duration: 1, ease: 'expo.out', stagger: 0.04, overwrite: true });
            gsap.to(copies[shown], { y: 0, duration: 1, ease: 'expo.out', stagger: 0.04, overwrite: true });
        };

        const enter = () => {
            gsap.fromTo(fill, { yPercent: 100 }, { yPercent: 0, duration: 0.6, delay: 0.04, ease: 'expo.out', overwrite: true });
            gsap.to(button, { color: hoverColor, duration: 0.3, overwrite: 'auto' });

            if (arrow) {
                gsap.to(arrow, { x: -4, duration: 0.5, ease: 'power3.out', overwrite: true });
            }

            roll();
        };

        const leave = () => {
            gsap.to(fill, { yPercent: -100, duration: 0.6, delay: 0.07, ease: 'expo.out', overwrite: true, onComplete: () => gsap.set(fill, { yPercent: 100 }) });
            gsap.to(button, { color: rest, duration: 0.3, overwrite: 'auto' });

            if (arrow) {
                gsap.to(arrow, { x: 0, duration: 0.5, ease: 'power3.out', overwrite: true });
            }

            roll();
        };

        let active = false;
        const on = () => { if (!active) { active = true; enter(); } };
        const off = () => { if (active) { active = false; leave(); } };

        button.addEventListener('mouseenter', on);
        button.addEventListener('mouseleave', off);
        button.addEventListener('focus', () => { if (button.matches(':focus-visible')) { on(); } });
        button.addEventListener('blur', off);
    });
}
