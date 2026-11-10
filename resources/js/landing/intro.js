import gsap from 'gsap';

/**
 * The opening screen, shown once per browser session on the home page. Entering (no motion
 * preference): the monogram opens from the left (1 second), its divider draws down, the two words
 * of the name rise out of their masks one after another, the tagline closes up from a wide letter
 * spacing, then the button, the hint and the languages fade in while a ring pulses around the
 * button and soft light drifts behind. Leaving: the content fades and swells, and the screen
 * lifts away upward (0.9 seconds, `expo.inOut`); the home page starts its own entrance halfway up
 * (`intro:closing`), and `intro:closed` follows when the screen is gone.
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

    // The focus goes into the dialog itself, not onto the button, so the button does not light up on its own. Enter and Escape work from anywhere, and Tab reaches the button.
    const focusButton = () => intro.focus({ preventScroll: true });

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
        const line = intro.querySelector('[data-intro-line]');
        const words = intro.querySelectorAll('[data-intro-word]');
        const tagline = intro.querySelector('[data-intro-tagline]');
        const action = intro.querySelector('[data-intro-action]');
        const languages = intro.querySelector('[data-intro-languages]');
        const ring = intro.querySelector('[data-intro-ring]');
        const lights = intro.querySelectorAll('[data-intro-light]');
        const mark = intro.querySelector('[data-intro-mark]');

        // The wrappers are shown at once; the parts inside move.
        gsap.set(items, { autoAlpha: 1 });
        gsap.set([logo, line, tagline, action, languages], { autoAlpha: 0 });
        gsap.set(words, { yPercent: 115 });

        gsap.timeline({ defaults: { ease: 'expo.out' }, onComplete: focusButton })
            .fromTo(logo, { autoAlpha: 1, clipPath: 'inset(0 100% 0 0)', scale: 0.92 }, { clipPath: 'inset(0 0% 0 0)', scale: 1, duration: 1.1 }, 0.15)
            .fromTo(line, { autoAlpha: 1, scaleY: 0 }, { scaleY: 1, duration: 0.8 }, 0.55)
            .to(words, { yPercent: 0, duration: 0.9, stagger: 0.12 }, 0.65)
            .fromTo(tagline, { autoAlpha: 0, letterSpacing: '0.7em' }, { autoAlpha: 1, letterSpacing: '0.3em', duration: 1.3 }, 0.95)
            .fromTo(action, { autoAlpha: 0, y: 24 }, { autoAlpha: 1, y: 0, duration: 0.8 }, 1.2)
            .to(languages, { autoAlpha: 1, duration: 0.8 }, 1.5);

        gsap.fromTo(ring, { scale: 1, autoAlpha: 0.7 }, { scale: 1.35, autoAlpha: 0, duration: 1.6, ease: 'power2.out', repeat: -1, repeatDelay: 0.5, delay: 2 });
        gsap.to(lights[0], { x: '8vw', y: '6vw', duration: 7, ease: 'sine.inOut', repeat: -1, yoyo: true });
        gsap.to(lights[1], { x: '-7vw', y: '-6vw', duration: 8, ease: 'sine.inOut', repeat: -1, yoyo: true });
        gsap.to(mark, { rotation: 4, duration: 14, ease: 'sine.inOut', repeat: -1, yoyo: true });

        animateClose = () => {
            gsap.timeline({ onComplete: finish })
                .to(items, { autoAlpha: 0, duration: 0.35, ease: 'power2.in' }, 0)
                .to(logo, { scale: 1.12, duration: 0.5, ease: 'power2.in' }, 0)
                .to(intro, { yPercent: -100, duration: 0.9, ease: 'expo.inOut' }, 0.15)
                .call(announceClosing, null, 0.6);
        };
    });

    mm.add('(prefers-reduced-motion: reduce)', () => {
        gsap.set(items, { autoAlpha: 1 });
        focusButton();
        animateClose = finish;
    });

    button?.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if ((event.key === 'Escape' || event.key === 'Enter') && root.classList.contains('intro-pending')) {
            close();
        }
    });
}
