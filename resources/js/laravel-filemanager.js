/**
 * Laravel Filemanager integration for Flux Editor
 * Generic implementation that works for all Flux editors
 */

import { Plugin, PluginKey } from '@tiptap/pm/state'
import { getDragDropConfig, processImageFile } from './drag-drop-config.js'

const DEFAULT_RESIZE_PRESETS = ['25%', '50%', '75%', '100%']
const GENERATED_CLASSES = ['tiptap-image', 'align-left', 'align-center', 'align-right']
const GENERATED_STYLES = ['width', 'margin-left', 'margin-right', 'display']

/**
 * Load the settings and translations the editor component renders as JSON.
 * A host app can set window.fluxFilemanagerConfig itself; that takes precedence.
 * @returns {object}
 */
function loadConfig() {
    if (window.fluxFilemanagerConfig) return window.fluxFilemanagerConfig

    const element = document.querySelector('script[data-flux-filemanager-config]')
    if (!element) return {}

    try {
        window.fluxFilemanagerConfig = JSON.parse(element.textContent)
    } catch (error) {
        console.warn('Flux Filemanager: could not parse the config JSON.', error)
        return {}
    }

    return window.fluxFilemanagerConfig
}

/**
 * Get configuration value with fallback
 * @param {string} key - Configuration key
 * @param {*} defaultValue - Default value if config not found
 * @returns {*} Configuration value
 */
function getConfig(key, defaultValue) {
    return loadConfig()[key] ?? defaultValue
}

function escapeAttr(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
}

function resizePresets() {
    const presets = getConfig('resize_presets', DEFAULT_RESIZE_PRESETS)
    return Array.isArray(presets) && presets.length ? presets : DEFAULT_RESIZE_PRESETS
}

function widthOptions(current) {
    const presets = resizePresets()
    const values = current && !presets.includes(current) ? [current, ...presets] : presets

    return values
        .map((value) => `<option value="${escapeAttr(value)}"${value === current ? ' selected' : ''}>${escapeAttr(value)}</option>`)
        .join('')
}

/**
 * The classes on an image that the editor did not generate itself.
 */
function extraClasses(element) {
    return (element.getAttribute('class') || '')
        .split(/\s+/)
        .filter((name) => name && !GENERATED_CLASSES.includes(name))
        .join(' ')
}

/**
 * The inline styles on an image that the editor did not generate itself.
 */
function extraStyles(element) {
    return (element.getAttribute('style') || '')
        .split(';')
        .map((declaration) => declaration.trim())
        .filter((declaration) => declaration && !GENERATED_STYLES.includes(declaration.split(':')[0].trim()))
        .join('; ')
}

/**
 * Build the class, style, width and data-align attributes of an image.
 * @param {{width?: string, align?: string, classes?: string, styles?: string}} options
 */
function imageAttributes({ width, align, classes, styles }) {
    const classList = ['tiptap-image']
    if (align) classList.push(`align-${align}`)
    if (classes) classList.push(classes)

    const styleList = []
    if (width) styleList.push(`width: ${width};`)
    if (align === 'left') styleList.push('margin-left: 0; margin-right: auto;')
    if (align === 'center') styleList.push('margin-left: auto; margin-right: auto; display: block;')
    if (align === 'right') styleList.push('margin-left: auto; margin-right: 0;')
    if (styles) styleList.push(styles.endsWith(';') ? styles : `${styles};`)

    return {
        width: width || null,
        'data-align': align || null,
        class: classList.join(' '),
        style: styleList.join(' ') || null,
    }
}

/**
 * Update the attributes of an image node without replacing it, so alt and title survive.
 */
function updateImage(img, attrs) {
    const editorElement = img.closest('ui-editor')
    if (!editorElement?.editor) return

    const pos = editorElement.editor.view.posAtDOM(img, 0)

    editorElement.editor.chain().focus().setNodeSelection(pos).updateAttributes('image', attrs).run()

    syncLivewire(editorElement)
}

/**
 * Flux syncs wire:model on input and blur; attribute updates through commands don't fire those.
 */
function syncLivewire(editorElement) {
    setTimeout(() => {
        editorElement.dispatchEvent(new Event('input', { bubbles: true }))
        editorElement.dispatchEvent(new Event('blur', { bubbles: true }))
    }, 100)
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
 * Show the link modal, to insert a new file link or edit an existing link
 * @param {object} editor - TipTap editor instance
 * @param {string} url - File URL
 * @param {object|null} existing - The link being edited: text, target, class, style
 */
function showFileLinkModal(editor, url, existing = null) {
    const modal = document.createElement('div')
    modal.className = 'file-link-modal-overlay'

    const filename = url.split('/').pop()
    const linkText = existing?.text ?? filename
    const target = existing?.target ?? '_blank'
    const targetOption = (value, label) => `<option value="${value}"${target === value ? ' selected' : ''}>${label}</option>`

    modal.innerHTML = `
        <div class="file-link-modal">
            <div class="file-link-modal-header">
                <h3>${existing ? t('edit_link', 'Edit Link') : t('insert_link', 'Insert File Link')}</h3>
                <button class="file-link-modal-close" type="button">&times;</button>
            </div>
            <div class="file-link-modal-body">
                <div class="form-group">
                    <label>${t('file', 'File')}:</label>
                    <input type="text" class="file-url" value="${escapeAttr(url)}" readonly />
                </div>
                <div class="form-group">
                    <label>${t('link_text', 'Link Text')}:</label>
                    <input type="text" class="link-text" value="${escapeAttr(linkText)}" placeholder="${t('link_text_placeholder', 'Click here to download')}" />
                </div>
                <div class="form-group">
                    <label>${t('target', 'Target')}:</label>
                    <select class="link-target">
                        ${targetOption('_blank', t('target_blank', 'New window (_blank)'))}
                        ${targetOption('_self', t('target_self', 'Same window (_self)'))}
                        ${targetOption('_parent', t('target_parent', 'Parent window (_parent)'))}
                        ${targetOption('_top', t('target_top', 'Top window (_top)'))}
                    </select>
                </div>
                <div class="form-group">
                    <label>${t('extra_css_classes', 'Extra CSS Classes')}:</label>
                    <input type="text" class="link-classes" value="${escapeAttr(existing?.class)}" placeholder="${t('link_css_classes_placeholder', 'e.g. btn btn-primary')}" />
                </div>
                <div class="form-group">
                    <label>${t('extra_styles', 'Extra Styles')}:</label>
                    <input type="text" class="link-styles" value="${escapeAttr(existing?.style)}" placeholder="${t('link_styles_placeholder', 'e.g. color: blue; font-weight: bold;')}" />
                </div>
            </div>
            <div class="file-link-modal-footer">
                <button class="btn-cancel" type="button">${t('cancel', 'Cancel')}</button>
                <button class="btn-insert" type="button">${existing ? t('update', 'Update') : t('insert', 'Insert')}</button>
            </div>
        </div>
    `

    document.body.appendChild(modal)

    const linkTextInput = modal.querySelector('.link-text')
    linkTextInput.focus()
    linkTextInput.select()

    modal.querySelector('.file-link-modal-close').addEventListener('click', () => modal.remove())
    modal.querySelector('.btn-cancel').addEventListener('click', () => modal.remove())

    modal.querySelector('.btn-insert').addEventListener('click', () => {
        const text = modal.querySelector('.link-text').value.trim()
        const classes = modal.querySelector('.link-classes').value.trim()
        const styles = modal.querySelector('.link-styles').value.trim()

        if (!text) {
            alert(t('enter_link_text', 'Please enter link text'))
            return
        }

        const attrs = {
            href: url,
            target: modal.querySelector('.link-target').value,
        }

        if (classes) attrs.class = classes
        if (styles) attrs.style = styles

        // Editing replaces the whole link the cursor is in; inserting adds one at the cursor.
        const chain = editor.chain().focus()
        if (existing) chain.extendMarkRange('link')
        chain.insertContent({ type: 'text', text, marks: [{ type: 'link', attrs }] }).run()

        modal.remove()
    })

    linkTextInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            modal.querySelector('.btn-insert').click()
        }
    })

    modal.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.remove()
        }
    })

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
                    <input type="text" class="image-src" value="${escapeAttr(src)}" readonly />
                </div>
                <div class="form-group">
                    <label>${t('alt_text', 'Alt Text')}:</label>
                    <input type="text" class="image-alt" value="${escapeAttr(alt)}" placeholder="${t('alt_text_placeholder', 'Description of the image')}" />
                </div>
                <div class="form-group">
                    <label>${t('title', 'Title')}:</label>
                    <input type="text" class="image-title" value="${escapeAttr(title)}" placeholder="${t('title_placeholder', 'Tooltip text on hover')}" />
                </div>
                <div class="form-group">
                    <label>${t('width', 'Width')}:</label>
                    <select class="image-width">
                        ${widthOptions(width)}
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
                    <input type="text" class="image-classes" value="${escapeAttr(extraClasses(img))}" placeholder="${t('extra_css_classes_placeholder', 'e.g. rounded shadow-lg')}" />
                </div>
                <div class="form-group">
                    <label>${t('extra_styles', 'Extra Styles')}:</label>
                    <input type="text" class="image-styles" value="${escapeAttr(extraStyles(img))}" placeholder="${t('extra_styles_placeholder', 'e.g. border: 1px solid red;')}" />
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
        const newClasses = modal.querySelector('.image-classes').value.trim()
        const newStyles = modal.querySelector('.image-styles').value.trim()

        updateImage(img, {
            alt: newAlt,
            title: newTitle,
            ...imageAttributes({ width: newWidth, align: newAlign, classes: newClasses, styles: newStyles }),
        })

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

        showFileLinkModal(editorElement.editor, link.getAttribute('href'), {
            text: link.textContent,
            target: link.getAttribute('target') || '_blank',
            class: link.getAttribute('class') || '',
            style: link.getAttribute('style') || '',
        })
    })
}

/**
 * Enable image editing: single click shows the resize menu, double click opens the edit modal
 */
function enableImageResize() {
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

        const menu = resizeMenu()
        const rect = img.getBoundingClientRect()
        menu.style.top = `${rect.bottom + window.scrollY + 5}px`
        menu.style.left = `${rect.left + window.scrollX}px`
        menu.classList.add('show')

        document.querySelectorAll('.ProseMirror img').forEach((other) => other.classList.remove('active-resize'))
        img.classList.add('active-resize')
    })

    // Close the menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.ProseMirror img') && !e.target.closest('.image-resize-menu')) {
            document.querySelector('.image-resize-menu')?.classList.remove('show')
        }
    })
}

/**
 * The one resize menu on the page, created on first use
 * @returns {HTMLElement}
 */
function resizeMenu() {
    let menu = document.querySelector('.image-resize-menu')
    if (menu) return menu

    const min = parseInt(getConfig('custom_width_min', 1), 10) || 1
    const max = parseInt(getConfig('custom_width_max', 100), 10) || 100

    menu = document.createElement('div')
    menu.className = 'image-resize-menu'
    menu.innerHTML = `
        <div class="resize-section">
            ${resizePresets().map((preset) => `<button type="button" data-width="${escapeAttr(preset)}">${escapeAttr(preset)}</button>`).join('')}
            <div class="custom-width-input">
                <input type="number" min="${min}" max="${max}" placeholder="%" class="width-input" />
                <button type="button" class="apply-custom">${t('apply', 'Apply')}</button>
            </div>
        </div>
        <div class="align-section">
            <button type="button" data-align="left" title="${t('align_left_title', 'Align left')}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="15" y2="12"></line>
                    <line x1="3" y1="18" x2="18" y2="18"></line>
                </svg>
            </button>
            <button type="button" data-align="center" title="${t('align_center_title', 'Align center')}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="6" y1="12" x2="18" y2="12"></line>
                    <line x1="5" y1="18" x2="19" y2="18"></line>
                </svg>
            </button>
            <button type="button" data-align="right" title="${t('align_right_title', 'Align right')}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="9" y1="12" x2="21" y2="12"></line>
                    <line x1="6" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    `
    document.body.appendChild(menu)

    const activeImage = () => document.querySelector('.ProseMirror img.active-resize')
    const close = () => menu.classList.remove('show')
    const resize = (img, width) => updateImage(img, imageAttributes({
        width,
        align: img.getAttribute('data-align') || '',
        classes: extraClasses(img),
        styles: extraStyles(img),
    }))

    menu.addEventListener('click', (e) => {
        const button = e.target.closest('button[data-width], button[data-align]')
        const img = activeImage()
        if (!button || !img) return

        if (button.dataset.width) {
            resize(img, button.dataset.width)
        } else {
            updateImage(img, imageAttributes({
                width: img.getAttribute('width') || '',
                align: button.dataset.align,
                classes: extraClasses(img),
                styles: extraStyles(img),
            }))
        }

        close()
    })

    const widthInput = menu.querySelector('.width-input')
    const applyCustom = menu.querySelector('.apply-custom')

    applyCustom.addEventListener('click', () => {
        const value = parseInt(widthInput.value, 10)
        const img = activeImage()
        if (!img || Number.isNaN(value) || value < min || value > max) return

        resize(img, `${value}%`)
        widthInput.value = ''
        close()
    })

    widthInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') applyCustom.click()
    })

    return menu
}

/**
 * ProseMirror plugin that inserts image files dropped on or pasted into the editor.
 * Add it to the Image extension:
 *   addProseMirrorPlugins() { return [createImageDropPastePlugin()] }
 * The method (base64 or upload), size limit and allowed types come from the
 * data attributes the editor component renders from config/flux-filemanager.php.
 * @returns {Plugin}
 */
export function createImageDropPastePlugin() {
    return new Plugin({
        key: new PluginKey('fluxFilemanagerImageDropPaste'),
        props: {
            handleDrop(view, event, slice, moved) {
                if (moved) return false

                const files = imageFiles(event.dataTransfer?.files)
                if (!files.length || !view.dom.closest('ui-editor')?.editor) return false

                event.preventDefault()

                const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY })
                insertImageFiles(view, files, coordinates?.pos ?? view.state.selection.from)

                return true
            },
            handlePaste(view, event) {
                const files = imageFiles(event.clipboardData?.files)
                if (!files.length || !view.dom.closest('ui-editor')?.editor) return false

                event.preventDefault()
                insertImageFiles(view, files, view.state.selection.from)

                return true
            },
        },
    })
}

function imageFiles(fileList) {
    return Array.from(fileList ?? []).filter((file) => file.type.startsWith('image/'))
}

/**
 * Files are processed one by one, so they end up in the order they were dropped
 */
async function insertImageFiles(view, files, position) {
    const editorElement = view.dom.closest('ui-editor')
    const editor = editorElement?.editor
    if (!editor) return

    const config = getDragDropConfig(editorElement)
    let pos = position

    for (const file of files) {
        try {
            const src = await processImageFile(file, config)
            if (!src) continue

            editor.chain().focus().insertContentAt(pos, { type: 'image', attrs: { src, class: 'tiptap-image' } }).run()
            pos = editor.state.selection.to
        } catch (error) {
            console.warn('Flux Filemanager: image not inserted.', error)
            alert(error.message)
        }
    }

    syncLivewire(editorElement)
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
 * @deprecated Use initLaravelFilemanager(). Kept for 1.x compatibility.
 */
export function initLaravelFilemanagerForAllEditors() {
    initLaravelFilemanager()
}
