import gsap from 'gsap';
import { all } from './shared.js';

/**
 * Services, as in the reference: the list sticks to the screen and the scroll alone opens
 * the services one by one. The open one closes while the next opens (height and opacity,
 * scrubbed). Wide screens only. Elsewhere every service is open and static.
 */
export const services = () => {
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
