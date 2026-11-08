import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';

const buttons = [
    ['bold', 'bold', (e) => e.chain().focus().toggleBold().run(), (e) => e.isActive('bold')],
    ['italic', 'italic', (e) => e.chain().focus().toggleItalic().run(), (e) => e.isActive('italic')],
    ['heading2', 'H2', (e) => e.chain().focus().toggleHeading({ level: 2 }).run(), (e) => e.isActive('heading', { level: 2 })],
    ['heading3', 'H3', (e) => e.chain().focus().toggleHeading({ level: 3 }).run(), (e) => e.isActive('heading', { level: 3 })],
    ['bullet_list', '•', (e) => e.chain().focus().toggleBulletList().run(), (e) => e.isActive('bulletList')],
    ['ordered_list', '1.', (e) => e.chain().focus().toggleOrderedList().run(), (e) => e.isActive('orderedList')],
    ['quote', '“”', (e) => e.chain().focus().toggleBlockquote().run(), (e) => e.isActive('blockquote')],
    ['link', 'link', null, (e) => e.isActive('link')],
    ['undo', '↶', (e) => e.chain().focus().undo().run(), () => false],
    ['redo', '↷', (e) => e.chain().focus().redo().run(), () => false],
];

const parse = (raw) => {
    if (!raw) {
        return '';
    }

    try {
        return JSON.parse(raw);
    } catch {
        return '';
    }
};

const mount = (host) => {
    if (host.dataset.mounted) {
        return;
    }

    host.dataset.mounted = 'true';

    const input = document.getElementById(host.dataset.input);
    const labels = JSON.parse(host.dataset.labels || '{}');

    const toolbar = document.createElement('div');
    toolbar.className = 'flex flex-wrap gap-1 rounded-t-lg border border-b-0 border-ink/50 bg-lavender p-2';
    toolbar.setAttribute('role', 'toolbar');
    toolbar.setAttribute('aria-labelledby', host.dataset.labelledBy);

    const surface = document.createElement('div');
    surface.className = 'rounded-b-lg border border-ink/50 bg-white';

    host.append(toolbar, surface);

    const editor = new Editor({
        element: surface,
        extensions: [
            StarterKit.configure({
                heading: { levels: [2, 3] },
                code: false,
                codeBlock: false,
                horizontalRule: false,
                strike: false,
            }),
            Link.configure({ openOnClick: false, autolink: false }),
        ],
        content: parse(host.dataset.content),
        editorProps: {
            attributes: {
                class: 'article-body min-h-64 px-4 py-3 text-base focus:outline-none',
                dir: host.dataset.dir || 'ltr',
                lang: host.dataset.lang || 'en',
                role: 'textbox',
                'aria-multiline': 'true',
                'aria-labelledby': host.dataset.labelledBy,
            },
        },
        onUpdate: ({ editor: current }) => {
            if (!input) {
                return;
            }

            input.value = current.isEmpty ? '' : JSON.stringify(current.getJSON());
            input.dispatchEvent(new Event('input', { bubbles: true }));
        },
        onTransaction: () => refresh(),
    });

    const elements = buttons.map(([key, text, run, active]) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = text === 'bold' || text === 'italic' || text === 'link' ? labels[key] : text;
        button.title = labels[key] || key;
        button.setAttribute('aria-label', labels[key] || key);
        button.className = 'inline-flex min-h-10 min-w-10 items-center justify-center rounded-md px-3 text-sm font-bold text-ink hover:bg-white aria-pressed:bg-ink aria-pressed:text-white';

        if (key === 'bold') {
            button.classList.add('font-black');
        }

        if (key === 'italic') {
            button.classList.add('italic');
        }

        button.addEventListener('click', () => {
            if (key === 'link') {
                const previous = editor.getAttributes('link').href || '';
                const url = window.prompt(labels.link_prompt, previous);

                if (url === null) {
                    return;
                }

                if (url.trim() === '') {
                    editor.chain().focus().unsetLink().run();
                } else {
                    editor.chain().focus().extendMarkRange('link').setLink({ href: url.trim() }).run();
                }

                return;
            }

            run(editor);
        });

        toolbar.append(button);

        return [button, active];
    });

    function refresh() {
        elements.forEach(([button, active]) => {
            button.setAttribute('aria-pressed', String(active(editor)));
        });
    }

    refresh();
};

const mountAll = () => document.querySelectorAll('[data-tiptap]').forEach(mount);

mountAll();
document.addEventListener('livewire:navigated', mountAll);
