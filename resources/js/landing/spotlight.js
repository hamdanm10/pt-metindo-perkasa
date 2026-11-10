/** A soft light follows the pointer over the process cards (pointer devices only). */
if (window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll('[data-spot]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const box = card.getBoundingClientRect();

            card.style.setProperty('--mx', `${event.clientX - box.left}px`);
            card.style.setProperty('--my', `${event.clientY - box.top}px`);
        });
    });
}
