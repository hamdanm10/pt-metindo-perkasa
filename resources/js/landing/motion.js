import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const root = document.documentElement;
const all = (selector) => gsap.utils.toArray(selector);

/** One gentle rise-and-fade for everything. No splitting, no parallax, no pinning. */
const rise = { autoAlpha: 0, y: 28, duration: 0.7, ease: 'power3.out' };

const hero = () => {
    const items = all('[data-anim="hero-title"], [data-anim="hero-item"]');

    if (!items.length) {
        return;
    }

    const start = () => gsap.from(items, { ...rise, stagger: 0.1 });

    if (root.classList.contains('intro-pending')) {
        document.addEventListener('intro:closed', start, { once: true });
    } else {
        start();
    }
};

const onScroll = () => {
    all('[data-anim="heading"], [data-anim="card"]').forEach((element) => {
        gsap.from(element, { ...rise, scrollTrigger: { trigger: element, start: 'top 90%', once: true } });
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

const mm = gsap.matchMedia();

mm.add('(prefers-reduced-motion: no-preference)', () => {
    document.fonts.ready.then(() => {
        hero();
        onScroll();

        window.__animReady = true;
        root.classList.remove('anim-hide');
        ScrollTrigger.refresh();
    });

    return () => root.classList.remove('anim-hide');
});
