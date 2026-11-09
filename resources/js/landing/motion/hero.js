import gsap from 'gsap';
import { all, canSplitChars, growLetters, rise, root } from './shared.js';

export const hero = () => {
    const title = document.querySelector('[data-anim="hero-title"]');
    const items = all('[data-anim="hero-item"]');

    if (!title && !items.length) {
        return;
    }

    const timeline = gsap.timeline({ paused: true });

    if (title) {
        if (canSplitChars()) {
            timeline.add(growLetters(title, { duration: 0.9, stagger: 0.035 }, { revert: true }), 0);
        } else {
            timeline.from(title, { ...rise, duration: 0.9 }, 0);
        }
    }

    if (items.length) {
        timeline.from(items, { ...rise, stagger: 0.1 }, title ? 0.5 : 0);
    }

    const start = () => timeline.play();

    if (root.classList.contains('intro-pending')) {
        document.addEventListener('intro:closed', start, { once: true });
    } else {
        start();
    }

    const panel = document.querySelector('[data-hero-panel]');
    const section = document.querySelector('[data-hero]');

    if (panel && section) {
        gsap.to(panel, {
            scale: 0.86,
            transformOrigin: '50% 0%',
            ease: 'none',
            scrollTrigger: { trigger: section, start: 'top top', end: 'bottom top', scrub: true },
        });
    }
};
