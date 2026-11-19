/**
 * The number picker: a button with several numbers opens its dialog instead of the first number's link.
 * The close button, a click on the dimmed area and Escape (native) close it. Without JavaScript the button
 * is a plain link to the first number.
 */
document.querySelectorAll('[data-number-picker]').forEach((button) => {
    const dialog = document.getElementById(button.dataset.numberPicker);

    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    button.addEventListener('click', (event) => {
        event.preventDefault();
        dialog.showModal();
    });

    dialog.querySelector('[data-number-close]')?.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
    dialog.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => dialog.close()));
});
