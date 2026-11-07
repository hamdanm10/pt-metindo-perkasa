import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

const root = document.documentElement;
const dir = root.dir === 'rtl' ? -1 : 1;
const all = (selector) => gsap.utils.toArray(selector);

/**
 * Headline lines slide up out of a mask. Only lines are split, never characters,
 * so Arabic letters keep their joins. The split is reverted once it has played.
 */
const splitLines = (element) => SplitText.create(element, { type: 'lines', mask: 'lines' });

const hero = () => {
    const timeline = gsap.timeline({ paused: true, defaults: { ease: 'power3.out' } });
    const title = document.querySelector('[data-anim="hero-title"]');
    const items = all('[data-anim="hero-item"]');
    const plates = all('[data-plate]');

    if (title) {
        const split = splitLines(title);

        timeline.from(split.lines, { yPercent: 110, duration: 0.9, stagger: 0.12, ease: 'power4.out', onComplete: () => split.revert() }, 0);
    }

    if (items.length) {
        timeline.from(items, { y: 24, autoAlpha: 0, duration: 0.7, stagger: 0.1 }, 0.35);
    }

    if (plates.length) {
        timeline.from(plates, {
            y: 140,
            scale: 0.88,
            autoAlpha: 0,
            rotation: (index) => (-18 + index * 6) * dir,
            duration: 1.1,
            stagger: 0.12,
            ease: 'back.out(1.3)',
        }, 0.15);
    }

    const start = () => timeline.play();

    if (root.classList.contains('intro-pending')) {
        document.addEventListener('intro:closed', start, { once: true });
    } else {
        start();
    }

    const art = document.querySelector('[data-anim="hero-art"]');
    const section = document.querySelector('[data-hero]');

    if (art && section) {
        gsap.to(art, {
            yPercent: -10,
            ease: 'none',
            scrollTrigger: { trigger: section, start: 'top top', end: 'bottom top', scrub: 0.6 },
        });
    }
};

const headings = () => {
    all('[data-anim="heading"]').forEach((element) => {
        const split = splitLines(element);

        gsap.from(split.lines, {
            yPercent: 110,
            duration: 0.8,
            stagger: 0.1,
            ease: 'power4.out',
            scrollTrigger: { trigger: element, start: 'top 88%', once: true },
            onComplete: () => split.revert(),
        });
    });
};

const rows = () => {
    all('[data-anim="rows"]').forEach((group) => {
        const items = Array.from(group.children);

        gsap.set(items, { autoAlpha: 0, y: 28 });

        ScrollTrigger.batch(items, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => gsap.to(batch, { autoAlpha: 1, y: 0, duration: 0.7, stagger: 0.07, ease: 'power3.out', overwrite: true }),
        });
    });
};

const cards = () => {
    all('[data-anim="card"]').forEach((card) => {
        const from = card.dataset.from ?? 'up';
        const vars = {
            autoAlpha: 0,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: { trigger: card, start: 'top 90%', once: true },
        };

        if (from === 'start') {
            Object.assign(vars, { x: -60 * dir, rotation: -1.5 * dir });
        } else if (from === 'end') {
            Object.assign(vars, { x: 60 * dir, rotation: 1.5 * dir });
        } else {
            vars.y = 60;
        }

        gsap.from(card, vars);
    });
};

const steps = () => {
    all('[data-anim="steps"]').forEach((list) => {
        const items = Array.from(list.children);
        const numbers = gsap.utils.toArray('[data-step-number]', list);
        const hidden = dir > 0 ? 'inset(0% 100% 0% 0%)' : 'inset(0% 0% 0% 100%)';

        gsap.timeline({ scrollTrigger: { trigger: list, start: 'top 82%', once: true } })
            .fromTo(items, { clipPath: hidden }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 0.8, stagger: 0.15, ease: 'power3.inOut' })
            .from(numbers, { scale: 0.3, autoAlpha: 0, rotation: -25 * dir, duration: 0.6, stagger: 0.15, ease: 'back.out(2)' }, '<0.3');
    });
};

const media = () => {
    all('[data-anim="media"]').forEach((element) => {
        gsap.fromTo(element, { scale: 0.94 }, {
            scale: 1,
            ease: 'none',
            scrollTrigger: { trigger: element, start: 'top bottom', end: 'top 45%', scrub: 0.6 },
        });
    });
};

const mm = gsap.matchMedia();

mm.add('(prefers-reduced-motion: no-preference)', () => {
    const setup = () => {
        hero();
        headings();
        rows();
        cards();
        steps();
        media();

        window.__animReady = true;
        root.classList.remove('anim-hide');
        ScrollTrigger.refresh();
    };

    document.fonts.ready.then(setup);

    return () => root.classList.remove('anim-hide');
});
