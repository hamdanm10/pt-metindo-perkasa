/** A pin on the subsidiaries map and its card light up together when either is hovered or focused. */
document.querySelectorAll('[data-map-stage]').forEach((stage) => {
    const parts = (key) => stage.querySelectorAll(`[data-pin="${key}"], [data-sub="${key}"]`);
    const keyOf = (element) => element.dataset.pin ?? element.dataset.sub;

    const set = (key, on) => parts(key).forEach((part) => part.toggleAttribute('data-active', on));

    stage.querySelectorAll('[data-pin], [data-sub]').forEach((element) => {
        const key = keyOf(element);

        element.addEventListener('mouseenter', () => set(key, true));
        element.addEventListener('mouseleave', () => set(key, false));
        element.addEventListener('focusin', () => set(key, true));
        element.addEventListener('focusout', () => set(key, false));
    });
});
