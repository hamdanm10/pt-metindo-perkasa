import gsap from 'gsap';

/**
 * The floating chat and WhatsApp buttons. One panel is open at a time. Opening: the panel grows
 * from its bottom corner (0.5 seconds, `expo.out`); closing: it fades down (0.25 seconds). Escape
 * or a click outside closes it and the focus goes back to its button.
 *
 * The chat is rule-based and has no text box. The server sends the topics for the active language,
 * each with a label, an answer and a link. The assistant greets, then shows the topics as option
 * bubbles; tapping one adds it as the visitor's message, the assistant answers, and the options
 * come back under the answer (with an option to talk to customer service when there are numbers).
 * All text is put in with `textContent`.
 */
const root = document.querySelector('[data-widgets]');

if (root) {
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const toggles = Array.from(root.querySelectorAll('[data-widget-toggle]'));
    const panels = Object.fromEntries(Array.from(root.querySelectorAll('[data-panel]')).map((panel) => [panel.dataset.panel, panel]));

    let open = null;

    const show = (name) => {
        const panel = panels[name];

        if (!panel) {
            return;
        }

        if (open && open !== name) {
            hide(false);
        }

        open = name;
        panel.hidden = false;
        toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', String(toggle.dataset.widgetToggle === name)));

        if (!calm) {
            gsap.fromTo(panel, { autoAlpha: 0, y: 18, scale: 0.9 }, { autoAlpha: 1, y: 0, scale: 1, duration: 0.5, ease: 'expo.out', clearProps: 'transform' });
        }

        const target = panel.querySelector('[data-chat-log] button, a[href]');

        window.setTimeout(() => target?.focus({ preventScroll: true }), calm ? 0 : 120);
    };

    function hide(returnFocus = true) {
        if (!open) {
            return;
        }

        const name = open;
        const panel = panels[name];

        open = null;
        toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));

        const done = () => {
            panel.hidden = true;
            gsap.set(panel, { clearProps: 'all' });
        };

        if (calm) {
            done();
        } else {
            gsap.to(panel, { autoAlpha: 0, y: 12, duration: 0.25, ease: 'power1.in', onComplete: done });
        }

        if (returnFocus) {
            toggles.find((toggle) => toggle.dataset.widgetToggle === name)?.focus();
        }
    }

    toggles.forEach((toggle) => toggle.addEventListener('click', () => (open === toggle.dataset.widgetToggle ? hide() : show(toggle.dataset.widgetToggle))));
    root.querySelectorAll('[data-widget-close]').forEach((button) => button.addEventListener('click', () => hide()));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && open) {
            hide();
        }
    });

    document.addEventListener('click', (event) => {
        // The path is taken when the click happens: a bubble that removed itself is no longer in the page, but it was inside.
        if (open && !event.composedPath().includes(root)) {
            hide(false);
        }
    });

    initChat();
}

function initChat() {
    const rulesNode = root.querySelector('[data-chat-rules]');
    const log = root.querySelector('[data-chat-log]');

    if (!rulesNode || !log) {
        return;
    }

    let rules;

    try {
        rules = JSON.parse(rulesNode.dataset.chatRules ?? '{}');
    } catch (error) {
        return;
    }

    const topics = Array.isArray(rules.topics) ? rules.topics : [];
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let options = null;

    const scroll = () => {
        log.scrollTop = log.scrollHeight;
    };

    const enter = (node) => {
        if (!calm) {
            gsap.from(node, { y: 12, autoAlpha: 0, duration: 0.4, ease: 'expo.out' });
        }
    };

    const bubble = (from) => {
        const row = document.createElement('div');
        const body = document.createElement('div');

        row.className = from === 'user' ? 'flex max-w-[88%] flex-col items-end self-end' : 'flex max-w-[88%] flex-col items-start self-start';
        body.className = from === 'user'
            ? 'rounded-2xl rounded-ee-sm bg-ink px-3.5 py-2 text-sm text-white'
            : 'rounded-2xl rounded-es-sm bg-white px-3.5 py-2 text-sm text-ink shadow-sm';
        row.append(body);
        log.append(row);
        enter(row);

        return { row, body };
    };

    const say = (from, text, link = null) => {
        const { row, body } = bubble(from);

        body.textContent = text;

        if (link) {
            const anchor = document.createElement('a');

            anchor.href = link.url;
            anchor.textContent = `${link.label} →`;
            anchor.className = 'mt-1.5 inline-block text-xs font-bold text-brand-700 underline underline-offset-2';
            row.append(anchor);
        }

        scroll();

        return row;
    };

    const option = (label, onChoose) => {
        const button = document.createElement('button');

        button.type = 'button';
        button.textContent = label;
        button.className = 'rounded-2xl rounded-es-sm border border-brand-600 bg-white px-3.5 py-2 text-start text-sm font-semibold text-brand-700 transition-colors hover:bg-brand-600 hover:text-white focus-visible:outline-offset-2';
        button.addEventListener('click', onChoose);

        return button;
    };

    /** The option bubbles, left aligned like the assistant's. Taking them away once one is chosen keeps the log short. */
    const showOptions = () => {
        options?.remove();
        options = document.createElement('div');
        options.className = 'flex flex-col items-start gap-2 self-start';
        options.setAttribute('role', 'group');

        topics.forEach((topic) => {
            options.append(option(topic.label, () => choose(topic)));
        });

        if (rules.talk) {
            const talk = option(rules.talk, () => {
                options?.remove();
                root.querySelector('[data-widget-toggle="whatsapp"]')?.click();
            });

            talk.className = talk.className.replace('border-brand-600 bg-white text-brand-700', 'border-[#128c4a] bg-white text-[#128c4a]').replace('hover:bg-brand-600', 'hover:bg-[#128c4a]');
            options.append(talk);
        }

        log.append(options);
        enter(options);
        scroll();
    };

    const choose = (topic) => {
        options?.remove();
        options = null;
        const asked = say('user', topic.label);

        const typing = bubble('bot');

        typing.body.textContent = '...';
        typing.body.setAttribute('aria-hidden', 'true');
        scroll();

        window.setTimeout(() => {
            typing.row.remove();
            say('bot', topic.answer, topic.link);
            say('bot', rules.again ?? '');
            showOptions();
            // Keep the question and its answer in view; the options sit below and can be reached by scrolling.
            log.scrollTo({ top: Math.max(asked.offsetTop - 12, 0), behavior: calm ? 'auto' : 'smooth' });
        }, calm ? 0 : 550);
    };

    say('bot', rules.welcome ?? '');
    showOptions();
}
