---
title: Drag and drop
nav_order: 7
description: Drop image files on the Flux editor or paste screenshots, embedded as base64 or uploaded to Laravel Filemanager, with size and type limits from the config.
---

# Drag and drop and paste

Drop one or more image files on the editor, or paste an image from the clipboard (a screenshot, or an image copied from another application), and they are inserted where you drop them or at the cursor. Several files keep their order.

Dragging content around inside the editor, and pasting HTML or text, is left to the editor itself.

## Base64 or upload

`drag_drop.method` in `config/flux-filemanager.php` decides what happens to the file:

- `base64` (default): the image is embedded in the HTML as a data URL. Nothing is uploaded, it works without further setup, and the content grows with the image. Fine for small images, screenshots and demos.
- `upload`: the file is posted to `drag_drop.upload_url` (default `/filemanager/upload`) with the CSRF token, as Laravel Filemanager's own uploader does, and the returned URL is inserted. The image then lives in your storage like the ones picked with the image button. Use this in production when editors drop large photos.

The limits apply to both methods: files over `drag_drop.max_file_size` (5 MB by default) or with a MIME type outside `drag_drop.allowed_types` are refused with a message.

Dropped and pasted images get the class `tiptap-image` and can be resized and edited like any other image; see [Image editing](image-editing.md).

## Setup

The behaviour lives in `createImageDropPastePlugin()`, a ProseMirror plugin exported by the package's JavaScript. The setup block the installer writes to `app.js` adds it to the Image extension:

```javascript
import * as fluxFilemanager from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'

const FluxSafeImage = Image.extend({
    addProseMirrorPlugins() {
        const dropPaste = fluxFilemanager.createImageDropPastePlugin?.()

        return [...(this.parent?.() ?? []), ...(dropPaste ? [dropPaste] : [])]
    },
})
```

The namespace import and the optional call are deliberate: when the copy under `vendor/` is older than your setup block, the plugin is simply absent and the editor keeps working. A named import of an export that isn't there is a module error, and one of those stops all of `app.js`, so every toolbar button dies with it.

The plugin reads the method, the limits and the upload URL from the data attributes the component renders on the editor, so the config applies without a rebuild.

## When it doesn't work

- **Nothing happens on drop.** The setup block predates 1.2.0, when the plugin was added. Run `php artisan flux-filemanager:install` again or compare your `app.js` with `examples/app.js`, then `npm run build`.
- **The console says the package JavaScript has no drop and paste plugin.** Your `app.js` is current but the copy under `vendor/` is not the one being served. Restart `npm run dev`: Vite does not watch `vendor/`.
- **A message says the file is too large or not allowed.** Raise `max_file_size` or add the type to `allowed_types`.
- **Upload method: the image doesn't appear.** Open the browser console. The upload endpoint must accept a POST with an `upload` file field and answer with JSON containing `url`, `link` or `path`. The `/filemanager` routes need to be reachable with the session of the logged-in editor.
- **The saved content is huge.** That's base64. Switch to `upload`.
