import gsap from 'gsap';

/**
 * The deck of the reference: cards of the same size stacked by z-index. Every 4 seconds the front
 * card changes (first to last and back). The whole stack shrinks to 85% and returns while the new
 * front card tilts and settles. Returns the timer so a section can pause it, and `setPlaying` to
 * stop the change without stopping the timer.
 */
export const createDeck = ({ deck, cards, medias }) => {
    let active = 0;
    let direction = 1;
    let top = cards.length;
    let playing = true;

    gsap.set(deck, { transformOrigin: '50% 100%' });
    gsap.set(cards, { transformOrigin: '0 0' });

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

    return { loop, setPlaying: (value) => { playing = value; } };
};
