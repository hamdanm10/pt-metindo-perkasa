import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { createDeck } from './deck.js';
import { all, root } from './shared.js';

const hoverArt = () => {
    const headline = document.querySelector('[data-about-headline]');
    const chart = headline?.querySelector('[data-art-chart]');
    const bands = headline?.querySelector('[data-art-bar]');
    const hexes = headline?.querySelector('[data-art-hex]');

    if (!headline || !window.matchMedia('(hover: hover)').matches) {
        return;
    }

    /** The area under the line has its two bottom corners at `edge`; at 92 it sits below the line, at 0 above it. */
    const flip = { edge: 92 };
    const draw = () => chart?.setAttribute('d', `M0 ${flip.edge}V84L85 62H250L340 30H420L481 8V${flip.edge}Z`);
    const hexList = hexes ? Array.from(hexes.children) : [];

    const to = (open) => {
        const duration = open ? 0.9 : 0.7;
        const ease = open ? 'power3.out' : 'power3.inOut';

        if (chart) {
            gsap.to(flip, { edge: open ? 0 : 92, duration, ease, onUpdate: draw, overwrite: true });
        }

        if (bands) {
            gsap.to(bands, { x: open ? 290 : 0, duration: open ? 1.2 : 0.9, ease, overwrite: true });
        }

        if (hexes) {
            gsap.to(hexes, { x: open ? -64 : 0, duration, ease, overwrite: true });
            gsap.to(hexList, { rotation: open ? -7 : 0, transformOrigin: '50% 50%', duration, ease, overwrite: 'auto' });
        }
    };

    headline.addEventListener('mouseenter', () => to(true));
    headline.addEventListener('mouseleave', () => to(false));
};

/**
 * About, as on the reference: the three headline lines grow letter by letter (the shared
 * `data-anim="grow"`), the graphic beside each line wipes open from its own side, and the photo
 * deck changes its front card every 4 seconds while it is on screen. When the pointer is over
 * the headline (pointer devices only) the three graphics move as on the reference: the chart
 * flips so the area is on the other side of the line, the bands slide right and wrap around, and
 * the hexagons shift and turn (an approximation from three screenshots of the reference). They go back when the pointer leaves. The graphics are drawn in
 * SVG here; the reference draws them on a canvas.
 */
export const about = () => {
    const panel = document.querySelector('[data-about-deck-panel]');
    const sign = root.dir === 'rtl' ? -1 : 1;

    all('[data-about-art]').forEach((art, index) => {
        const fromRight = index % 2 === 1 ? sign : -sign;
        const hidden = fromRight > 0 ? 'inset(0 0 0 100% round 8px)' : 'inset(0 100% 0 0 round 8px)';

        gsap.fromTo(art, { clipPath: hidden }, {
            clipPath: 'inset(0 0% 0 0% round 8px)',
            duration: 1.1,
            ease: 'expo.out',
            clearProps: 'clipPath',
            scrollTrigger: { trigger: art, start: 'top 90%', once: true },
        });
    });

    hoverArt();

    if (!panel) {
        return;
    }

    const { loop } = createDeck({
        deck: panel.querySelector('[data-deck]'),
        cards: all('[data-deck-card]', panel),
        medias: all('[data-deck-media]', panel),
    });

    loop.pause();

    ScrollTrigger.create({
        trigger: panel,
        start: 'top bottom',
        end: 'bottom top',
        onToggle: (self) => (self.isActive ? loop.restart(true) : loop.pause()),
    });
};
