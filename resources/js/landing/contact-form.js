/**
 * The contact form writes nothing to the site. The two buttons are plain links to WhatsApp and to
 * the email address; on a click the form is checked, and the message is put into the link: a
 * greeting, the message, then the name and the email. The app that opens (WhatsApp or the email
 * program) shows it ready to send. Without JavaScript the buttons still open the chat or a blank
 * email.
 */
document.querySelectorAll('[data-contact-form]').forEach((form) => {
    const fields = {
        name: form.querySelector('[name="name"]'),
        email: form.querySelector('[name="email"]'),
        message: form.querySelector('[name="message"]'),
    };
    const status = form.querySelector('[data-form-status]');
    const done = form.querySelector('[data-done]')?.textContent ?? '';

    const show = (field, text) => {
        const error = form.querySelector(`#${field.id}-error`);

        field.setAttribute('aria-invalid', text === '' ? 'false' : 'true');

        if (error) {
            error.textContent = text;
        }
    };

    const check = () => {
        let first = null;

        Object.values(fields).forEach((field) => {
            const value = field.value.trim();
            let problem = '';

            if (value === '') {
                problem = form.dataset.required;
            } else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                problem = form.dataset.invalid;
            }

            show(field, problem);
            first = first ?? (problem === '' ? null : field);
        });

        first?.focus();

        return first === null;
    };

    const text = () => [
        form.dataset.greeting,
        '',
        fields.message.value.trim(),
        '',
        `${form.dataset.nameLabel}: ${fields.name.value.trim()}`,
        `${form.dataset.emailLabel}: ${fields.email.value.trim()}`,
    ].join('\n');

    Object.values(fields).forEach((field) => field.addEventListener('input', () => {
        if (field.getAttribute('aria-invalid') === 'true') {
            show(field, '');
        }
    }));

    form.querySelectorAll('[data-send]').forEach((link) => link.addEventListener('click', (event) => {
        if (!check()) {
            event.preventDefault();

            return;
        }

        if (link.dataset.send === 'whatsapp') {
            const number = form.querySelector('[name="agent"]:checked')?.value ?? link.dataset.wa;

            link.href = `https://wa.me/${number}?text=${encodeURIComponent(text())}`;
        } else {
            link.href = `mailto:${form.dataset.mail}?subject=${encodeURIComponent(form.dataset.subject)}&body=${encodeURIComponent(text())}`;
        }

        if (status) {
            status.textContent = done;
        }
    }));
});
