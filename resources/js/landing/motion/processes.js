import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all } from './shared.js';

/**
 * The process catalogue page. The hero count runs up to its number, and the four steps are
 * joined by a line that is drawn as the page scrolls, each step appearing when the line reaches
 * it. The category list has its own script, process-tabs.js.
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
