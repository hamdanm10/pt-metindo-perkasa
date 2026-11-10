import gsap from 'gsap';

/**
 * The floating chat and WhatsApp buttons. One panel is open at a time. Opening: the panel grows
 * from its bottom corner (0.5 seconds, `expo.out`); closing: it fades down (0.25 seconds). Escape
 * or a click outside closes it and the focus goes back to its button.
 *
 * The chat is rule-based. The server sends the topics for the active language: a label, an
 * answer, a link, and keywords. A typed question gets the topic whose keywords appear in it most
 * often; a topic button answers directly; otherwise the assistant says it cannot answer and offers
 * the topics. All text is put in with `textContent`, so nothing typed is ever read as HTML.
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

        const target = panel.querySelector('[data-chat-input], a[href]');

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
        if (open && event.target instanceof Node && !root.contains(event.target)) {
            hide(false);
        }
    });

    initChat();
}

function initChat() {
    const rulesNode = root.querySelector('[data-chat-rules]');
    const log = root.querySelector('[data-chat-log]');
    const topicList = root.querySelector('[data-chat-topics]');
    const form = root.querySelector('[data-chat-form]');
    const input = root.querySelector('[data-chat-input]');

    if (!rulesNode || !log || !form || !input) {
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

    const scroll = () => {
        log.scrollTop = log.scrollHeight;
    };

    const bubble = (from) => {
        const row = document.createElement('div');
        const body = document.createElement('div');

        row.className = from === 'user' ? 'flex max-w-[88%] flex-col items-end self-end' : 'flex max-w-[88%] flex-col items-start self-start';
        body.className = from === 'user'
            ? 'rounded-lg rounded-ee-sm bg-ink px-3 py-2 text-sm text-white'
            : 'rounded-lg rounded-es-sm bg-white px-3 py-2 text-sm text-ink shadow-sm';
        row.append(body);
        log.append(row);

        if (!calm) {
            gsap.from(row, { y: 12, autoAlpha: 0, duration: 0.4, ease: 'expo.out' });
        }

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
    };

    const reply = (topic) => {
        const typing = bubble('bot');

        typing.body.textContent = '...';
        typing.body.setAttribute('aria-hidden', 'true');
        scroll();

        window.setTimeout(() => {
            typing.row.remove();
            say('bot', topic ? topic.answer : rules.fallback, topic?.link ?? null);
        }, calm ? 0 : 550);
    };

    const match = (question) => {
        const text = question.toLowerCase();
        let best = null;
        let bestScore = 0;

        topics.forEach((topic) => {
            const score = (topic.keywords ?? []).filter((word) => word !== '' && text.includes(word)).length;

            if (score > bestScore) {
                best = topic;
                bestScore = score;
            }
        });

        return best;
    };

    topics.forEach((topic) => {
        const item = document.createElement('li');
        const button = document.createElement('button');

        button.type = 'button';
        button.textContent = topic.label;
        button.className = 'shrink-0 whitespace-nowrap rounded-full border border-ink/25 px-3 py-1 text-xs font-bold text-ink hover:border-brand-600 hover:bg-brand-600 hover:text-white';
        button.addEventListener('click', () => {
            say('user', topic.label);
            reply(topic);
        });
        item.append(button);
        topicList.append(item);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const question = input.value.trim();

        if (question === '') {
            return;
        }

        input.value = '';
        say('user', question);
        reply(match(question));
    });

    say('bot', rules.welcome ?? '');
}
