import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { all } from './shared.js';

/**
 * The call-to-action card. It rises and settles as it enters, a soft light drifts behind it, the
 * three graphics on its right take turns opening and closing by themselves (the same movement as
 * their hover in the About section) and, with a pointer, lean slightly toward it.
 */
export const cta = () => {
    const card = document.querySelector('[data-cta-card]');

    if (!card) {
        return;
    }

    const glow = card.querySelector('[data-cta-glow]');
    const stack = card.querySelector('[data-cta-art]');
    const arts = all('[data-about-art]', stack ?? card);

    gsap.from(card, {
        y: 70,
        scale: 0.95,
        autoAlpha: 0,
        duration: 1.1,
        ease: 'expo.out',
        scrollTrigger: { trigger: card, start: 'top 88%', once: true },
    });

    if (glow) {
        gsap.to(glow, { x: -60, y: 40, duration: 7, ease: 'sine.inOut', repeat: -1, yoyo: true });
    }

    if (arts.length > 0) {
        let step = 0;

        const turn = () => {
            const art = arts[step % arts.length];

            art.dispatchEvent(new Event(Math.floor(step / arts.length) % 2 === 0 ? 'art:open' : 'art:close'));
            step += 1;
        };

        const loop = gsap.delayedCall(1.6, function tick() {
            turn();
            loop.restart(true);
        });

        loop.pause();

        ScrollTrigger.create({
            trigger: card,
            start: 'top bottom',
            end: 'bottom top',
            onToggle: (self) => (self.isActive ? loop.restart(true) : loop.pause()),
        });
    }

    if (stack && window.matchMedia('(hover: hover)').matches) {
        const moveX = gsap.quickTo(stack, 'x', { duration: 0.8, ease: 'power3.out' });
        const moveY = gsap.quickTo(stack, 'y', { duration: 0.8, ease: 'power3.out' });

        card.addEventListener('mousemove', (event) => {
            const box = card.getBoundingClientRect();

            moveX(((event.clientX - box.left) / box.width - 0.5) * -22);
            moveY(((event.clientY - box.top) / box.height - 0.5) * -16);
        });

        card.addEventListener('mouseleave', () => {
            moveX(0);
            moveY(0);
        });
    }
};
