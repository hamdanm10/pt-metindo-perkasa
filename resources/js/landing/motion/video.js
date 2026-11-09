import gsap from 'gsap';
import './shared.js';

/**
 * Video section: the box opens up from a smaller rounded frame as it scrolls into view, the
 * thumbnail drifts slowly inside it, and the play button pops in once the box is on screen.
 */
export const video = () => {
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
