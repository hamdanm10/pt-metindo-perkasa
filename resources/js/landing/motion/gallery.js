import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all, root } from './shared.js';

/**
 * Gallery, as measured on the reference: every 3 seconds the next photo slides in over 1.45
 * seconds (`expo.inOut`) while the one before moves the same distance, half the width, and the
 * new photo fades in during the first 0.4 seconds. The label, the caption, and the counter below
 * swap about 0.3 seconds in. It runs only while the gallery is on screen, and not at all with
 * reduced motion.
 */
export const gallery = () => {
    const section = document.querySelector('[data-gallery]');

    if (!section) {
        return;
    }

    const slides = all('[data-gallery-slide]', section);
    const count = section.querySelector('[data-gallery-count]');
    const texts = [
        section.querySelector('[data-gallery-label] [data-gallery-text]'),
        section.querySelector('[data-gallery-caption] [data-gallery-text]'),
        count,
    ];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const sign = root.dir === 'rtl' ? -1 : 1;

    let current = 0;
    let timer = null;

    const pad = (number) => String(number).padStart(2, '0');

    const valuesOf = (slide, index) => [slide.dataset.label, slide.dataset.caption, `${pad(index + 1)} / ${pad(slides.length)}`];

    const swapLabels = (slide) => {
        const values = valuesOf(slide, slides.indexOf(slide));

        texts.forEach((text, index) => {
            gsap.timeline()
                .to(text, { yPercent: -120, duration: 0.2, ease: 'power2.in' })
                .call(() => {
                    text.textContent = values[index];
                    gsap.set(text, { yPercent: 120 });
                })
                .to(text, { yPercent: 0, duration: 0.35, ease: 'power3.out' });
        });
    };

    const next = () => {
        const from = slides[current];

        current = (current + 1) % slides.length;

        const to = slides[current];

        gsap.set(to, { visibility: 'visible', zIndex: 2, xPercent: -50 * sign, autoAlpha: 0 });
        gsap.set(from, { zIndex: 1 });

        gsap.timeline({
            defaults: { ease: 'expo.inOut' },
            onComplete: () => {
                gsap.set(from, { visibility: 'hidden', xPercent: 0, clearProps: 'zIndex' });
                gsap.set(to, { clearProps: 'zIndex' });
            },
        })
            .to(from, { xPercent: 50 * sign, duration: 1.45 }, 0)
            .to(to, { xPercent: 0, duration: 1.45 }, 0)
            .to(to, { autoAlpha: 1, duration: 0.35, ease: 'power1.out' }, 0.07)
            .call(() => swapLabels(to), null, 0.3);
    };

    if (reduced || slides.length < 2) {
        return;
    }

    timer = gsap.delayedCall(3, function tick() {
        next();
        timer.restart(true);
    });
    timer.pause();

    ScrollTrigger.create({
        trigger: section,
        start: 'top bottom',
        end: 'bottom top',
        onToggle: (self) => (self.isActive ? timer.restart(true) : timer.pause()),
    });
};
