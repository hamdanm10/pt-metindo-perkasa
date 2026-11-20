import gsap from 'gsap';
import { all } from './shared.js';

/**
 * The figures in the About opening count up to their number once the headline has settled. The
 * markup already holds the final number, so the page reads the same without scripts.
 */
export const aboutStats = () => {
    all('[data-stat]').forEach((element, index) => {
        const value = { n: 0 };

        gsap.to(value, {
            n: Number(element.dataset.stat),
            duration: 1.6,
            delay: 0.6 + index * 0.12,
            ease: 'power3.out',
            onUpdate: () => (element.textContent = String(Math.round(value.n))),
        });
    });
};
