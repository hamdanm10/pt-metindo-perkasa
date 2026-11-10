import gsap from 'gsap';

const intro = document.getElementById('intro');

if (intro && getComputedStyle(intro).display !== 'none') {
    const root = document.documentElement;
    const button = intro.querySelector('[data-intro-close]');
    const items = intro.querySelectorAll('[data-intro-item]');
    const rest = document.querySelectorAll('header, main > :not(#intro), footer, [data-widgets]');
    const mm = gsap.matchMedia();

    window.__introReady = true;

    let closing = false;
    let animateClose = () => finish();

    rest.forEach((element) => element.setAttribute('inert', ''));

    const focusButton = () => button?.focus({ preventScroll: true });

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
        gsap.from(items, { autoAlpha: 0, y: 20, duration: 0.6, stagger: 0.15, delay: 0.1, ease: 'power3.out', onComplete: focusButton });

        animateClose = () => {
            gsap.to(intro, { autoAlpha: 0, duration: 0.4, ease: 'power2.inOut', onComplete: finish });
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
