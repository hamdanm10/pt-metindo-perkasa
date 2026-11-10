import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all, root } from './shared.js';

/**
 * The contact page. The detail tiles rise in rows as they come into view (0.9 seconds,
 * `expo.out`), the message card opens from the side the tiles are on while its fields follow one
 * after another, and the map section uses the same entrance as the About page. Focus on a field
 * and the hover of a tile are CSS.
 */
export const contactPage = () => {
    const tiles = document.querySelector('[data-contact-tiles]');

    if (tiles) {
        const items = Array.from(tiles.children);

        gsap.set(items, { autoAlpha: 0, y: 48 });

        ScrollTrigger.batch(items, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => gsap.to(batch, { autoAlpha: 1, y: 0, duration: 0.9, stagger: 0.08, ease: 'expo.out', overwrite: true }),
        });
    }

    const card = document.querySelector('[data-contact-card]');

    if (card) {
        const sign = root.dir === 'rtl' ? -1 : 1;
        const fields = all('.field, [data-send]', card);
        const once = { trigger: card, start: 'top 85%', once: true };

        gsap.fromTo(card, { autoAlpha: 0, x: 60 * sign }, { autoAlpha: 1, x: 0, duration: 1.1, ease: 'expo.out', scrollTrigger: once });
        gsap.from(fields, { y: 24, autoAlpha: 0, duration: 0.8, ease: 'expo.out', stagger: 0.08, delay: 0.35, scrollTrigger: once });
    }
};
