const header = document.querySelector('[data-header]');
const sentinel = document.querySelector('[data-header-sentinel]');

if (header && sentinel && 'IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => header.toggleAttribute('data-stuck', !entry.isIntersecting)).observe(sentinel);
}
