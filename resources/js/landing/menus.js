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
