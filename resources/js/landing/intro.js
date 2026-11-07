import gsap from 'gsap';

const intro = document.getElementById('intro');

if (intro && getComputedStyle(intro).display !== 'none') {
    const root = document.documentElement;
    const button = intro.querySelector('[data-intro-close]');
    const items = intro.querySelectorAll('[data-intro-item]');
    const rest = document.querySelectorAll('header, main > :not(#intro), footer');
    const mm = gsap.matchMedia();

    window.__introReady = true;

    let closing = false;
    let released = false;
    let animateClose = () => finish();

    rest.forEach((element) => element.setAttribute('inert', ''));

    const focusButton = () => button?.focus({ preventScroll: true });

    /** Tells the page the curtain is nearly gone, so the hero can start. */
    function release() {
        if (released) {
            return;
        }

        released = true;
        document.dispatchEvent(new Event('intro:closed'));
    }

    function finish() {
        root.classList.remove('intro-pending');
        gsap.set(intro, { clearProps: 'all' });
        rest.forEach((element) => element.removeAttribute('inert'));

        try {
            sessionStorage.setItem('intro-seen', '1');
        } catch (error) {}

        const main = document.getElementById('main');

        main?.setAttribute('tabindex', '-1');
        main?.focus({ preventScroll: true });
        release();
    }

    function close() {
        if (closing) {
            return;
        }

        closing = true;
        animateClose();
    }

    mm.add('(prefers-reduced-motion: no-preference)', () => {
        gsap.set(intro, { borderBottomLeftRadius: '0% 0vh', borderBottomRightRadius: '0% 0vh' });
        gsap.from(items, { autoAlpha: 0, y: 28, duration: 0.8, stagger: 0.18, delay: 0.15, ease: 'power3.out', onComplete: focusButton });

        animateClose = () => {
            gsap.timeline({ onComplete: finish })
                .to(items, { autoAlpha: 0, y: -24, duration: 0.35, stagger: 0.06, ease: 'power2.in' })
                .to(intro, {
                    yPercent: -100,
                    borderBottomLeftRadius: '50% 18vh',
                    borderBottomRightRadius: '50% 18vh',
                    duration: 0.95,
                    ease: 'power4.inOut',
                }, '-=0.05')
                .call(release, null, '-=0.45');
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
