import './landing/buttons.js';
import './landing/intro.js';
import './landing/motion.js';

const header = document.querySelector('[data-header]');
const sentinel = document.querySelector('[data-header-sentinel]');

if (header && sentinel && 'IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => header.toggleAttribute('data-stuck', !entry.isIntersecting)).observe(sentinel);
}

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

document.querySelectorAll('[data-video]').forEach((box) => {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const load = () => {
        const frame = document.createElement('iframe');

        frame.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(box.dataset.video)}?autoplay=1&rel=0`;
        frame.title = box.dataset.videoTitle ?? '';
        frame.allow = 'autoplay; encrypted-media; picture-in-picture';
        frame.allowFullscreen = true;
        frame.className = 'absolute inset-0 h-full w-full';

        if (!reduced) {
            frame.style.opacity = '0';
            frame.style.transition = 'opacity 0.5s ease';
            requestAnimationFrame(() => requestAnimationFrame(() => (frame.style.opacity = '1')));
        }

        box.replaceChildren(frame);
    };

    box.querySelector('[data-video-play]')?.addEventListener('click', () => {
        if (box.classList.contains('is-loading')) {
            return;
        }

        box.classList.add('is-loading');

        // Let the play button swell and fade out first, then swap in the player.
        reduced ? load() : setTimeout(load, 350);
    });
});
