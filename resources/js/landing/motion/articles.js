import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all } from './shared.js';

/**
 * The article pages. The list: the large first article opens its picture upward (1 second,
 * `expo.out`) and the cards rise in rows as they come into view. The reading page: the title
 * block rises in order, the picture opens upward and settles from 108 percent, the bar under the
 * top of the screen fills as the text is read, and the related cards rise in rows. The text
 * itself does not move, so reading stays calm.
 */
export const articles = () => {
    const featured = document.querySelector('[data-art-featured-media]');

    if (featured) {
        gsap.fromTo(featured, { clipPath: 'inset(100% 0 0 0 round 8px)' }, {
            clipPath: 'inset(0% 0 0 0 round 8px)',
            duration: 1,
            delay: 0.25,
            ease: 'expo.out',
            clearProps: 'clipPath',
        });
    }

    const head = all('[data-read-head]');

    if (head.length > 0) {
        gsap.from(head, { y: 28, autoAlpha: 0, duration: 0.9, ease: 'expo.out', stagger: 0.08, delay: 0.1 });
    }

    const image = document.querySelector('[data-read-image]');

    if (image) {
        const frame = image.firstElementChild;
        const picture = frame?.querySelector('img');

        gsap.fromTo(frame, { clipPath: 'inset(100% 0 0 0 round 8px)' }, { clipPath: 'inset(0% 0 0 0 round 8px)', duration: 1.1, delay: 0.5, ease: 'expo.out', clearProps: 'clipPath' });
        gsap.fromTo(picture, { scale: 1.08 }, { scale: 1, duration: 1.6, delay: 0.5, ease: 'expo.out' });
    }

    const body = document.querySelector('[data-read-body]');
    const bar = document.querySelector('[data-read-progress]');

    if (body && bar) {
        gsap.to(bar, {
            scaleX: 1,
            ease: 'none',
            scrollTrigger: { trigger: body, start: 'top 70%', end: 'bottom 60%', scrub: 0.3 },
        });
    }

    all('[data-art-grid]').forEach((grid) => {
        const cards = Array.from(grid.children);

        gsap.set(cards, { autoAlpha: 0, y: 48 });

        ScrollTrigger.batch(cards, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => gsap.to(batch, { autoAlpha: 1, y: 0, duration: 0.9, stagger: 0.08, ease: 'expo.out', overwrite: true }),
        });
    });
};
