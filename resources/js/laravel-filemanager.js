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

function t(key, fallback) {
    const translations = getConfig('i18n', {})
    return translations?.[key] || fallback
}

function normalizePickedUrl(rawUrl) {
    if (!rawUrl) return rawUrl

    try {
        const parsed = new URL(rawUrl, window.location.origin)
        const pointsToLocalStorage = parsed.pathname.startsWith('/storage/')
        const pointsToLfm = parsed.pathname.startsWith('/laravel-filemanager')
            || parsed.pathname.startsWith('/filemanager')
            || parsed.pathname.startsWith('/cms/filemanager')
            || parsed.pathname.startsWith('/cms/laravel-filemanager')

        if ((pointsToLocalStorage || pointsToLfm) && parsed.origin !== window.location.origin) {
            return `${parsed.pathname}${parsed.search}${parsed.hash}`
        }

        return parsed.toString()
    } catch {
        return rawUrl
    }
}

function extractUrlsFromLfmItems(items) {
    if (!items) return []

    const values = Array.isArray(items) ? items : [items]

    return values
        .map((item) => {
            if (typeof item === 'string') return item
            return item?.url || item?.path || item?.thumb_url || null
        })
        .map((url) => normalizePickedUrl(url))
        .filter(Boolean)
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
        url: getConfig('filemanager_url', '/filemanager')
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
                t('popup_blocked_message', 'Popup was blocked by your browser. Please allow popups for this site.'))
            alert(message)
            return
        }

        // UniSharp Laravel Filemanager callback
        window.SetUrl = function (items) {
            const urls = extractUrlsFromLfmItems(items)

            try {
                if (urls.length === 0) return
                onPicked(urls)
            } catch (error) {
                console.error('Failed processing selected filemanager items:', error)
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
            t('filemanager_error_message', 'Laravel Filemanager could not be loaded. Please check your installation.'))
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
            try {
                const inserted = editor.chain().focus().setImage({
                    src,
                    width: '400px' // Default width, can be resized via context menu
                }).run()

                if (inserted) {
                    return
                }
            } catch (error) {
                console.warn('setImage command failed, using fallback insertion.', error)
            }

            const escapedSrc = String(src).replace(/"/g, '&quot;')

            try {
                const insertedHtml = editor
                    .chain()
                    .focus()
                    .insertContent(`<img src="${escapedSrc}" class="tiptap-image" style="width: 400px;" />`)
                    .run()

                if (insertedHtml) {
                    return
                }
            } catch (error) {
                console.warn('HTML image insertion failed, using link fallback.', error)
            }

            editor
                .chain()
                .focus()
                .insertContent(`<a href="${escapedSrc}" target="_blank" rel="noopener noreferrer">${escapedSrc}</a>`)
                .run()
        })
    })
}

/**
 * Insert file link via Laravel Filemanager
 * @param {object} editor - TipTap editor instance
 */
function insertFileLinkFromFilemanager(editor) {
    openLaravelFilemanager('Files', (urls) => {
        if (!urls.length) return

        const url = urls[0] // Take first selected file

        // Show modal for link configuration
        showFileLinkModal(editor, url)
    })
}

/**
 * Show file link configuration modal
 * @param {object} editor - TipTap editor instance
 * @param {string} url - File URL
 */
function showFileLinkModal(editor, url) {
    // Create modal
    const modal = document.createElement('div')
    modal.className = 'file-link-modal-overlay'

    // Get filename from URL
    const filename = url.split('/').pop()

    modal.innerHTML = `
        <div class="file-link-modal">
            <div class="file-link-modal-header">
                <h3>${t('insert_link', 'Insert File Link')}</h3>
                <button class="file-link-modal-close" type="button">&times;</button>
            </div>
            <div class="file-link-modal-body">
                <div class="form-group">
                    <label>${t('file', 'File')}:</label>
                    <input type="text" class="file-url" value="${url}" readonly />
                </div>
                <div class="form-group">
                    <label>${t('link_text', 'Link Text')}:</label>
                    <input type="text" class="link-text" value="${filename}" placeholder="${t('link_text_placeholder', 'Click here to download')}" />
                </div>
                <div class="form-group">
                    <label>${t('target', 'Target')}:</label>
                    <select class="link-target">
                        <option value="_blank">${t('target_blank', 'New window (_blank)')}</option>
                        <option value="_self">${t('target_self', 'Same window (_self)')}</option>
                        <option value="_parent">${t('target_parent', 'Parent window (_parent)')}</option>
                        <option value="_top">${t('target_top', 'Top window (_top)')}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>${t('extra_css_classes', 'Extra CSS Classes')}:</label>
                    <input type="text" class="link-classes" value="" placeholder="${t('link_css_classes_placeholder', 'e.g. btn btn-primary')}" />
                </div>
                <div class="form-group">
                    <label>${t('extra_styles', 'Extra Styles')}:</label>
                    <input type="text" class="link-styles" value="" placeholder="${t('link_styles_placeholder', 'e.g. color: blue; font-weight: bold;')}" />
                </div>
            </div>
            <div class="file-link-modal-footer">
                <button class="btn-cancel" type="button">${t('cancel', 'Cancel')}</button>
                <button class="btn-insert" type="button">${t('insert', 'Insert')}</button>
            </div>
        </div>
    `

    document.body.appendChild(modal)

    // Focus on link text input
    const linkTextInput = modal.querySelector('.link-text')
    linkTextInput.focus()
    linkTextInput.select()

    // Handle close button
    modal.querySelector('.file-link-modal-close').addEventListener('click', () => {
        modal.remove()
    })

    // Handle cancel button
    modal.querySelector('.btn-cancel').addEventListener('click', () => {
        modal.remove()
    })

    // Handle insert/update button
    modal.querySelector('.btn-insert').addEventListener('click', () => {
        const linkText = modal.querySelector('.link-text').value.trim()
        const target = modal.querySelector('.link-target').value
        const extraClasses = modal.querySelector('.link-classes').value.trim()
        const extraStyles = modal.querySelector('.link-styles').value.trim()

        if (!linkText) {
            alert(t('enter_link_text', 'Please enter link text'))
            return
        }

        // Build link attributes
        const linkAttrs = {
            href: url,
            target: target
        }

        if (extraClasses) linkAttrs.class = extraClasses
        if (extraStyles) linkAttrs.style = extraStyles

        // Insert new link
        editor
            .chain()
            .focus()
            .insertContent({
                type: 'text',
                text: linkText,
                marks: [
                    {
                        type: 'link',
                        attrs: linkAttrs
                    }
                ]
            })
            .run()

        modal.remove()
    })

    // Handle Enter key
    linkTextInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            modal.querySelector('.btn-insert').click()
        }
    })

    // Handle Escape key
    modal.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.remove()
        }
    })

    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove()
        }
    })
}

/**
 * Show image edit modal
 * @param {object} editor - TipTap editor instance
 * @param {HTMLElement} img - Image element
 */
function showImageEditModal(editor, img) {
    const modal = document.createElement('div')
    modal.className = 'file-link-modal-overlay'

    const src = img.getAttribute('src')
    const alt = img.getAttribute('alt') || ''
    const title = img.getAttribute('title') || ''
    const width = img.getAttribute('width') || '100%'
    const dataAlign = img.getAttribute('data-align') || ''

    modal.innerHTML = `
        <div class="file-link-modal">
            <div class="file-link-modal-header">
                <h3>${t('edit_image', 'Edit Image')}</h3>
                <button class="file-link-modal-close" type="button">&times;</button>
            </div>
            <div class="file-link-modal-body">
                <div class="form-group">
                    <label>${t('image', 'Image')}:</label>
                    <input type="text" class="image-src" value="${src}" readonly />
                </div>
                <div class="form-group">
                    <label>${t('alt_text', 'Alt Text')}:</label>
                    <input type="text" class="image-alt" value="${alt}" placeholder="${t('alt_text_placeholder', 'Description of the image')}" />
                </div>
                <div class="form-group">
                    <label>${t('title', 'Title')}:</label>
                    <input type="text" class="image-title" value="${title}" placeholder="${t('title_placeholder', 'Tooltip text on hover')}" />
                </div>
                <div class="form-group">
                    <label>${t('width', 'Width')}:</label>
                    <select class="image-width">
                        <option value="25%" ${width === '25%' ? 'selected' : ''}>25%</option>
                        <option value="50%" ${width === '50%' ? 'selected' : ''}>50%</option>
                        <option value="75%" ${width === '75%' ? 'selected' : ''}>75%</option>
                        <option value="100%" ${width === '100%' ? 'selected' : ''}>100%</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>${t('alignment', 'Alignment')}:</label>
                    <select class="image-align">
                        <option value="" ${!dataAlign ? 'selected' : ''}>${t('alignment_none', 'None')}</option>
                        <option value="left" ${dataAlign === 'left' ? 'selected' : ''}>${t('alignment_left', 'Left')}</option>
                        <option value="center" ${dataAlign === 'center' ? 'selected' : ''}>${t('alignment_center', 'Center')}</option>
                        <option value="right" ${dataAlign === 'right' ? 'selected' : ''}>${t('alignment_right', 'Right')}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>${t('extra_css_classes', 'Extra CSS Classes')}:</label>
                    <input type="text" class="image-classes" value="" placeholder="${t('extra_css_classes_placeholder', 'e.g. rounded shadow-lg')}" />
                </div>
                <div class="form-group">
                    <label>${t('extra_styles', 'Extra Styles')}:</label>
                    <input type="text" class="image-styles" value="" placeholder="${t('extra_styles_placeholder', 'e.g. border: 1px solid red;')}" />
                </div>
            </div>
            <div class="file-link-modal-footer">
                <button class="btn-cancel" type="button">${t('cancel', 'Cancel')}</button>
                <button class="btn-insert" type="button">${t('update', 'Update')}</button>
            </div>
        </div>
    `

    document.body.appendChild(modal)

    const altInput = modal.querySelector('.image-alt')
    altInput.focus()
    altInput.select()

    // Handle close button
    modal.querySelector('.file-link-modal-close').addEventListener('click', () => {
        modal.remove()
    })

    // Handle cancel button
    modal.querySelector('.btn-cancel').addEventListener('click', () => {
        modal.remove()
    })

    // Handle update button
    modal.querySelector('.btn-insert').addEventListener('click', () => {
        const newAlt = modal.querySelector('.image-alt').value.trim()
        const newTitle = modal.querySelector('.image-title').value.trim()
        const newWidth = modal.querySelector('.image-width').value
        const newAlign = modal.querySelector('.image-align').value
        const extraClasses = modal.querySelector('.image-classes').value.trim()
        const extraStyles = modal.querySelector('.image-styles').value.trim()

        const editorElement = img.closest('ui-editor')
        if (editorElement?.editor) {
            const pos = editorElement.editor.view.posAtDOM(img, 0)

            // Build class string
            let classString = 'tiptap-image'
            if (newAlign) classString += ` align-${newAlign}`
            if (extraClasses) classString += ` ${extraClasses}`

            // Calculate margin styles for alignment
            let marginStyle = ''
            if (newAlign === 'left') {
                marginStyle = 'margin-left: 0; margin-right: auto;'
            } else if (newAlign === 'center') {
                marginStyle = 'margin-left: auto; margin-right: auto; display: block;'
            } else if (newAlign === 'right') {
                marginStyle = 'margin-left: auto; margin-right: 0;'
            }

            // Build style string
            const widthStyle = `width: ${newWidth};`
            let combinedStyle = marginStyle ? `${widthStyle} ${marginStyle}`.trim() : widthStyle
            if (extraStyles) combinedStyle += ` ${extraStyles}`

            editorElement.editor
                .chain()
                .focus()
                .setNodeSelection(pos)
                .updateAttributes('image', {
                    alt: newAlt,
                    title: newTitle,
                    width: newWidth,
                    style: combinedStyle.trim(),
                    'data-align': newAlign,
                    class: classString.trim()
                })
                .run()
        }

        modal.remove()
    })

    // Handle Enter key
    modal.querySelector('.image-title').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            modal.querySelector('.btn-insert').click()
        }
    })

    // Handle Escape key
    modal.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.remove()
        }
    })

    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove()
        }
    })
}

/**
 * Enable link editing functionality
 * Opens modal when clicking on links in the editor
 */
function enableLinkEditing() {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('.ProseMirror a')
        if (!link) return

        e.preventDefault()

        const editorElement = link.closest('ui-editor')
        if (!editorElement?.editor) return

        const href = link.getAttribute('href')
        const target = link.getAttribute('target') || '_blank'
        const text = link.textContent

        showFileLinkModal(editorElement.editor, href, { text, target })
    })
}

/**
 * Enable image resize functionality
 * Creates a resize menu when clicking on images in the editor
 */
function enableImageResize() {
    // Double click to edit image
    document.addEventListener('dblclick', (e) => {
        const img = e.target.closest('.ProseMirror img')
        if (!img) return

        e.preventDefault()

        const editorElement = img.closest('ui-editor')
        if (!editorElement?.editor) return

        showImageEditModal(editorElement.editor, img)
    })

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
                        <button class="apply-custom">${t('apply', 'Apply')}</button>
                    </div>
                </div>
                <div class="align-section">
                    <button data-align="left" title="${t('align_left_title', 'Align left')}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="15" y2="12"></line>
                            <line x1="3" y1="18" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <button data-align="center" title="${t('align_center_title', 'Align center')}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="6" y1="12" x2="18" y2="12"></line>
                            <line x1="5" y1="18" x2="19" y2="18"></line>
                        </svg>
                    </button>
                    <button data-align="right" title="${t('align_right_title', 'Align right')}">
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
            enableLinkEditing()
        })
    } else {
        setupImageButtonListener()
        enableImageResize()
        enableLinkEditing()
    }
}

/**
 * Setup image and file link button listeners
 */
function setupImageButtonListener() {

    // Event listener for image button (works for all Flux editors)
    document.addEventListener('click', (e) => {
        const imageButton = e.target.closest('[data-editor="image"]')
        if (imageButton) {
            e.preventDefault()
            e.stopPropagation()

            const editorElement = imageButton.closest('ui-editor')
            if (!editorElement?.editor) {
                return
            }

            insertImageFromFilemanager(editorElement.editor)
            return
        }

        // Event listener for file link button
        const fileLinkButton = e.target.closest('[data-editor="file-link"]')
        if (fileLinkButton) {
            e.preventDefault()
            e.stopPropagation()

            const editorElement = fileLinkButton.closest('ui-editor')
            if (!editorElement?.editor) {
                return
            }

            insertFileLinkFromFilemanager(editorElement.editor)
            return
        }

        const checklistButton = e.target.closest('[data-filemanager-checklist]')
        if (checklistButton) {
            e.preventDefault()
            e.stopPropagation()

            const checklistUrl = checklistButton.getAttribute('data-filemanager-checklist')
            if (checklistUrl) {
                window.open(checklistUrl, '_blank', 'noopener,noreferrer')
            }
        }
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
