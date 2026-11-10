/** The footer badge returns to the top. The smooth scroll takes the request when it is running, otherwise the page jumps. */
document.querySelectorAll('[data-to-top]').forEach((button) => {
    button.addEventListener('click', () => {
        if (document.dispatchEvent(new CustomEvent('landing:to-top', { cancelable: true }))) {
            window.scrollTo({ top: 0 });
        }
    });
});
