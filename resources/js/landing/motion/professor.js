import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Geometry of the artwork in components/landing/professor-scene.blade.php (the SVG's own units).
const ARM = { upper: 140, fore: 150 };
const SHOULDER = { left: [430, 890], right: [770, 890] };
const LEVER = { x: 600, y: 1160, length: 136 };
const LANE = { first: 250, second: 950 };
const RACK = { up: 300, down: 1100 };
const COLOR = { steel: '#98A2C4', chrome: '#E3E8F7', gold: '#F2B84B' };

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
const deg = (radians) => (radians * 180) / Math.PI;

/** Two-bone arm: angles (degrees) of the upper arm and, relative to it, the forearm so the hand reaches the target. */
const reach = (shoulder, target, side) => {
    const dx = target[0] - shoulder[0];
    const dy = target[1] - shoulder[1];
    const distance = clamp(Math.hypot(dx, dy), Math.abs(ARM.upper - ARM.fore) + 1, ARM.upper + ARM.fore - 1);
    const bend = Math.acos((ARM.upper ** 2 + distance ** 2 - ARM.fore ** 2) / (2 * ARM.upper * distance));
    const upper = Math.atan2(dy, dx) + side * bend;
    const elbow = [shoulder[0] + ARM.upper * Math.cos(upper), shoulder[1] + ARM.upper * Math.sin(upper)];
    const fore = Math.atan2(target[1] - elbow[1], target[0] - elbow[0]);

    return [deg(upper), deg(fore - upper)];
};

/**
 * The hero scene: the professor runs a plating line. A hoist carries a rack of parts along the rail, dips it into the
 * cleaning bath, then into the gold bath (the parts come out gold), while he works the lever, watches the rack and
 * checks his clipboard. The artwork is inline SVG found by data-part; without this script, or with reduced motion,
 * it is simply a still picture.
 */
export const professor = () => {
    const scene = document.querySelector('[data-professor-scene]');

    if (!scene) {
        return;
    }

    const part = (name) => scene.querySelector(`[data-part="${name}"]`);
    const el = Object.fromEntries([
        'carriage', 'rope', 'rack', 'plated', 'lever', 'clipboard', 'arm-left', 'fore-left', 'arm-right', 'fore-right',
        'head', 'eye-left', 'eye-right', 'pupil-left', 'pupil-right', 'needle', 'light-a', 'light-b', 'light-c',
        'lamp-left', 'lamp-right', 'dial-left', 'dial-right', 'current-1', 'current-2', 'bubbles-1', 'bubbles-2',
    ].map((name) => [name, part(name)]));
    const sparks = gsap.utils.toArray('[data-part="spark"]', scene);

    const state = { cx: LANE.first, ry: RACK.up, lever: 0, first: 0, second: 0, blink: 1, sway: 0, previousX: LANE.first };

    const render = () => {
        const time = gsap.ticker.time;
        const amp = Math.max(state.first, state.second);

        el.carriage.setAttribute('transform', `translate(${state.cx} 120)`);
        el.rope.setAttribute('x1', state.cx);
        el.rope.setAttribute('x2', state.cx);
        el.rope.setAttribute('y2', state.ry - 16);

        // The rack lags behind the carriage a little and settles.
        state.sway += (clamp(-(state.cx - state.previousX) * 1.4, -7, 7) - state.sway) * 0.12;
        state.previousX = state.cx;
        el.rack.setAttribute('transform', `translate(${state.cx} ${state.ry}) rotate(${state.sway.toFixed(2)})`);

        const angle = (state.lever * Math.PI) / 180;
        el.lever.setAttribute('transform', `translate(${LEVER.x} ${LEVER.y}) rotate(${state.lever.toFixed(2)})`);

        const grip = [LEVER.x + LEVER.length * Math.sin(angle), LEVER.y - LEVER.length * Math.cos(angle)];
        const [rightUpper, rightFore] = reach(SHOULDER.right, grip, -1);
        el['arm-right'].setAttribute('transform', `translate(${SHOULDER.right[0]} ${SHOULDER.right[1]}) rotate(${rightUpper.toFixed(2)})`);
        el['fore-right'].setAttribute('transform', `translate(${ARM.upper} 0) rotate(${rightFore.toFixed(2)})`);

        // The other hand holds the clipboard and now and then writes on it.
        const writing = [430 + Math.sin(time * 2.3) * 4, 1030 + Math.sin(time * 3.1) * 3];
        const [leftUpper, leftFore] = reach(SHOULDER.left, writing, 1);
        el['arm-left'].setAttribute('transform', `translate(${SHOULDER.left[0]} ${SHOULDER.left[1]}) rotate(${leftUpper.toFixed(2)})`);
        el['fore-left'].setAttribute('transform', `translate(${ARM.upper} 0) rotate(${leftFore.toFixed(2)})`);
        el.clipboard.setAttribute('transform', `translate(${writing[0].toFixed(1)} ${writing[1].toFixed(1)}) rotate(-8)`);

        // He follows the rack with his head and eyes.
        const lookX = clamp((state.cx - 600) / 350, -1, 1);
        const lookY = clamp((state.ry - 700) / 400, -1, 1);
        el.head.setAttribute('transform', `rotate(${(lookX * 3).toFixed(2)} 600 800)`);
        el['eye-left'].setAttribute('transform', `translate(556 706) scale(1 ${state.blink.toFixed(2)})`);
        el['eye-right'].setAttribute('transform', `translate(644 706) scale(1 ${state.blink.toFixed(2)})`);
        [el['pupil-left'], el['pupil-right']].forEach((pupil) => {
            pupil.setAttribute('cx', (lookX * 10).toFixed(1));
            pupil.setAttribute('cy', (lookY * 8).toFixed(1));
        });

        // Meters and lamps.
        el.needle.setAttribute('transform', `translate(640 1228) rotate(${(-50 + amp * 95 + Math.sin(time * 9) * 3 * amp).toFixed(1)})`);
        el['dial-left'].setAttribute('transform', `translate(70 330) rotate(${(-40 + amp * 55 + Math.sin(time * 1.1) * 8).toFixed(1)})`);
        el['dial-right'].setAttribute('transform', `translate(1130 640) rotate(${(30 + state.second * 40 + Math.sin(time * 0.8) * 6).toFixed(1)})`);
        el['light-a'].style.opacity = amp > 0.5 && Math.sin(time * 7) < 0 ? 0.3 : 1;
        el['light-b'].style.opacity = amp > 0.5 && Math.sin(time * 7) >= 0 ? 0.3 : 1;
        el['light-c'].style.opacity = 0.55 + 0.45 * Math.sin(time * 5);
        el['lamp-left'].style.opacity = Math.sin(time * 3) > 0 ? 1 : 0.35;
        el['lamp-right'].style.opacity = Math.sin(time * 3) > 0 ? 0.35 : 1;

        // Current runs along the cable of the bath that is working, and the baths bubble harder.
        [[el['current-1'], state.first, el['bubbles-1']], [el['current-2'], state.second, el['bubbles-2']]].forEach(([cable, charge, bubbles]) => {
            cable.style.opacity = charge;
            cable.setAttribute('stroke-dashoffset', (-time * 60).toFixed(1));
            bubbles.style.opacity = 0.3 + charge * 0.7;
        });
    };

    render();

    const loop = gsap.timeline({ repeat: -1, paused: true, defaults: { ease: 'sine.inOut' } });
    const lowerRack = (at) => loop
        .to(state, { lever: 14, duration: 0.4 }, at)
        .to(state, { ry: RACK.down, duration: 1.7, ease: 'power2.inOut' }, at + 0.2)
        .to(state, { lever: 0, duration: 0.5 }, at + 1.6);
    const raiseRack = (at) => loop
        .to(state, { lever: -14, duration: 0.4 }, at)
        .to(state, { ry: RACK.up, duration: 1.6, ease: 'power2.inOut' }, at + 0.1)
        .to(state, { lever: 0, duration: 0.4 }, at + 1.5);
    const travel = (at, to, push) => loop
        .to(state, { lever: push, duration: 0.5 }, at)
        .to(state, { cx: to, duration: 1.9, ease: 'power2.inOut' }, at + 0.1)
        .to(state, { lever: 0, duration: 0.5 }, at + 1.7);

    // First bath: the parts are cleaned and turn bright.
    lowerRack(0.4);
    loop.to(state, { first: 1, duration: 0.5 }, 2.2)
        .to(el.plated, { color: COLOR.chrome, duration: 1.6, ease: 'none' }, 2.5);
    loop.to(state, { first: 0, duration: 0.4 }, 4.3);
    raiseRack(4.3);

    // Along the rail to the second bath, which plates them gold.
    travel(6.1, LANE.second, 24);
    lowerRack(8.1);
    loop.to(state, { second: 1, duration: 0.5 }, 9.9)
        .to(el.plated, { color: COLOR.gold, duration: 1.8, ease: 'none' }, 10.1);
    loop.to(state, { second: 0, duration: 0.4 }, 12);
    raiseRack(12);

    // The finished parts shine, then the rack goes back with a fresh batch.
    sparks.forEach((spark, index) => {
        loop.fromTo(spark, { scale: 0, rotation: 0 }, { scale: 1.1, rotation: 45, duration: 0.35, ease: 'back.out(2)' }, 13.7 + index * 0.18)
            .to(spark, { scale: 0, duration: 0.3 }, 14.2 + index * 0.18);
    });
    travel(14.4, LANE.first, -24);
    loop.to(el.plated, { opacity: 0, duration: 0.3, ease: 'none' }, 15.6)
        .set(el.plated, { color: COLOR.steel }, 15.9)
        .to(el.plated, { opacity: 1, duration: 0.4, ease: 'none' }, 16)
        .to({}, { duration: 0.6 }, 16.4);

    const blink = gsap.timeline({ paused: true, repeat: -1, repeatDelay: 2.6 })
        .to(state, { blink: 0.08, duration: 0.07, yoyo: true, repeat: 1, ease: 'power1.inOut' });

    const bubbles = [el['bubbles-1'], el['bubbles-2']].flatMap((group) => gsap.utils.toArray(group.children).map((bubble, index) => gsap.fromTo(
        bubble,
        { y: 0, opacity: 1 },
        { y: -(150 + index * 12), opacity: 0, duration: 1.7 + (index % 3) * 0.4, repeat: -1, delay: index * 0.45, ease: 'sine.out', paused: true },
    )));

    const animations = [loop, blink, ...bubbles];

    // Nothing runs while the hero is off screen.
    ScrollTrigger.create({
        trigger: scene,
        start: 'top bottom',
        end: 'bottom top',
        onToggle: ({ isActive }) => {
            animations.forEach((animation) => animation.paused(!isActive));
            gsap.ticker[isActive ? 'add' : 'remove'](render);
        },
    });

    return () => {
        gsap.ticker.remove(render);
        animations.forEach((animation) => animation.kill());
    };
};
