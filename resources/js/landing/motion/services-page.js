import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all, root } from './shared.js';

/**
 * The services page. Each illustration opens with a wipe (1.2 seconds, `expo.out`) from the side
 * its text is not on while its picture drifts a little against the scroll, and the big number
 * rises from under its edge. The four steps are joined by a line that is drawn as the page
 * scrolls, each step appearing when the line reaches it.
 */
export const servicesPage = () => {
    const sign = root.dir === 'rtl' ? -1 : 1;

    all('[data-svc-art]').forEach((art, index) => {
        const fromEnd = index % 2 === 0 ? sign : -sign;
        const inner = art.querySelector('[data-svc-art-inner]');
        const number = art.querySelector('[data-svc-number]');

        gsap.fromTo(art, { clipPath: fromEnd > 0 ? 'inset(0 0 0 100% round 8px)' : 'inset(0 100% 0 0 round 8px)' }, {
            clipPath: 'inset(0 0% 0 0% round 8px)',
            duration: 1.2,
            ease: 'expo.out',
            clearProps: 'clipPath',
            scrollTrigger: { trigger: art, start: 'top 85%', once: true },
        });

        gsap.fromTo(number, { yPercent: 100 }, {
            yPercent: 0,
            duration: 1,
            delay: 0.35,
            ease: 'expo.out',
            scrollTrigger: { trigger: art, start: 'top 85%', once: true },
        });

        gsap.fromTo(inner, { yPercent: -6 }, {
            yPercent: 6,
            ease: 'none',
            scrollTrigger: { trigger: art, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });

    const steps = document.querySelector('[data-svc-steps]');

    if (steps) {
        const items = all('[data-svc-step]', steps);
        const line = steps.querySelector('[data-svc-line]');

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
