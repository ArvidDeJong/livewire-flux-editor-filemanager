---
title: How it works
nav_order: 8
description: The technical reference for darvis/livewire-flux-editor-filemanager, the DOM contract, the Laravel Filemanager callback, the extensions and the files in the package.
---

# How it works

A reference for debugging and extending. Nothing here is needed to use the package.

## The pieces

| Piece | File | Role |
| --- | --- | --- |
| Blade component | `resources/views/components/editor.blade.php`, `src/View/Components/Editor.php` | Wraps `<flux:editor>`, renders the toolbar preset, the JSON config and the drag and drop data attributes |
| Toolbar buttons | `resources/views/flux/editor/image.blade.php`, `file-link.blade.php`, `checklist.blade.php` | `<flux:editor.button>` elements with a `data-editor` or `data-filemanager-checklist` attribute |
| JavaScript | `resources/js/laravel-filemanager.js` | Button handling, the popup, the resize menu, the modals and the drop and paste plugin |
| Drag and drop helpers | `resources/js/drag-drop-config.js` | Reads the data attributes, validates a file, converts to base64 or uploads |
| Setup block | `resources/stubs/flux-filemanager-setup.js`, `examples/app.js` | Registers the Image and Link extensions on the `flux:editor` event |
| Styles | `resources/css/tiptap-image.css`, `file-link-modal.css` | Images in the editor, the resize menu, the modals |
| Installer | `src/Console/InstallCommand.php` | Laravel Filemanager config and routes, storage, npm, config, `app.js` |
| Demo | `routes/web.php`, `src/Livewire/EditorDemo.php`, `resources/views/examples/` | Opt-in demo and checklist pages |

## The DOM contract

Everything is event delegation on `document`, so it works for editors rendered later by Livewire:

- A click on an element with `data-editor="image"` or `data-editor="file-link"` finds the closest `ui-editor` element. Flux puts the TipTap instance on it as `editor`. Without that property the click is ignored.
- A click on `.ProseMirror img` shows the resize menu, a double click opens the image modal.
- A click on `.ProseMirror a` opens the link modal in edit mode, with `preventDefault()` so the link doesn't navigate.
- A click on `[data-filemanager-checklist]` opens the checklist URL in a new tab.

Settings come from a `<script type="application/json" data-flux-filemanager-config>` tag the component renders once per page, or from `window.fluxFilemanagerConfig` if the host app sets it first. The JSON holds the file manager URL, the popup size, the resize presets, the custom width limits, the two messages and the translations. The drag and drop settings are data attributes on the `ui-editor` element, because the plugin reads them per editor.

## The Laravel Filemanager popup

`window.open()` with `?type=Images` or `?type=Files`. Laravel Filemanager calls `window.SetUrl(items)` in the opener when the user confirms; the package sets that function before opening the popup, reads `url` (or `path`, or `thumb_url`) from each item, and removes the function again. URLs that point to `/storage` or the file manager on another origin are made relative, which is what you want when `APP_URL` doesn't match the host in use.

Images are inserted with the `setImage` command, with a fallback to raw HTML and then to a link if the extension refuses. Files open the link modal.

## Changing images and links

The resize menu and the modal call `updateAttributes('image', ...)` on the selected node, so attributes they don't touch stay. The generated part of `class` and `style` is recomputed every time: `tiptap-image`, `align-*`, `width`, the alignment margins and `display: block` for center. Anything else in those attributes is treated as the editor's own and kept.

Editing a link extends the selection to the whole link mark and replaces it with new text carrying a new link mark, so text and attributes change together.

After a change through commands, the package dispatches `input` and `blur` on the `ui-editor` element, because Flux syncs `wire:model` on those events.

## The extensions

The setup block registers two extensions on every editor through Flux's `flux:editor` event and `e.detail.registerExtension()`:

- **Image**: TipTap's Image extension, extended with `class`, `style` and `data-align` attributes and the drop and paste plugin. `addNodeView()` returns null and `resize` is off, because Flux's editor already renders images with its own node view; a second one breaks selection. `inline: true` so an image can sit in a paragraph, `allowBase64: true` for dropped images.
- **Link**: TipTap's Link extension with `target`, `class` and `style` attributes, `openOnClick: false` so clicks open the modal instead of navigating, and `rel="noopener noreferrer nofollow"`.

A flag on `e.detail` stops the block from registering twice when the event fires more than once.

## Drop and paste

`createImageDropPastePlugin()` is a ProseMirror plugin with `handleDrop` and `handlePaste`. It takes over only when the event carries image files and the editor belongs to a `ui-editor`; everything else goes to the editor's defaults. Files are processed one at a time through `processImageFile()`: size and type check, then base64 or an upload, then `insertContentAt()` at the drop position or the cursor. A refused file shows a message and the rest continue.
