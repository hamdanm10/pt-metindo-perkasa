import gsap from 'gsap';

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

document.querySelectorAll('[data-dialog-close]').forEach((button) => {
    button.addEventListener('click', () => button.closest('dialog')?.close());
});

// A click on the dimmed area (the dialog element itself) closes it, a click inside does not.
document.querySelectorAll('dialog[data-diagram-dialog], dialog[data-material-dialog]').forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
});

const materialDialog = document.querySelector('[data-material-dialog]');

// Only a secure link, or a file of this site, is put in the page, whatever the data holds.
const safeLink = (url, text) => {
    let target;

    try {
        target = new URL(url, window.location.href);
    } catch {
        return null;
    }

    if (typeof url !== 'string' || (target.protocol !== 'https:' && target.origin !== window.location.origin)) {
        return null;
    }

    const anchor = document.createElement('a');
    anchor.href = target.href;
    anchor.textContent = text;
    anchor.target = '_blank';
    anchor.rel = 'noopener noreferrer';
    anchor.className = 'font-semibold text-brand-700 underline underline-offset-4';

    return anchor;
};

const fill = (field, nodes) => {
    const target = materialDialog.querySelector(`[data-material-${field}]`);
    target.replaceChildren(...nodes);
    target.classList.toggle('text-ink/60', nodes.length === 0);

    if (nodes.length === 0) {
        target.textContent = '–';
    }
};

const requestLink = () => {
    const anchor = document.createElement('a');
    anchor.href = materialDialog.dataset.contactUrl ?? '#';
    anchor.textContent = materialDialog.dataset.requestLabel ?? '';
    anchor.className = 'font-semibold text-brand-700 underline underline-offset-4';

    return anchor;
};

document.querySelectorAll('[data-material-open]').forEach((button) => {
    button.addEventListener('click', () => {
        if (!materialDialog) {
            return;
        }

        let product = {};

        try {
            product = JSON.parse(button.dataset.product ?? '{}');
        } catch {
            product = {};
        }

        const open = materialDialog.dataset.openLabel ?? '';

        materialDialog.querySelector('[data-material-name]').textContent = product.name ?? '';
        materialDialog.querySelector('[data-material-usage]').textContent = product.usage ?? '';

        fill('brochure', [safeLink(product.brochure, open)].filter(Boolean));
        fill('video', [safeLink(product.video, open)].filter(Boolean));
        fill('ecommerce', (product.links ?? []).map((link) => safeLink(link.url, link.label)).filter(Boolean).flatMap((anchor, index) => (index === 0 ? [anchor] : [document.createTextNode(' · '), anchor])));

        fill('tds', [safeLink(product.tds, open) ?? requestLink()]);
        fill('price', product.price ? [document.createTextNode(product.price)] : [requestLink()]);

        materialDialog.showModal();
    });
});

const diagramDialog = document.querySelector('[data-diagram-dialog]');

if (diagramDialog) {
    const nodes = [...diagramDialog.querySelectorAll('[data-diagram-node]')];
    let timeline = null;

    const reset = () => {
        timeline?.kill();
        diagramDialog.querySelectorAll('[data-diagram-badge]').forEach((badge) => badge.removeAttribute('data-reached'));
        gsap.set(nodes, { clearProps: 'opacity,y' });
        gsap.set(diagramDialog.querySelectorAll('[data-diagram-link]'), { clearProps: 'transform' });
    };

    // Each step appears, its badge turns crimson (data-reached), and the line to the next step draws itself.
    const play = () => {
        reset();

        if (reduced) {
            return;
        }

        timeline = gsap.timeline();

        nodes.forEach((node) => {
            const badge = node.querySelector('[data-diagram-badge]');
            const link = node.querySelector('[data-diagram-link]');

            timeline
                .fromTo(node, { opacity: 0.25, y: 12 }, { opacity: 1, y: 0, duration: 0.45, ease: 'power2.out' })
                .call(() => badge.setAttribute('data-reached', ''), null, '<0.1');

            if (link) {
                timeline.fromTo(link, { scaleX: 0 }, { scaleX: 1, duration: 0.4, ease: 'power1.inOut' }, '>-0.05');
            }
        });
    };

    document.querySelectorAll('[data-diagram-open]').forEach((button) => {
        button.addEventListener('click', () => {
            diagramDialog.showModal();
            play();
        });
    });

    diagramDialog.querySelector('[data-diagram-replay]')?.addEventListener('click', play);
    diagramDialog.addEventListener('close', reset);
}
