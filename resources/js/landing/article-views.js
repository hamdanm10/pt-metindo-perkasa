/** Counts a view once the article has been open and visible for a few seconds, so bots and quick bounces do not count. */
const marker = document.querySelector('[data-article-view]');

if (marker && marker.dataset.url) {
    const send = () => {
        if (document.visibilityState !== 'visible') {
            document.addEventListener('visibilitychange', onVisible, { once: true });
            return;
        }

        fetch(marker.dataset.url, {
            method: 'POST',
            keepalive: true,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).catch(() => {});
    };

    const onVisible = () => window.setTimeout(send, 3000);

    window.setTimeout(send, 3000);
}
