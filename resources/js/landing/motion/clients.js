import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all } from './shared.js';

/**
 * Clients, as measured on the reference: the nine logo boxes shrink and drop away one after
 * another from the top left, the chip swaps its text and width, then the new logos pop in with
 * a little overshoot. It advances by itself every 5 seconds while the section is on screen,
 * and the arrows move it by hand. Without motion preference it still switches, but instantly
 * and never by itself.
 */
export const clients = () => {
    const section = document.querySelector('[data-clients]');
    const slides = all('[data-clients-slide]', section ?? document);

    if (!section || slides.length < 2) {
        return;
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const chip = section.querySelector('[data-chip]');
    const chipText = chip.querySelector('[data-chip-text]');
    const labels = slides.map((slide) => slide.dataset.label);
    const cellsOf = (index) => Array.from(slides[index].children);
    const wave = { each: 0.035, from: 'start', grid: 'auto' };

    let current = 0;
    let busy = false;
    let timer = null;

    const widthFor = (text) => {
        const probe = chip.cloneNode(true);

        probe.style.cssText = 'position:absolute;visibility:hidden;width:auto;pointer-events:none';
        probe.querySelector('[data-chip-text]').textContent = text;
        chip.parentElement.appendChild(probe);

        const width = probe.offsetWidth;

        probe.remove();

        return width;
    };

    const show = (index) => {
        slides.forEach((slide, position) => {
            slide.hidden = position !== index;
        });
        chipText.textContent = labels[index];
    };

    const go = (to) => {
        if (busy || to === current) {
            return;
        }

        const from = current;

        current = to;

        if (reduced) {
            show(to);

            return;
        }

        busy = true;

        gsap.set(chip, { width: chip.offsetWidth });

        gsap.timeline({ onComplete: () => (busy = false) })
            .to(cellsOf(from), { y: 54, scale: 0, autoAlpha: 0, duration: 0.35, ease: 'power2.in', stagger: wave }, 0)
            .to(chipText, { yPercent: -130, duration: 0.2, ease: 'power2.in' }, 0.18)
            .to(chip, { width: widthFor(labels[to]), duration: 0.5, ease: 'power2.inOut' }, 0.25)
            .call(() => {
                chipText.textContent = labels[to];
                gsap.set(chipText, { yPercent: 130 });
            }, null, 0.4)
            .to(chipText, { yPercent: 0, duration: 0.35, ease: 'power3.out' }, 0.42)
            .call(() => {
                gsap.set(cellsOf(from), { clearProps: 'all' });
                slides[from].hidden = true;
                slides[to].hidden = false;
                gsap.fromTo(cellsOf(to), { scale: 0, autoAlpha: 0 }, {
                    scale: 1,
                    autoAlpha: 1,
                    duration: 0.7,
                    ease: 'back.out(1.7)',
                    stagger: { ...wave, each: 0.05 },
                    clearProps: 'transform,opacity,visibility',
                });
            }, null, 0.68)
            .to({}, { duration: 0.95 }, 0.68);
    };

    const next = () => go((current + 1) % slides.length);
    const previous = () => go((current - 1 + slides.length) % slides.length);

    section.querySelectorAll('[data-clients-next]').forEach((button) => button.addEventListener('click', () => {
        next();
        timer?.restart(true);
    }));

    section.querySelectorAll('[data-clients-prev]').forEach((button) => button.addEventListener('click', () => {
        previous();
        timer?.restart(true);
    }));

    if (!reduced) {
        timer = gsap.delayedCall(5, function tick() {
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
    }
};
