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

document.querySelectorAll('[data-material-open]').forEach((button) => {
    button.addEventListener('click', () => {
        if (!materialDialog) {
            return;
        }

        materialDialog.querySelector('[data-material-name]').textContent = button.dataset.name ?? '';
        materialDialog.querySelector('[data-material-usage]').textContent = button.dataset.usage ?? '';
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
