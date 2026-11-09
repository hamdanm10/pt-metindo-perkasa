import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { createDeck } from './deck.js';
import { all, root } from './shared.js';

/**
 * About, as on the reference: the three headline lines grow letter by letter (the shared
 * `data-anim="grow"`), the graphic beside each line wipes open from its own side, and the photo
 * deck changes its front card every 4 seconds while it is on screen. The graphics are still
 * pictures; the reference animates them, and that waits for the animated files.
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
