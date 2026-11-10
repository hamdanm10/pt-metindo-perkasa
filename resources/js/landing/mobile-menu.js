import gsap from 'gsap';

/**
 * The full-screen menu of the reference, below the large-screen breakpoint. Opening: the backdrop
 * fades in over 0.6 seconds while a dark sheet swings down from the top right, turning from about
 * -60 degrees to flat with a small overshoot (0.75 seconds, `back.out`), its content moving the
 * opposite way at two thirds of the distance; the items rise in one after another from 0.33
 * seconds. Closing: the sheet slides up and away over 1.1 seconds (`expo.inOut`) while the
 * backdrop fades. With reduced motion it only shows and hides.
 */
const toggle = document.querySelector('[data-menu-toggle]');
const menu = document.querySelector('[data-menu]');

if (toggle && menu) {
    const root = document.documentElement;
    const backdrop = menu.querySelector('[data-menu-backdrop]');
    const mask = menu.querySelector('[data-menu-mask]');
    const panel = menu.querySelector('[data-menu-panel]');
    const items = Array.from(menu.querySelectorAll('[data-menu-item]'));
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const wide = window.matchMedia('(min-width: 1024px)');

    let open = false;
    let timeline = null;

    const lock = (on) => {
        root.classList.toggle('menu-open', on);
        document.dispatchEvent(new Event(on ? 'landing:lock' : 'landing:unlock'));
    };

    const show = () => {
        open = true;
        toggle.setAttribute('aria-expanded', 'true');
        menu.hidden = false;
        lock(true);
        timeline?.kill();

        if (calm) {
            return;
        }

        const width = window.innerWidth;
        const height = window.innerHeight;
        const ease = 'back.out(1.05)';

        gsap.set(mask, { transformOrigin: '0 0' });

        timeline = gsap.timeline()
            .fromTo(backdrop, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.6, ease: 'power1.out' }, 0)
            .fromTo(mask, { rotation: -60, x: width * 1.5, y: -height * 1.1 }, { rotation: 0, x: 0, y: 0, duration: 0.75, ease }, 0)
            .fromTo(panel, { y: height * 0.733 }, { y: 0, duration: 0.75, ease }, 0)
            .fromTo(items, { y: 52, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7, ease: 'expo.out', stagger: 0.07, clearProps: 'transform,opacity,visibility' }, 0.33);
    };

    const finish = () => {
        menu.hidden = true;
        gsap.set([backdrop, mask, panel, ...items], { clearProps: 'all' });
        lock(false);
    };

    const hide = (instant = false) => {
        if (!open) {
            return;
        }

        open = false;
        toggle.setAttribute('aria-expanded', 'false');
        timeline?.kill();

        if (calm || instant) {
            finish();

            return;
        }

        const height = window.innerHeight;

        timeline = gsap.timeline({ onComplete: finish })
            .to(backdrop, { autoAlpha: 0, duration: 1.1, ease: 'power1.inOut' }, 0)
            .to(mask, { y: -height, duration: 1.1, ease: 'expo.inOut' }, 0)
            .to(panel, { y: height, duration: 1.1, ease: 'expo.inOut' }, 0);
    };

    toggle.addEventListener('click', () => (open ? hide() : show()));

    menu.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('a')) {
            hide(true);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && open) {
            hide();
            toggle.focus();
        }
    });

    wide.addEventListener('change', () => {
        if (wide.matches) {
            hide(true);
        }
    });
}
