import gsap from 'gsap';
import { createDeck } from './deck.js';
import { all } from './shared.js';

/**
 * Catalog deck, measured from the reference hero: four cards of the same size, each shifted
 * sideways and stacked by z-index. Every 4 seconds the front card changes (0 to 3 and back).
 * The whole stack shrinks to 85% and returns while the new front card tilts and settles.
 * When the page scrolls on, the cards leave the deck and travel down to the row (scrubbed).
 * Wide screens only. Without it the deck is static and the row is plain.
 */
export const catalog = () => {
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

    const { loop, setPlaying } = createDeck({ deck, cards, medias });

    gsap.set(tiles, { autoAlpha: 0 });
    gsap.set(captions, { autoAlpha: 0 });
    gsap.set(action, { autoAlpha: 0 });

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
                setPlaying(self.progress < 0.01);
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
