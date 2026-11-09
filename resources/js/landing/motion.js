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
 * Catalog deck, measured from the reference hero: four cards of the same size, each shifted
 * sideways and stacked by z-index. Every 4 seconds the front card changes (0 to 3 and back).
 * The whole stack shrinks to 85% and returns while the new front card tilts and settles.
 * When the page scrolls on, the cards leave the deck and travel down to the row (scrubbed).
 * Wide screens only. Without it the deck is static and the row is plain.
 */
const catalog = () => {
    const stage = document.querySelector('[data-catalog-stage]');
    const slot = stage?.querySelector('[data-catalog-panel]');

    if (!stage || !slot) {
        return;
    }

    const deck = slot.querySelector('[data-deck]');
    const cards = all('[data-deck-card]', slot);
    const medias = all('[data-deck-media]', slot);
    const tiles = all('[data-catalog-tile]', stage);
    const captions = all('[data-catalog-caption]', stage);
    const action = stage.querySelector('[data-catalog-action]');

    let active = 0;
    let direction = 1;
    let top = cards.length;
    let playing = true;

    gsap.set(deck, { transformOrigin: '50% 100%' });
    gsap.set(cards, { transformOrigin: '0 0' });
    gsap.set(tiles, { autoAlpha: 0 });
    gsap.set(captions, { autoAlpha: 0 });
    gsap.set(action, { autoAlpha: 0 });

    const slide = () => {
        if (!playing) {
            return;
        }

        if (active + direction >= cards.length || active + direction < 0) {
            direction *= -1;
        }

        active += direction;

        const media = medias[active];
        const tilt = gsap.timeline();

        tilt
            .to(deck, { scale: 0.85, duration: 0.28, ease: 'power2.inOut' }, 0)
            .to(media, { rotation: -4.75, scale: 1.047, duration: 0.28, ease: 'power2.inOut' }, 0)
            .call(() => {
                top += 1;
                cards[active].style.zIndex = String(top);
            }, null, 0.34)
            .to(deck, { scale: 1, duration: 0.35, ease: 'power2.out' }, 0.28)
            .to(media, { rotation: 0.55, scale: 0.994, duration: 0.3, ease: 'power2.out' }, 0.28)
            .to(media, { rotation: 0, scale: 1, duration: 0.77, ease: 'power2.inOut' }, 0.58);
    };

    const loop = gsap.delayedCall(4, function tick() {
        slide();
        loop.restart(true);
    });

    /** Where card `index` has to go to land on its tile, relative to where it sits now. */
    const toRow = (card, index) => {
        const tile = tiles[index];

        return {
            x: tile.offsetLeft - (slot.offsetLeft + card.offsetLeft),
            y: tile.offsetTop - (slot.offsetTop + card.offsetTop),
            scale: tile.offsetWidth / card.offsetWidth,
        };
    };

    gsap.timeline({
        defaults: { ease: 'none' },
        scrollTrigger: {
            trigger: stage,
            start: 'top top',
            end: () => `+=${Math.max(300, tiles[0].offsetTop - window.innerHeight * 0.2)}`,
            scrub: 0.6,
            invalidateOnRefresh: true,
            onUpdate: (self) => {
                playing = self.progress < 0.01;
            },
        },
    })
        .to(cards, {
            x: (index, card) => toRow(card, index).x,
            y: (index, card) => toRow(card, index).y,
            scale: (index, card) => toRow(card, index).scale,
            ease: 'power2.inOut',
        }, 0)
        .set(tiles, { autoAlpha: 1 }, 0.98)
        .set(cards, { autoAlpha: 0 }, 0.98)
        .fromTo(captions, { autoAlpha: 0, y: 16 }, { autoAlpha: 1, y: 0, duration: 0.3, stagger: 0.05 }, 0.7)
        .fromTo(action, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.3 }, 0.8);

    return () => loop.kill();
};

/**
 * Video section: the box opens up from a smaller rounded frame as it scrolls into view, the
 * thumbnail drifts slowly inside it, and the play button pops in once the box is on screen.
 */
const video = () => {
    const box = document.querySelector('[data-video]');

    if (!box) {
        return;
    }

    const parallax = box.querySelector('[data-video-parallax]');
    const circle = box.querySelector('[data-video-circle]');
    const label = box.querySelector('[data-video-label]');
    const once = { trigger: box, start: 'top 62%', once: true };

    gsap.fromTo(box,
        { clipPath: 'inset(14% 10% 14% 10% round 28px)', scale: 0.9 },
        {
            clipPath: 'inset(0% 0% 0% 0% round 8px)',
            scale: 1,
            ease: 'power2.out',
            scrollTrigger: { trigger: box, start: 'top 95%', end: 'top 40%', scrub: 0.6 },
        });

    if (parallax) {
        gsap.fromTo(parallax, { yPercent: -5 }, {
            yPercent: 5,
            ease: 'none',
            scrollTrigger: { trigger: box, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    }

    gsap.from(circle, { scale: 0, rotation: -90, duration: 0.8, ease: 'back.out(1.7)', scrollTrigger: once });
    gsap.from(label, { autoAlpha: 0, y: 14, duration: 0.6, delay: 0.25, ease: 'power3.out', scrollTrigger: once });
};

/**
 * Services, as in the reference: the list sticks to the screen and the scroll alone opens
 * the services one by one. The open one closes while the next opens (height and opacity,
 * scrubbed). Wide screens only. Elsewhere every service is open and static.
 */
const services = () => {
    const track = document.querySelector('[data-services-track]');

    if (!track) {
        return;
    }

    const panels = all('[data-acc-panel]', track);
    const count = panels.length;
    const natural = (panel) => panel.querySelector('[data-acc-inner]').offsetHeight;

    track.setAttribute('data-pinned', '');
    track.style.height = `${100 + (count - 1) * 55 + 25}vh`;

    gsap.set(panels.slice(1), { height: 0, autoAlpha: 0 });
    gsap.set(panels, { overflow: 'hidden' });

    const timeline = gsap.timeline({
        defaults: { ease: 'power1.inOut' },
        scrollTrigger: { trigger: track, start: 'top top', end: 'bottom bottom', scrub: 0.5, invalidateOnRefresh: true },
    });

    // 0.25 of rest, then each hand-over takes 1 and rests 0.25, as measured on the reference.
    panels.slice(0, -1).forEach((panel, index) => {
        const at = 0.25 + index * 1.25;

        timeline
            .to(panel, { height: 0, autoAlpha: 0, duration: 1 }, at)
            .fromTo(panels[index + 1], { height: 0, autoAlpha: 0 }, { height: () => natural(panels[index + 1]), autoAlpha: 1, duration: 1 }, at);
    });

    timeline.to({}, { duration: 0.25 });

    return () => {
        track.removeAttribute('data-pinned');
        track.style.height = '';
        gsap.set(panels, { clearProps: 'height,opacity,visibility,overflow' });
    };
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
        video();
        gsap.matchMedia().add('(min-width: 1024px)', catalog);
        gsap.matchMedia().add('(min-width: 1024px)', services);

        window.__animReady = true;
        root.classList.remove('anim-hide');
        ScrollTrigger.refresh();
    });

    return () => {
        lenis?.destroy();
        root.classList.remove('anim-hide');
    };
});
