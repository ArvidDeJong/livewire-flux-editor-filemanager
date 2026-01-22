/**
 * Example app.js configuration for Flux Editor with Filemanager
 * 
 * This file shows the complete setup including:
 * - Image extension with drag & drop and paste support
 * - Link extension with target, class, and style attributes
 * - All image attributes (width, style, align, class, alt, title)
 * - Laravel Filemanager integration
 */

import { Image } from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import { Plugin, PluginKey } from 'prosemirror-state'
import { initLaravelFilemanager } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'
import { getDragDropConfig, processImageFile } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/drag-drop-config.js'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'

// Add Image and Link extensions to Flux editor
document.addEventListener('flux:editor', (e) => {
    if (e.detail?.registerExtension) {
        // Register Link extension with target, class, and style attributes
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

        // Register Image extension with drag & drop, paste, and all attributes
        e.detail.registerExtension(Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: 'tiptap-image',
            },
        }).extend({
            addAttributes() {
                return {
                    ...this.parent?.(),
                    width: {
                        default: null,
                        parseHTML: element => element.getAttribute('width') || element.style.width,
                        renderHTML: attributes => {
                            if (!attributes.width) return {}
                            return {
                                width: attributes.width,
                                style: `width: ${attributes.width}`
                            }
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
                    class: {
                        default: 'tiptap-image',
                        parseHTML: element => element.getAttribute('class'),
                        renderHTML: attributes => {
                            if (!attributes.class) return {}
                            return { class: attributes.class }
                        },
                    },
                    alt: {
                        default: null,
                        parseHTML: element => element.getAttribute('alt'),
                        renderHTML: attributes => {
                            if (!attributes.alt) return {}
                            return { alt: attributes.alt }
                        },
                    },
                    title: {
                        default: null,
                        parseHTML: element => element.getAttribute('title'),
                        renderHTML: attributes => {
                            if (!attributes.title) return {}
                            return { title: attributes.title }
                        },
                    },
                }
            },
            addProseMirrorPlugins() {
                return [
                    new Plugin({
                        key: new PluginKey('imageDrop'),
                        props: {
                            handleDrop(view, event, slice, moved) {
                                if (!moved && event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
                                    const files = Array.from(event.dataTransfer.files)
                                    const imageFiles = files.filter(file => file.type.startsWith('image/'))

                                    if (imageFiles.length === 0) return false

                                    event.preventDefault()

                                    // Get config from editor element
                                    const editorElement = view.dom.closest('ui-editor')
                                    const config = getDragDropConfig(editorElement)

                                    imageFiles.forEach(async (file) => {
                                        try {
                                            const src = await processImageFile(file, config)

                                            const { schema } = view.state
                                            const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY })

                                            const node = schema.nodes.image.create({
                                                src: src,
                                                class: 'tiptap-image',
                                            })

                                            const transaction = view.state.tr.insert(coordinates.pos, node)
                                            view.dispatch(transaction)
                                        } catch (error) {
                                            console.error('Failed to process image:', error)
                                            alert(error.message || 'Failed to process image')
                                        }
                                    })

                                    return true
                                }
                                return false
                            },
                            handlePaste(view, event, slice) {
                                const items = Array.from(event.clipboardData?.items || [])
                                const imageItems = items.filter(item => item.type.startsWith('image/'))

                                if (imageItems.length === 0) return false

                                event.preventDefault()

                                // Get config from editor element
                                const editorElement = view.dom.closest('ui-editor')
                                const config = getDragDropConfig(editorElement)

                                imageItems.forEach(async (item) => {
                                    const file = item.getAsFile()
                                    if (!file) return

                                    try {
                                        const src = await processImageFile(file, config)

                                        const { schema } = view.state
                                        const { selection } = view.state

                                        const node = schema.nodes.image.create({
                                            src: src,
                                            class: 'tiptap-image',
                                        })

                                        const transaction = view.state.tr.replaceSelectionWith(node)
                                        view.dispatch(transaction)
                                    } catch (error) {
                                        console.error('Failed to process image:', error)
                                        alert(error.message || 'Failed to process image')
                                    }
                                })

                                return true
                            },
                        },
                    }),
                ]
            },
        }))
    }
})

// Initialize Laravel Filemanager integration
initLaravelFilemanager()
