import gsap from 'gsap';
import { all } from './shared.js';

/**
 * The step list of a process page. A glowing dot rides the end of the line as it fills while the page scrolls.
 * Each step is revealed when it comes into view: its number pops and turns crimson, the card slides in from the
 * start edge, the title rises out of a mask, the text fades up, and the product chips pop in one after another.
 * The step nearest the middle of the screen is lifted (`data-active`). Without JavaScript, or with reduced
 * motion, every step is shown at once. The cartoons belong to the diagram dialog only.
 */
export const processFlow = () => {
    const list = document.querySelector('[data-process-steps]');

    if (!list) {
        return;
    }

    const rtl = document.documentElement.dir === 'rtl';
    const steps = all('[data-flow-step]', list);
    const line = list.querySelector('[data-flow-line]');

    if (line) {
        gsap.fromTo(line, { height: '0%' }, {
            height: '100%',
            ease: 'none',
            scrollTrigger: { trigger: list, start: 'top 60%', end: 'bottom 55%', scrub: 0.5 },
        });
    }

    steps.forEach((step) => {
        const badge = step.querySelector('[data-flow-badge]');
        const card = step.querySelector('[data-flow-card]');
        const title = step.querySelector('[data-flow-title]');
        const text = step.querySelector('[data-flow-text]');
        const chips = all('[data-flow-chips] > li', step);

        badge.setAttribute('data-pending', '');
        gsap.set(card, { autoAlpha: 0, x: rtl ? -48 : 48, rotate: rtl ? -1.5 : 1.5 });
        gsap.set(badge, { scale: 0.5 });
        gsap.set(title, { yPercent: 110 });
        gsap.set([text, ...chips], { autoAlpha: 0, y: 14 });
        gsap.set(chips, { scale: 0.85 });

        gsap.timeline({
            scrollTrigger: { trigger: step, start: 'top 82%', toggleActions: 'play none none none' },
        })
            .to(badge, { scale: 1, duration: 0.55, ease: 'back.out(2.4)' })
            .call(() => badge.removeAttribute('data-pending'), null, '<0.1')
            .to(card, { autoAlpha: 1, x: 0, rotate: 0, duration: 0.7, ease: 'power3.out' }, '<')
            .to(title, { yPercent: 0, duration: 0.5, ease: 'power3.out' }, '<0.2')
            .to(text, { autoAlpha: 1, y: 0, duration: 0.5, ease: 'power2.out' }, '<0.1')
            .to(chips, { autoAlpha: 1, y: 0, scale: 1, duration: 0.45, ease: 'back.out(1.8)', stagger: 0.06 }, '<0.1');

        gsap.timeline({
            scrollTrigger: {
                trigger: step,
                start: 'top 55%',
                end: 'bottom 45%',
                onToggle: (self) => card.toggleAttribute('data-active', self.isActive),
            },
        });
    });
};
