import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import './shared.js';

/** The header slides away as soon as the page scrolls down and returns at once when it scrolls up or reaches the top. */
export const smartHeader = () => {
    const header = document.querySelector('[data-header]');

    if (!header) {
        return;
    }

    const show = () => gsap.to(header, { y: 0, duration: 0.75, ease: 'expo.out', overwrite: true });
    const hide = () => gsap.to(header, { y: -(header.offsetHeight + 64), duration: 0.75, ease: 'expo.out', overwrite: true });

    ScrollTrigger.create({
        start: 0,
        end: 'max',
        onUpdate: (self) => {
            // Stay while a dropdown or the mobile menu is open, or while the keyboard is in the header (a mouse click leaves the focus there too, so only a keyboard focus counts).
            const focused = document.activeElement;
            const busy = header.querySelector('details[open]')
                || document.documentElement.classList.contains('menu-open')
                || (focused instanceof HTMLElement && header.contains(focused) && focused.matches(':focus-visible'));

            if (self.direction === 1 && self.scroll() > 0 && !busy) {
                hide();
            } else if (self.direction === -1 || self.scroll() <= 0) {
                show();
            }
        },
    });

    header.addEventListener('focusin', show);
};
