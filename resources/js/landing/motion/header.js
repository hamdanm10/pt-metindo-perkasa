import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import './shared.js';

/** The header slides away when scrolling down past the first section and returns when scrolling up. */
export const smartHeader = () => {
    const header = document.querySelector('[data-header]');

    if (!header) {
        return;
    }

    const first = document.querySelector('#main > section');
    const show = () => gsap.to(header, { y: 0, duration: 0.75, ease: 'expo.out', overwrite: true });
    const hide = () => gsap.to(header, { y: -(header.offsetHeight + 64), duration: 0.75, ease: 'expo.out', overwrite: true });
    const limit = () => (first ? first.getBoundingClientRect().bottom + window.scrollY : 160);

    ScrollTrigger.create({
        start: 0,
        end: 'max',
        onUpdate: (self) => {
            const busy = header.querySelector('details[open]') || header.contains(document.activeElement) || document.documentElement.classList.contains('menu-open');

            if (self.direction === 1 && self.scroll() > limit() && !busy) {
                hide();
            } else if (self.direction === -1 || self.scroll() <= limit()) {
                show();
            }
        },
    });

    header.addEventListener('focusin', show);
};
