import gsap from 'gsap';

/**
 * The opening screen, shown once per browser session on the home page: flat crimson with the
 * monogram, the headline of the home page in very large type, and one wide bar to enter by.
 * Entering (no motion preference): the monogram opens from the left (0.9 seconds), the lines of
 * the headline rise out of their masks one after another (1 second, `expo.out`), the bar opens
 * from the left (1 second) and the languages fade in. Leaving: the content fades (0.3 seconds)
 * and the whole screen lifts away upward (0.9 seconds, `expo.inOut`); the home page starts its own
 * entrance halfway up (`intro:closing`), and `intro:closed` follows when the screen is gone.
 */
const intro = document.getElementById('intro');

if (intro && getComputedStyle(intro).display !== 'none') {
    const root = document.documentElement;
    const button = intro.querySelector('[data-intro-close]');
    const items = intro.querySelectorAll('[data-intro-item]');
    const rest = document.querySelectorAll('header, main > :not(#intro), footer, [data-widgets]');
    const mm = gsap.matchMedia();

    window.__introReady = true;

    let closing = false;
    let closingSent = false;
    let animateClose = () => finish();

    rest.forEach((element) => element.setAttribute('inert', ''));

    // The focus goes into the dialog itself, so the bar does not react on its own. Enter and Escape work from anywhere, and Tab reaches the bar.
    const focusDialog = () => intro.focus({ preventScroll: true });

    function announceClosing() {
        if (!closingSent) {
            closingSent = true;
            document.dispatchEvent(new Event('intro:closing'));
        }
    }

    function finish() {
        announceClosing();
        root.classList.remove('intro-pending');
        gsap.set(intro, { clearProps: 'all' });
        rest.forEach((element) => element.removeAttribute('inert'));

        try {
            sessionStorage.setItem('intro-seen', '1');
        } catch (error) {}

        const main = document.getElementById('main');

        main?.setAttribute('tabindex', '-1');
        main?.focus({ preventScroll: true });
        document.dispatchEvent(new Event('intro:closed'));
    }

    function close() {
        if (closing) {
            return;
        }

        closing = true;
        animateClose();
    }

    mm.add('(prefers-reduced-motion: no-preference)', () => {
        const logo = intro.querySelector('[data-intro-logo]');
        const words = intro.querySelectorAll('[data-intro-word]');

        // The wrappers are shown at once; the parts inside move.
        gsap.set(items, { autoAlpha: 1 });
        gsap.set(words, { yPercent: 105 });
        gsap.set(items.length ? [...items].filter((item) => item !== logo && item !== button) : [], { autoAlpha: 0 });

        gsap.timeline({ defaults: { ease: 'expo.out' }, onComplete: focusDialog })
            .fromTo(logo, { clipPath: 'inset(0 100% 0 0)' }, { clipPath: 'inset(0 0% 0 0)', duration: 0.9 }, 0.1)
            .to(words, { yPercent: 0, duration: 1, stagger: 0.12 }, 0.3)
            .fromTo(button, { clipPath: 'inset(0 100% 0 0 round 8px)', autoAlpha: 1 }, { clipPath: 'inset(0 0% 0 0 round 8px)', duration: 1, clearProps: 'clipPath' }, 0.7)
            .to([...items].filter((item) => item !== logo && item !== button), { autoAlpha: 1, duration: 0.8 }, 0.9);

        animateClose = () => {
            gsap.timeline({ onComplete: finish })
                .to(items, { autoAlpha: 0, duration: 0.3, ease: 'power2.in' }, 0)
                .to(words, { yPercent: -40, autoAlpha: 0, duration: 0.5, ease: 'power2.in', stagger: 0.04 }, 0)
                .to(intro, { yPercent: -100, duration: 0.9, ease: 'expo.inOut' }, 0.15)
                .call(announceClosing, null, 0.6);
        };
    });

    mm.add('(prefers-reduced-motion: reduce)', () => {
        gsap.set(items, { autoAlpha: 1 });
        focusDialog();
        animateClose = finish;
    });

    button?.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if ((event.key === 'Escape' || event.key === 'Enter') && root.classList.contains('intro-pending')) {
            close();
        }
    });
}
