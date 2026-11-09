import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all, root } from './shared.js';

/**
 * Gallery, as measured on the reference: every 3 seconds the next photo slides in over 1.45
 * seconds (`expo.inOut`) while the one before moves the same distance, half the width, and the
 * new photo fades in during the first 0.4 seconds. The city, company, and local time below
 * swap about 0.3 seconds in. The local time is real, from each location's time zone. It runs
 * only while the gallery is on screen, and not at all with reduced motion.
 */
export const gallery = () => {
    const section = document.querySelector('[data-gallery]');

    if (!section) {
        return;
    }

    const slides = all('[data-gallery-slide]', section);
    const timeText = section.querySelector('[data-gallery-time]');
    const texts = [
        section.querySelector('[data-gallery-city] [data-gallery-text]'),
        section.querySelector('[data-gallery-name] [data-gallery-text]'),
        timeText,
    ];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const language = `${document.documentElement.lang || 'en'}-u-nu-latn`;
    const sign = root.dir === 'rtl' ? -1 : 1;

    let current = 0;
    let timer = null;
    let clock = null;

    const timeIn = (zone) => {
        try {
            return new Intl.DateTimeFormat(language, { hour: '2-digit', minute: '2-digit', hourCycle: 'h23', timeZone: zone }).format(new Date());
        } catch (error) {
            return timeText.textContent;
        }
    };

    const valuesOf = (slide) => [slide.dataset.city, slide.dataset.name, timeIn(slide.dataset.timezone)];

    const refreshTime = () => {
        timeText.textContent = timeIn(slides[current].dataset.timezone);
    };

    const swapLabels = (slide) => {
        const values = valuesOf(slide);

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

    clock = window.setInterval(refreshTime, 20000);

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
