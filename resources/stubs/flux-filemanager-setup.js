// flux-filemanager:start
// Registers the Image and Link extensions on every Flux editor on the page.
// Written by `php artisan flux-filemanager:install`; keep it in sync with examples/app.js.
const FluxSafeImage = Image.extend({
    addNodeView() {
        return () => null
    },
    addAttributes() {
        return {
            ...this.parent?.(),
            class: {
                default: 'tiptap-image',
                parseHTML: element => element.getAttribute('class'),
                renderHTML: attributes => {
                    if (!attributes.class) return {}
                    return { class: attributes.class }
                },
            },
            style: {
                default: null,
                parseHTML: element => element.getAttribute('style'),
                renderHTML: attributes => {
                    if (!attributes.style) return {}
                    return { style: attributes.style }
                },
            },
            'data-align': {
                default: null,
                parseHTML: element => element.getAttribute('data-align'),
                renderHTML: attributes => {
                    if (!attributes['data-align']) return {}
                    return { 'data-align': attributes['data-align'] }
                },
            },
        }
    },
    addProseMirrorPlugins() {
        return [...(this.parent?.() ?? []), createImageDropPastePlugin()]
    },
})

document.addEventListener('flux:editor', (e) => {
    if (!e.detail?.registerExtension || e.detail.__fluxFilemanagerExtensionsRegistered) return

    e.detail.__fluxFilemanagerExtensionsRegistered = true

    e.detail.registerExtension(Link.configure({
        openOnClick: false,
        HTMLAttributes: {
            rel: 'noopener noreferrer nofollow',
        },
    }).extend({
        addAttributes() {
            return {
                ...this.parent?.(),
                target: {
                    default: '_blank',
                    parseHTML: element => element.getAttribute('target'),
                    renderHTML: attributes => {
                        if (!attributes.target) return {}
                        return { target: attributes.target }
                    },
                },
                class: {
                    default: null,
                    parseHTML: element => element.getAttribute('class'),
                    renderHTML: attributes => {
                        if (!attributes.class) return {}
                        return { class: attributes.class }
                    },
                },
                style: {
                    default: null,
                    parseHTML: element => element.getAttribute('style'),
                    renderHTML: attributes => {
                        if (!attributes.style) return {}
                        return { style: attributes.style }
                    },
                },
            }
        },
    }))

    e.detail.registerExtension(FluxSafeImage.configure({
        inline: true,
        allowBase64: true,
        resize: false,
        HTMLAttributes: {
            class: 'tiptap-image',
        },
    }))
})
// flux-filemanager:end
