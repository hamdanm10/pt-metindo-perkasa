/** The copy-link buttons of an article: copy the address and say so for a moment. */
document.querySelectorAll('[data-copy-link]').forEach((button) => {
    const label = button.querySelector('[data-copy-label]');
    const original = label?.textContent ?? '';
    let timer = null;

    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copyLink ?? window.location.href);
        } catch (error) {
            return;
        }

        if (label) {
            label.textContent = button.dataset.copied ?? original;
            clearTimeout(timer);
            timer = setTimeout(() => (label.textContent = original), 2000);
        }
    });
});
