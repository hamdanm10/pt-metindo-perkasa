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

/*
 * Show and hide a password. The state lives on the button (aria-pressed) and is applied to the input, so it can be
 * applied again after Livewire re-renders the field and puts type="password" back.
 */
const applyPasswordToggle = (button) => {
    const input = document.getElementById(button.dataset.passwordToggle);
    const visible = button.getAttribute('aria-pressed') === 'true';

    if (!input) {
        return;
    }

    input.type = visible ? 'text' : 'password';
    button.setAttribute('aria-label', visible ? button.dataset.hide : button.dataset.show);
    button.querySelector('[data-eye]').hidden = visible;
    button.querySelector('[data-eye-off]').hidden = !visible;
};

document.addEventListener('click', (event) => {
    const button = event.target instanceof Element ? event.target.closest('[data-password-toggle]') : null;

    if (!button) {
        return;
    }

    button.setAttribute('aria-pressed', String(button.getAttribute('aria-pressed') !== 'true'));
    applyPasswordToggle(button);
});

document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morphed', () => {
        document.querySelectorAll('[data-password-toggle]').forEach(applyPasswordToggle);
    });
});

const loginForm = document.querySelector('[data-login-form]');

if (loginForm) {
    const password = loginForm.querySelector('#password');
    const caps = loginForm.querySelector('[data-caps]');
    const submit = loginForm.querySelector('button[type="submit"]');

    password.addEventListener('keydown', (event) => {
        caps.hidden = !event.getModifierState('CapsLock');
    });
    password.addEventListener('blur', () => {
        caps.hidden = true;
    });

    // Stops a second click from sending the form twice while the first request is on its way.
    loginForm.addEventListener('submit', (event) => {
        if (submit.hasAttribute('data-busy')) {
            event.preventDefault();

            return;
        }

        submit.setAttribute('data-busy', '');
        submit.querySelector('[data-submit-text]').textContent = submit.dataset.busyLabel;
    });
}
