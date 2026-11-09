import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger, SplitText);

const root = document.documentElement;
const all = (selector) => gsap.utils.toArray(selector);

/** Arabic letters join, so headlines in Arabic are never split into characters. */
const canSplitChars = () => !root.lang.toLowerCase().startsWith('ar');

const rise = { autoAlpha: 0, y: 28, duration: 0.7, ease: 'power3.out' };

let lenis = null;

/** Eased wheel scrolling, as on the reference site. Stopped while the intro is open. */
const smoothScroll = () => {
    lenis = new Lenis({ lerp: 0.1, anchors: false });

    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    if (root.classList.contains('intro-pending')) {
        lenis.stop();
        document.addEventListener('intro:closed', () => lenis.start(), { once: true });
    }

    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        const target = link.getAttribute('href').length > 1 ? document.querySelector(link.getAttribute('href')) : null;

        if (target) {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                lenis.scrollTo(target); // honours scroll-padding-top from the stylesheet
            });
        }
    });
};

/** Letters grow up from the baseline one after another, as in the reference hero. */
const growLetters = (element, vars, { revert = false } = {}) => {
    const split = SplitText.create(element, { type: 'words,chars' });

    gsap.set(split.chars, { transformOrigin: '50% 100%' });

    return gsap.fromTo(split.chars, { scaleY: 0 }, {
        scaleY: 1,
        ease: 'power3.out',
        ...vars,
        onComplete: revert ? () => split.revert() : undefined,
    });
};

const hero = () => {
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

const scrollReveals = () => {
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

/**
 * Catalog cards, as in the reference hero: they start as one narrow panel beside the
 * heading and spread into a row while the section scrolls into view (scrubbed). Only on
 * wide screens. Without it, or without JavaScript, the cards are simply the final row.
 */
const catalog = () => {
    const stage = document.querySelector('[data-catalog-stage]');

    if (!stage) {
        return;
    }

    const rtl = root.dir === 'rtl';
    const tiles = all('[data-catalog-tile]', stage);
    const contents = all('[data-catalog-tile-content]', stage);
    const captions = all('[data-catalog-caption]', stage);

    /** Where tile `index` sits in the compact panel, relative to the stage. */
    const panel = (tile, index) => {
        const width = stage.offsetWidth;
        const panelWidth = width * 0.44;
        const slice = panelWidth / tiles.length;
        const slot = rtl ? tiles.length - 1 - index : index;
        const left = rtl ? 0 : width - panelWidth;
        const height = Math.min(tile.offsetHeight, Math.max(stage.firstElementChild.offsetHeight, tile.offsetHeight * 0.8));

        return { x: left + slot * slice - tile.offsetLeft, y: -tile.offsetTop + (index % 2) * 14, scaleX: slice / tile.offsetWidth, scaleY: height / tile.offsetHeight };
    };

    const timeline = gsap.timeline({
        defaults: { ease: 'none' },
        scrollTrigger: { trigger: stage, start: 'top 90%', end: 'top 15%', scrub: 0.6, invalidateOnRefresh: true },
    });

    timeline
        .fromTo(tiles, {
            transformOrigin: '0 0',
            x: (index, tile) => panel(tile, index).x,
            y: (index, tile) => panel(tile, index).y,
            scaleX: (index, tile) => panel(tile, index).scaleX,
            scaleY: (index, tile) => panel(tile, index).scaleY,
        }, { x: 0, y: 0, scaleX: 1, scaleY: 1, ease: 'power2.inOut' }, 0)
        .fromTo(contents, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.3 }, 0.7)
        .fromTo(captions, { autoAlpha: 0, y: 16 }, { autoAlpha: 1, y: 0, duration: 0.3, stagger: 0.05 }, 0.7);
};

/** The header slides away when scrolling down and returns when scrolling up. */
const smartHeader = () => {
    const header = document.querySelector('[data-header]');

    if (!header) {
        return;
    }

    const show = () => gsap.to(header, { yPercent: 0, duration: 0.4, ease: 'power3.out', overwrite: true });
    const hide = () => gsap.to(header, { yPercent: -100, duration: 0.4, ease: 'power3.out', overwrite: true });

    ScrollTrigger.create({
        start: 0,
        end: 'max',
        onUpdate: (self) => {
            const busy = header.querySelector('details[open]') || header.contains(document.activeElement);

            if (self.direction === 1 && self.scroll() > 160 && !busy) {
                hide();
            } else if (self.direction === -1 || self.scroll() <= 160) {
                show();
            }
        },
    });

    header.addEventListener('focusin', show);
};

const mm = gsap.matchMedia();

mm.add('(prefers-reduced-motion: no-preference)', () => {
    smoothScroll();

    document.fonts.ready.then(() => {
        hero();
        scrollReveals();
        smartHeader();
        gsap.matchMedia().add('(min-width: 1024px)', catalog);

        window.__animReady = true;
        root.classList.remove('anim-hide');
        ScrollTrigger.refresh();
    });

    return () => {
        lenis?.destroy();
        root.classList.remove('anim-hide');
    };
});

/** Accordion for the services. Works with or without motion, and is open without JavaScript. */
const accordion = () => {
    const instant = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    all('[data-accordion]').forEach((list) => {
        const items = Array.from(list.querySelectorAll('[data-acc-item]'));

        const setState = (item, open, animate) => {
            const button = item.querySelector('[data-acc-button]');
            const panel = item.querySelector('[data-acc-panel]');

            button.setAttribute('aria-expanded', String(open));
            item.toggleAttribute('data-open', open);

            if (!animate || instant) {
                gsap.set(panel, { height: open ? 'auto' : 0, autoAlpha: open ? 1 : 0 });

                return;
            }

            gsap.to(panel, {
                height: open ? 'auto' : 0,
                autoAlpha: open ? 1 : 0,
                duration: 0.55,
                ease: 'power3.inOut',
                overwrite: true,
                onComplete: () => ScrollTrigger.refresh(),
            });
        };

        items.forEach((item, index) => {
            gsap.set(item.querySelector('[data-acc-panel]'), { overflow: 'hidden' });
            setState(item, index === 0, false);

            item.querySelector('[data-acc-button]').addEventListener('click', () => {
                const opening = !item.hasAttribute('data-open');

                items.forEach((other) => setState(other, other === item && opening, true));
            });
        });
    });
};

accordion();
