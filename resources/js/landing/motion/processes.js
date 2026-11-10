import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all } from './shared.js';

/**
 * The process catalogue page. The hero count runs up to its number, the category cards rise in
 * rows as they come into view, and the four steps are joined by a line that is drawn as the
 * page scrolls, each step appearing when the line reaches it.
 */
export const processes = () => {
    const count = document.querySelector('[data-count]');

    if (count) {
        const target = Number(count.dataset.count);
        const value = { n: 0 };

        gsap.to(value, {
            n: target,
            duration: 1.6,
            delay: 0.6,
            ease: 'power3.out',
            onUpdate: () => (count.textContent = String(Math.round(value.n))),
        });
    }

    const grid = document.querySelector('[data-process-grid]');

    if (grid) {
        const cards = Array.from(grid.children);

        gsap.set(cards, { autoAlpha: 0, y: 48 });

        ScrollTrigger.batch(cards, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => gsap.to(batch, { autoAlpha: 1, y: 0, duration: 0.9, stagger: 0.08, ease: 'expo.out', overwrite: true }),
        });
    }

    const steps = document.querySelector('[data-steps]');

    if (steps) {
        const items = all('[data-step]', steps);
        const line = steps.querySelector('[data-steps-line]');

        gsap.set(items, { autoAlpha: 0, y: 36 });

        const timeline = gsap.timeline({
            scrollTrigger: { trigger: steps, start: 'top 80%', end: 'bottom 60%', scrub: 0.6 },
        });

        if (line && line.offsetParent !== null) {
            timeline.fromTo(line, { scaleX: 0 }, { scaleX: 1, ease: 'none', duration: items.length }, 0);
        }

        items.forEach((item, index) => {
            timeline.to(item, { autoAlpha: 1, y: 0, ease: 'power3.out', duration: 0.8 }, index * 0.9);
        });
    }
};
