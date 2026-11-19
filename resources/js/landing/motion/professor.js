import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const DIP = 230;
const GOLD = '#F2B84B';
const STEEL = '#98A2C4';

/**
 * The hero mascot electroplates a part: the gear is lowered into the bath, bubbles rise, it comes out gold
 * and shines. The artwork is plain inline SVG (parts are found by data-part), so without this script, or with
 * reduced motion, the scene is simply a still picture.
 */
export const professor = () => {
    const scene = document.querySelector('[data-professor-scene]');

    if (!scene) {
        return;
    }

    const part = (name) => scene.querySelector(`[data-part="${name}"]`);
    const parts = (names) => names.map(part);
    const gloves = parts(['glove-left', 'glove-right']);
    const pupils = parts(['pupil-left', 'pupil-right']);
    const eyes = [...parts(['eye-left', 'eye-right']), ...pupils];
    const [gear, wire, fill, shine, sparkle, bubbles, button, light] = ['gear', 'wire', 'gear-fill', 'gear-shine', 'sparkle', 'bubbles', 'button', 'light'].map(part);

    // GSAP works out SVG origins from each element's own box, so no CSS transform-box here.
    gsap.set(scene.querySelectorAll('[data-part="professor"] g'), { transformOrigin: '50% 50%' });
    gsap.set(gloves, { transformOrigin: '50% 100%' });
    gsap.set([...eyes, button, light, sparkle], { transformOrigin: '50% 50%' });

    // The gear hangs on a wire that grows as it goes down.
    const hoist = { depth: 0 };
    const place = () => {
        gsap.set(gear, { y: 1300 + hoist.depth });
        gsap.set(wire, { attr: { height: 150 + hoist.depth } });
    };
    place();

    const loop = gsap.timeline({ repeat: -1, paused: true, defaults: { ease: 'sine.inOut' } });

    loop
        .to(hoist, { depth: DIP, duration: 1.8, ease: 'power2.inOut', onUpdate: place }, 0.4)
        .to(gloves, { y: 22, duration: 1.8 }, 0.4)
        .to(fill, { fill: GOLD, stroke: GOLD, duration: 2.2, ease: 'none' }, 2.3)
        .to(hoist, { depth: 0, duration: 1.7, ease: 'power2.inOut', onUpdate: place }, 4.7)
        .to(gloves, { y: -14, duration: 1.7 }, 4.7)
        .fromTo(sparkle, { scale: 0, rotation: 0 }, { scale: 1.1, rotation: 45, duration: 0.35, ease: 'back.out(2)' }, 6.3)
        .to(sparkle, { scale: 0, duration: 0.35 }, 6.8)
        .fromTo(shine, { opacity: 0.2 }, { opacity: 1, duration: 0.3, yoyo: true, repeat: 1 }, 6.3)
        .to(gloves, { y: 0, duration: 0.6 }, 6.4)
        .to(gear, { opacity: 0, duration: 0.3, ease: 'none' }, 7.2)
        .set(fill, { fill: STEEL, stroke: STEEL }, 7.5)
        .to(gear, { opacity: 1, duration: 0.4, ease: 'none' }, 7.5);

    // Gloves tilt towards each other while they work the hoist.
    loop
        .to(gloves[0], { rotation: 6, duration: 1.6 }, 0.4)
        .to(gloves[1], { rotation: -6, duration: 1.6 }, 0.4)
        .to(gloves, { rotation: 0, duration: 1.4 }, 4.7);

    const idle = [
        // Relative: the mascot group already carries the translate that places it in the scene.
        gsap.to(part('professor'), { y: '+=8', duration: 1.8, yoyo: true, repeat: -1, ease: 'sine.inOut' }),
        gsap.to(part('goggles'), { y: -8, duration: 1.8, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 0.2 }),
        gsap.to(part('mouth'), { scaleX: 1.12, scaleY: 1.2, duration: 2, yoyo: true, repeat: -1, ease: 'sine.inOut' }),
        gsap.to(light, { opacity: 0.25, duration: 0.6, yoyo: true, repeat: -1, ease: 'none' }),
        gsap.to(button, { scale: 0.88, duration: 1.1, yoyo: true, repeat: -1, ease: 'sine.inOut' }),
    ];

    // He watches the gear go down and come back up.
    loop
        .to(pupils, { y: 12, duration: 1.8 }, 0.4)
        .to(pupils, { y: -6, duration: 1.7 }, 4.7)
        .to(pupils, { y: 0, duration: 0.6 }, 6.4);

    const bubbleTweens = gsap.utils.toArray(bubbles.children).map((bubble, index) => gsap.fromTo(bubble,
        { y: 0, opacity: 0.9 },
        { y: -150, opacity: 0, duration: 1.6 + (index % 3) * 0.4, repeat: -1, delay: index * 0.5, ease: 'sine.out', paused: true },
    ));

    const blink = gsap.timeline({ paused: true, repeat: -1, repeatDelay: 2.5 })
        .to(eyes, { scaleY: 0.08, duration: 0.07, yoyo: true, repeat: 1, ease: 'power1.inOut' });

    const everything = [loop, ...idle, ...bubbleTweens, blink];

    // Nothing moves while the hero is off screen.
    ScrollTrigger.create({
        trigger: scene,
        start: 'top bottom',
        end: 'bottom top',
        onToggle: ({ isActive }) => everything.forEach((animation) => animation.paused(!isActive)),
    });

    return () => {
        everything.forEach((animation) => animation.kill());
    };
};
