import gsap from 'gsap';

/**
 * The process list: a list of categories on the left and the chosen one in a large panel on the
 * right (tabs with arrow keys, Home and End). Only a click or a tap chooses a category; hovering
 * does nothing, so moving the pointer down the list never restarts the panel animation. The white marker slides to the chosen row (0.6
 * seconds, `expo.out`) and the panel changes: the picture opens upward from its bottom edge while
 * its photo settles from 115%, the big number, the heading, the text, and each process rise in
 * one after another, and the previous panel fades away. With reduced motion the panel just changes.
 */
document.querySelectorAll('[data-ptabs]').forEach((root) => {
    const tabs = Array.from(root.querySelectorAll('[data-ptab]'));
    const panels = Array.from(root.querySelectorAll('[data-ppanel]'));
    const bar = root.querySelector('[data-ptabs-bar]');
    const list = root.querySelector('[data-ptabs-list]');
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const wide = window.matchMedia('(min-width: 1024px)');

    if (tabs.length < 2 || tabs.length !== panels.length) {
        return;
    }

    let current = 0;

    const placeBar = (index, instant = false) => {
        if (!bar || !wide.matches) {
            return;
        }

        const tab = tabs[index];
        const vars = { y: tab.offsetTop, height: tab.offsetHeight };

        if (instant || calm) {
            gsap.set(bar, vars);
        } else {
            gsap.to(bar, { ...vars, duration: 0.6, ease: 'expo.out', overwrite: true });
        }
    };

    const reveal = (panel) => {
        const media = panel.querySelector('[data-pmedia]');
        const inner = panel.querySelector('[data-pmedia-inner]');
        const number = panel.querySelector('[data-pnumber] > span');
        const title = panel.querySelector('[data-ptitle]');
        const items = Array.from(panel.querySelectorAll('[data-pitem]'));

        gsap.timeline({ defaults: { ease: 'expo.out' } })
            .fromTo(media, { clipPath: 'inset(100% 0 0 0)' }, { clipPath: 'inset(0% 0 0 0)', duration: 0.9, clearProps: 'clipPath' }, 0)
            .fromTo(inner, { scale: 1.15 }, { scale: 1, duration: 1.2, clearProps: 'transform' }, 0)
            .fromTo(number, { yPercent: 100 }, { yPercent: 0, duration: 0.9, clearProps: 'transform' }, 0.25)
            .fromTo(title, { y: 36, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.8, clearProps: 'transform,opacity,visibility' }, 0.2)
            .fromTo(items, { y: 22, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7, stagger: 0.035, clearProps: 'transform,opacity,visibility' }, 0.3);
    };

    const select = (index, focus = false) => {
        const next = (index + tabs.length) % tabs.length;

        if (next === current) {
            if (focus) {
                tabs[next].focus();
            }

            return;
        }

        const leaving = panels[current];

        tabs.forEach((tab, position) => {
            tab.setAttribute('aria-selected', String(position === next));
            tab.tabIndex = position === next ? 0 : -1;
        });

        current = next;
        placeBar(current);

        if (calm) {
            leaving.removeAttribute('data-active');
            panels[current].setAttribute('data-active', '');
        } else {
            gsap.to(leaving, {
                autoAlpha: 0,
                duration: 0.25,
                ease: 'power1.in',
                onComplete: () => {
                    leaving.removeAttribute('data-active');
                    gsap.set(leaving, { clearProps: 'opacity,visibility' });
                },
            });
            panels[current].setAttribute('data-active', '');
            gsap.set(panels[current], { autoAlpha: 1 });
            reveal(panels[current]);
        }

        if (!wide.matches) {
            tabs[current].scrollIntoView({ behavior: calm ? 'auto' : 'smooth', inline: 'center', block: 'nearest' });
        }

        if (focus) {
            tabs[current].focus();
        }
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => select(index));
    });

    list.addEventListener('keydown', (event) => {
        const moves = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1 };

        if (event.key in moves) {
            event.preventDefault();
            select(current + moves[event.key], true);
        } else if (event.key === 'Home') {
            event.preventDefault();
            select(0, true);
        } else if (event.key === 'End') {
            event.preventDefault();
            select(tabs.length - 1, true);
        }
    });

    wide.addEventListener('change', () => placeBar(current, true));
    window.addEventListener('resize', () => placeBar(current, true));
    document.fonts?.ready.then(() => placeBar(current, true));
    placeBar(current, true);
});
