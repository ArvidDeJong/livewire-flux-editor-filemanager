import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import * as fluxFilemanager from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'

// flux-filemanager:start
// Registers the Image and Link extensions on every Flux editor on the page.
// Written by `php artisan flux-filemanager:install`; keep it in sync with examples/app.js.
// Everything from the package is read off the `fluxFilemanager` namespace, so an outdated
// copy under vendor/ only costs the feature it lacks instead of breaking this whole file.
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
        const dropPaste = fluxFilemanager.createImageDropPastePlugin?.()

        if (!dropPaste) {
            console.warn('flux-filemanager: the package JavaScript has no drop and paste plugin. Restart the Vite dev server after updating the package, because it does not watch vendor/.')
        }

        return [...(this.parent?.() ?? []), ...(dropPaste ? [dropPaste] : [])]
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

fluxFilemanager.initLaravelFilemanager()
