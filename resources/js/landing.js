const menus = () => document.querySelectorAll('details[data-dismissible][open]');

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    menus().forEach((menu) => {
        menu.removeAttribute('open');
        menu.querySelector('summary')?.focus();
    });
});

document.addEventListener('click', (event) => {
    menus().forEach((menu) => {
        if (!menu.contains(event.target)) {
            menu.removeAttribute('open');
        }
    });
});

const intro = document.getElementById('intro');
const rest = document.querySelectorAll('header, main > :not(#intro), footer');

if (intro && getComputedStyle(intro).display !== 'none') {
    const root = document.documentElement;
    const button = intro.querySelector('[data-intro-close]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    rest.forEach((element) => element.setAttribute('inert', ''));
    button?.focus();

    const finish = () => {
        root.classList.remove('intro-pending');
        intro.classList.remove('is-leaving');
        rest.forEach((element) => element.removeAttribute('inert'));

        try {
            sessionStorage.setItem('intro-seen', '1');
        } catch (error) {}

        document.getElementById('main')?.setAttribute('tabindex', '-1');
        document.getElementById('main')?.focus({ preventScroll: true });
    };

    const close = () => {
        if (reduced) {
            finish();

            return;
        }

        intro.classList.add('is-leaving');
        intro.addEventListener('transitionend', finish, { once: true });
    };

    button?.addEventListener('click', close);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && root.classList.contains('intro-pending')) {
            close();
        }
    });
}

document.querySelectorAll('[data-video]').forEach((box) => {
    box.querySelector('[data-video-play]')?.addEventListener('click', () => {
        const frame = document.createElement('iframe');

        frame.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(box.dataset.video)}?autoplay=1&rel=0`;
        frame.title = box.dataset.videoTitle ?? '';
        frame.allow = 'autoplay; encrypted-media; picture-in-picture';
        frame.allowFullscreen = true;
        frame.className = 'absolute inset-0 h-full w-full';

        box.replaceChildren(frame);
    });
});
