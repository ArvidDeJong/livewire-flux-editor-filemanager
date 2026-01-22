/**
 * Laravel Filemanager integration for Flux Editor
 * Generic implementation that works for all Flux editors
 */

/**
 * Get configuration value with fallback
 * @param {string} key - Configuration key
 * @param {*} defaultValue - Default value if config not found
 * @returns {*} Configuration value
 */
function getConfig(key, defaultValue) {
    if (typeof window.fluxFilemanagerConfig !== 'undefined') {
        return window.fluxFilemanagerConfig[key] ?? defaultValue
    }
    return defaultValue
}

/**
 * Open Laravel Filemanager popup
 * @param {string} type - 'Images' or 'Files'
 * @param {function} onPicked - Callback function with selected URLs
 */
function openLaravelFilemanager(type, onPicked) {
    const config = {
        width: getConfig('popup_width', 900),
        height: getConfig('popup_height', 600),
        url: getConfig('filemanager_url', '/cms/laravel-filemanager')
    }

    const left = window.screenX + (window.outerWidth - config.width) / 2
    const top = window.screenY + (window.outerHeight - config.height) / 2

    const url = `${config.url}?type=${encodeURIComponent(type)}`

    try {
        const fm = window.open(
            url,
            'LaravelFilemanager',
            `width=${config.width},height=${config.height},left=${left},top=${top}`
        )

        // Check if popup was blocked
        if (!fm || fm.closed || typeof fm.closed === 'undefined') {
            const message = getConfig('popup_blocked_message',
                'Popup was blocked by your browser. Please allow popups for this site.')
            alert(message)
            return
        }

        // UniSharp Laravel Filemanager callback
        window.SetUrl = function (items) {
            const urls = (items || [])
                .map(i => i?.url)
                .filter(Boolean)

            try {
                if (urls.length === 0) {
                }
                onPicked(urls)
            } catch (error) {
            } finally {
                delete window.SetUrl
                if (fm && !fm.closed) {
                    try {
                        fm.close()
                    } catch (e) {
                    }
                }
            }
        }
    } catch (error) {
        const message = getConfig('filemanager_error_message',
            'Laravel Filemanager could not be loaded. Please check your installation.')
        alert(message)
    }
}

/**
 * Insert image(s) via Laravel Filemanager
 * @param {object} editor - TipTap editor instance
 */
function insertImageFromFilemanager(editor) {
    openLaravelFilemanager('Images', (urls) => {
        if (!urls.length) return

        urls.forEach((src) => {
            editor.chain().focus().setImage({
                src,
                width: '100%' // Default width, can be resized via context menu
            }).run()
        })
    })
}

/**
 * Enable image resize functionality
 * Creates a resize menu when clicking on images in the editor
 */
function enableImageResize() {

    document.addEventListener('click', (e) => {
        const img = e.target.closest('.ProseMirror img')
        if (!img) return


        // Create resize menu if it doesn't exist yet
        let menu = document.querySelector('.image-resize-menu')

        if (!menu) {
            menu = document.createElement('div')
            menu.className = 'image-resize-menu'
            menu.innerHTML = `
                <div class="resize-section">
                    <button data-width="25%">25%</button>
                    <button data-width="50%">50%</button>
                    <button data-width="75%">75%</button>
                    <button data-width="100%">100%</button>
                    <div class="custom-width-input">
                        <input type="number" min="1" max="100" placeholder="%" class="width-input" />
                        <button class="apply-custom">OK</button>
                    </div>
                </div>
                <div class="align-section">
                    <button data-align="left" title="Align left">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="15" y2="12"></line>
                            <line x1="3" y1="18" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <button data-align="center" title="Align center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="6" y1="12" x2="18" y2="12"></line>
                            <line x1="5" y1="18" x2="19" y2="18"></line>
                        </svg>
                    </button>
                    <button data-align="right" title="Align right">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="9" y1="12" x2="21" y2="12"></line>
                            <line x1="6" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                </div>
            `
            document.body.appendChild(menu)

            // Handle resize button clicks
            menu.addEventListener('click', (e) => {
                const btn = e.target.closest('button:not(.apply-custom)')
                const activeImg = document.querySelector('.ProseMirror img.active-resize')

                if (btn && btn.dataset.width) {
                    const width = btn.dataset.width

                    if (activeImg) {
                        const editorElement = activeImg.closest('ui-editor')
                        if (editorElement?.editor) {
                            // Get current image attributes
                            const src = activeImg.getAttribute('src')
                            const currentClass = activeImg.getAttribute('class') || 'tiptap-image'
                            const dataAlign = activeImg.getAttribute('data-align')


                            // Get the image position
                            const pos = editorElement.editor.view.posAtDOM(activeImg, 0)

                            // Get content before update
                            const contentBefore = editorElement.editor.getHTML()

                            // Delete old image and insert new one with updated attributes
                            editorElement.editor
                                .chain()
                                .focus()
                                .setNodeSelection(pos)
                                .deleteSelection()
                                .insertContentAt(pos, {
                                    type: 'image',
                                    attrs: {
                                        src: src,
                                        width: width,
                                        style: `width: ${width}`,
                                        class: currentClass,
                                        'data-align': dataAlign
                                    }
                                })
                                .run()

                            // Get content after update
                            const contentAfter = editorElement.editor.getHTML()

                            // Force Livewire to sync by triggering blur event
                            setTimeout(() => {
                                const event = new Event('blur', { bubbles: true })
                                editorElement.dispatchEvent(event)
                                // Also trigger input for immediate sync
                                const inputEvent = new Event('input', { bubbles: true })
                                editorElement.dispatchEvent(inputEvent)
                            }, 100)
                        } else {
                        }
                    } else {
                    }
                    menu.classList.remove('show')
                }

                // Handle align button clicks
                if (btn && btn.dataset.align) {
                    const align = btn.dataset.align
                    if (activeImg) {
                        const editorElement = activeImg.closest('ui-editor')
                        if (editorElement?.editor) {
                            // Get the image position in the document
                            const pos = editorElement.editor.view.posAtDOM(activeImg, 0)

                            // Calculate margin styles
                            let marginStyle = ''
                            if (align === 'left') {
                                marginStyle = 'margin-left: 0; margin-right: auto;'
                            } else if (align === 'center') {
                                marginStyle = 'margin-left: auto; margin-right: auto; display: block;'
                            } else if (align === 'right') {
                                marginStyle = 'margin-left: auto; margin-right: 0;'
                            }

                            // Get current width to preserve it
                            const currentWidth = activeImg.getAttribute('width') || activeImg.style.width
                            const widthStyle = currentWidth ? `width: ${currentWidth};` : ''

                            // Update image attributes using TipTap commands
                            editorElement.editor
                                .chain()
                                .focus()
                                .setNodeSelection(pos)
                                .updateAttributes('image', {
                                    'data-align': align,
                                    class: `tiptap-image align-${align}`,
                                    style: `${widthStyle} ${marginStyle}`.trim()
                                })
                                .run()

                            // Force Livewire to sync
                            setTimeout(() => {
                                const event = new Event('blur', { bubbles: true })
                                editorElement.dispatchEvent(event)
                                const inputEvent = new Event('input', { bubbles: true })
                                editorElement.dispatchEvent(inputEvent)
                            }, 100)
                        }
                    }
                    menu.classList.remove('show')
                }
            })

            // Handle custom width input
            const applyCustomBtn = menu.querySelector('.apply-custom')
            const widthInput = menu.querySelector('.width-input')

            applyCustomBtn.addEventListener('click', () => {
                const value = parseInt(widthInput.value)
                if (value && value > 0 && value <= 100) {
                    const activeImg = document.querySelector('.ProseMirror img.active-resize')
                    if (activeImg) {
                        const editorElement = activeImg.closest('ui-editor')
                        if (editorElement?.editor) {
                            const width = `${value}%`
                            const src = activeImg.getAttribute('src')
                            const currentClass = activeImg.getAttribute('class') || 'tiptap-image'
                            const dataAlign = activeImg.getAttribute('data-align')
                            const pos = editorElement.editor.view.posAtDOM(activeImg, 0)

                            // Delete old image and insert new one with updated attributes
                            editorElement.editor
                                .chain()
                                .focus()
                                .setNodeSelection(pos)
                                .deleteSelection()
                                .insertContentAt(pos, {
                                    type: 'image',
                                    attrs: {
                                        src: src,
                                        width: width,
                                        style: `width: ${width}`,
                                        class: currentClass,
                                        'data-align': dataAlign
                                    }
                                })
                                .run()

                            // Force Livewire to sync
                            setTimeout(() => {
                                const event = new Event('blur', { bubbles: true })
                                editorElement.dispatchEvent(event)
                                const inputEvent = new Event('input', { bubbles: true })
                                editorElement.dispatchEvent(inputEvent)
                            }, 100)
                        }
                    }
                    menu.classList.remove('show')
                    widthInput.value = ''
                }
            })

            // Allow Enter key to apply custom width
            widthInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    applyCustomBtn.click()
                }
            })
        }

        // Position menu near image (always, whether new or existing)
        const rect = img.getBoundingClientRect()
        const top = rect.bottom + window.scrollY + 5
        const left = rect.left + window.scrollX
        menu.style.top = `${top}px`
        menu.style.left = `${left}px`


        menu.classList.add('show')


        document.querySelectorAll('.ProseMirror img').forEach(i => i.classList.remove('active-resize'))
        img.classList.add('active-resize')

    })

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.ProseMirror img') && !e.target.closest('.image-resize-menu')) {
            const menu = document.querySelector('.image-resize-menu')
            if (menu) menu.classList.remove('show')
        }
    })
}

/**
 * Initialize Laravel Filemanager integration
 * Sets up the image button click handler and enables resize functionality
 */
export function initLaravelFilemanager() {

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setupImageButtonListener()
            enableImageResize()
        })
    } else {
        setupImageButtonListener()
        enableImageResize()
    }
}

/**
 * Setup image button click listener
 */
function setupImageButtonListener() {

    // Event listener for image button (works for all Flux editors)
    document.addEventListener('click', (e) => {
        const imageButton = e.target.closest('[data-editor="image"]')
        if (!imageButton) return

        e.preventDefault()
        e.stopPropagation()

        const editorElement = imageButton.closest('ui-editor')
        if (!editorElement?.editor) {
            return
        }

        insertImageFromFilemanager(editorElement.editor)
    })
}

/**
 * Initialize Laravel Filemanager for all Flux editors
 * Sets up event listeners and resize functionality
 */
export function initLaravelFilemanagerForAllEditors() {

    // Event listener for image button (works for all Flux editors)
    document.addEventListener('click', (e) => {
        const imageButton = e.target.closest('[data-editor="image"]')
        if (!imageButton) return

        e.preventDefault()
        e.stopPropagation()

        const editorElement = imageButton.closest('ui-editor')
        if (!editorElement?.editor) return

        insertImageFromFilemanager(editorElement.editor)
    })

    // Enable image resize functionality
    enableImageResize()
}
