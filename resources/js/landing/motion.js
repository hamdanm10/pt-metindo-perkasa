import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { about } from './motion/about.js';
import { catalog } from './motion/catalog.js';
import { clients } from './motion/clients.js';
import { gallery } from './motion/gallery.js';
import { smartHeader } from './motion/header.js';
import { hero } from './motion/hero.js';
import { scrollReveals } from './motion/reveals.js';
import { services } from './motion/services.js';
import { root } from './motion/shared.js';
import { smoothScroll } from './motion/smooth-scroll.js';
import { video } from './motion/video.js';

const mm = gsap.matchMedia();

mm.add('(prefers-reduced-motion: no-preference)', () => {
    const lenis = smoothScroll();

    document.fonts.ready.then(() => {
        hero();
        scrollReveals();
        smartHeader();
        video();
        about();
        gsap.matchMedia().add('(min-width: 1024px)', catalog);
        gsap.matchMedia().add('(min-width: 1024px)', services);

        window.__animReady = true;
        root.classList.remove('anim-hide');
        ScrollTrigger.refresh();
    });

    return () => {
        lenis.destroy();
        root.classList.remove('anim-hide');
    };
});

clients();
gallery();
