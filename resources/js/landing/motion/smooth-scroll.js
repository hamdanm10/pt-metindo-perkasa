import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { root } from './shared.js';

/** Eased wheel scrolling, as on the reference site. Stopped while the intro is open. */
export const smoothScroll = () => {
    const lenis = new Lenis({ lerp: 0.1, anchors: false });

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

    // The back-to-top badge in the footer asks for this scroll, so it eases like the rest of the page.
    document.addEventListener('landing:to-top', (event) => {
        event.preventDefault();
        lenis.scrollTo(0, { duration: 1.6 });
    });

    return lenis;
};
