import gsap from 'gsap';
import { all, root } from './shared.js';

/**
 * The subsidiaries map: the land is drawn open from the west with a wipe (1.6 seconds), then the
 * pins drop onto the map one after another with a small bounce, and the cards rise in a row.
 */
export const subsidiaries = () => {
    const stage = document.querySelector('[data-map-stage]');

    if (!stage) {
        return;
    }

    const sign = root.dir === 'rtl' ? -1 : 1;
    const land = stage.querySelector('[data-map-land]');
    const pins = all('[data-pin]', stage);
    const cards = all('[data-sub]', stage);
    const once = { trigger: stage, start: 'top 75%', once: true };

    gsap.fromTo(land, { clipPath: sign > 0 ? 'inset(0 100% 0 0)' : 'inset(0 0 0 100%)' }, {
        clipPath: 'inset(0 0% 0 0%)',
        duration: 1.6,
        ease: 'expo.out',
        clearProps: 'clipPath',
        scrollTrigger: once,
    });

    gsap.from(pins, {
        y: -46,
        autoAlpha: 0,
        scale: 0.5,
        duration: 0.9,
        ease: 'back.out(2.2)',
        stagger: 0.14,
        delay: 0.5,
        scrollTrigger: once,
    });

    gsap.from(cards, {
        y: 40,
        autoAlpha: 0,
        duration: 0.8,
        ease: 'expo.out',
        stagger: 0.08,
        scrollTrigger: { trigger: cards[0] ?? stage, start: 'top 92%', once: true },
    });
};
