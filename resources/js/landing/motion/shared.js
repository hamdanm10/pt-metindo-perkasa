import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

export const root = document.documentElement;
export const all = (selector, scope) => gsap.utils.toArray(selector, scope);

/** Arabic letters join, so headlines in Arabic are never split into characters. */
export const canSplitChars = () => !root.lang.toLowerCase().startsWith('ar');

export const rise = { autoAlpha: 0, y: 28, duration: 0.7, ease: 'power3.out' };

/** Letters grow up from the baseline one after another, as in the reference hero. */
export const growLetters = (element, vars, { revert = false } = {}) => {
    const split = SplitText.create(element, { type: 'words,chars' });

    gsap.set(split.chars, { transformOrigin: '50% 100%' });

    return gsap.fromTo(split.chars, { scaleY: 0 }, {
        scaleY: 1,
        ease: 'power3.out',
        ...vars,
        onComplete: revert ? () => split.revert() : undefined,
    });
};
