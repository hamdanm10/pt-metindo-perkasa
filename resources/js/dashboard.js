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

const root = document.documentElement;
const sidebar = document.getElementById('sidebar');
const toggle = document.querySelector('[data-sidebar-toggle]');

if (sidebar && toggle) {
    const isOpen = () => sidebar.hasAttribute('data-open');
    const focusable = () => sidebar.querySelectorAll('a[href], button:not([disabled])');

    const setOpen = (open) => {
        sidebar.toggleAttribute('data-open', open);
        root.toggleAttribute('data-sidebar-open', open);
        toggle.setAttribute('aria-expanded', String(open));

        if (open) {
            focusable()[0]?.focus();
        } else {
            toggle.focus();
        }
    };

    toggle.addEventListener('click', () => setOpen(!isOpen()));

    document.querySelectorAll('[data-sidebar-close]').forEach((element) => {
        element.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (!isOpen()) {
            return;
        }

        if (event.key === 'Escape') {
            setOpen(false);
        }

        if (event.key === 'Tab') {
            const items = focusable();
            const first = items[0];
            const last = items[items.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches && isOpen()) {
            sidebar.removeAttribute('data-open');
            root.removeAttribute('data-sidebar-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

if (document.querySelector('[data-tiptap]')) {
    import('./dashboard/editor.js');
}

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.dataset.passwordToggle);

    button.addEventListener('click', () => {
        const visible = input.type === 'password';

        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        button.setAttribute('aria-label', visible ? button.dataset.hide : button.dataset.show);
        button.querySelector('[data-eye]').hidden = visible;
        button.querySelector('[data-eye-off]').hidden = !visible;
    });
});
