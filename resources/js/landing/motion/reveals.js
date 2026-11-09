import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all, canSplitChars, growLetters, rise } from './shared.js';

export const scrollReveals = () => {
    all('[data-anim="heading"], [data-anim="card"]').forEach((element) => {
        gsap.from(element, { ...rise, scrollTrigger: { trigger: element, start: 'top 90%', once: true } });
    });

    /** Big headings: letters stretch up to full height as the heading scrolls into view. */
    all('[data-anim="grow"]').forEach((element) => {
        if (!canSplitChars()) {
            gsap.from(element, { ...rise, scrollTrigger: { trigger: element, start: 'top 90%', once: true } });

            return;
        }

        gsap.set(element, { autoAlpha: 1 });

        const tween = growLetters(element, { stagger: 0.02, ease: 'none', paused: true });

        ScrollTrigger.create({ trigger: element, start: 'top 95%', end: 'top 55%', scrub: 0.6, animation: tween });
    });

    all('[data-anim="rows"], [data-anim="steps"]').forEach((group) => {
        const items = Array.from(group.children);

        gsap.set(items, { autoAlpha: 0, y: 28 });

        ScrollTrigger.batch(items, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => gsap.to(batch, { autoAlpha: 1, y: 0, duration: 0.7, stagger: 0.07, ease: 'power3.out', overwrite: true }),
        });
    });
};
