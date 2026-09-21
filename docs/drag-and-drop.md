---
title: "Drag and drop"
nav_order: 7
description: "Drop image files on the Flux editor or paste a screenshot: embedded as base64 or uploaded to Laravel Filemanager, and where each limit is checked."
---

# Drag and drop and paste

Drop one or more image files on the editor, or paste an image from the clipboard (a screenshot, or an image copied from another application). Dropped images are inserted where you drop them, pasted images at the cursor. Several files keep their order.

Only files whose type starts with `image/` are handled. Anything else, such as a dropped PDF, pasted text or content dragged around inside the editor, is left to the editor itself.

## Choose between base64 and upload

`drag_drop.method` in `config/flux-filemanager.php` decides what happens to the file:

| Method | What happens | Use it when |
| --- | --- | --- |
| `base64` (default) | The image is embedded in the HTML as a data URL. Nothing is uploaded and nothing else needs to be set up. The saved content grows with every image | Small images, screenshots, a local demo |
| `upload` | The file is posted to `drag_drop.upload_url` (default `/filemanager/upload`, Laravel Filemanager's upload route) and the URL from the answer is inserted. The image then lives in your storage, like the ones picked with the image button | Production, and whenever editors drop photos |

To switch, publish the config file and change one line in `config/flux-filemanager.php`:

```php
'drag_drop' => [
    'method' => 'upload',
    // ...
],
```

Dropped and pasted images get the class `tiptap-image` and can be resized and edited like any other image; see [Image editing](image-editing.md).

## What the upload method needs

- The editor must be logged in with a user that passes the `middlewares` in `config/lfm.php`.
- The page needs a `<meta name="csrf-token">` tag with the CSRF token, as the [Laravel CSRF documentation](https://laravel.com/docs/csrf#csrf-x-csrf-token) shows. The package reads the token from that tag and sends it in the `X-CSRF-TOKEN` header. Without the tag Laravel answers 419 and the upload fails.
- The file type must be allowed in **both** configs. Laravel Filemanager 2.15 accepts `image/jpeg`, `image/pjpeg`, `image/png` and `image/gif` for images by default, so a WebP or SVG passes this package's check and is then refused by Laravel Filemanager. Add the types to `folder_categories.image.valid_mime` in `config/lfm.php`, or remove them from `drag_drop.allowed_types`.

## The limits are checked in the browser

Both methods first compare the file with `drag_drop.max_file_size` (5 MB by default) and `drag_drop.allowed_types`. A file that fails is not inserted, and an alert says why:

```text
Image too large. Maximum size is 5.0MB
File type image/bmp is not allowed
```

These two texts are fixed English strings in the JavaScript; they are not in the language files.

The check runs in JavaScript, on the type the browser reports. It is a convenience for editors, not a protection: it says nothing about what your server accepts. With the `upload` method the real limits are Laravel Filemanager's and PHP's. See [Uploads and security](uploads-and-security.md).

## The setup block in app.js

The behaviour lives in `createImageDropPastePlugin()`, a ProseMirror plugin exported by the package's JavaScript. ProseMirror is the engine under TipTap, the editor library Flux uses. The setup block that the installer writes to `resources/js/app.js` adds the plugin to the Image extension:

```javascript
import * as fluxFilemanager from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'

const FluxSafeImage = Image.extend({
    addProseMirrorPlugins() {
        const dropPaste = fluxFilemanager.createImageDropPastePlugin?.()

        return [...(this.parent?.() ?? []), ...(dropPaste ? [dropPaste] : [])]
    },
})
```

This is a fragment; [examples/app.js](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/blob/main/examples/app.js) is the complete file.

The namespace import and the optional call `?.()` are deliberate. When the copy under `vendor/` is older than your setup block, the plugin is absent and the rest of the editor keeps working. A named import of an export that is not there is a module error, and one of those stops all of `app.js`.

The plugin reads the method, the limits and the upload URL from data attributes that the component renders on the editor element, so a config change needs no rebuild.

## When it does not work

See [Troubleshooting](troubleshooting.md#dropping-or-pasting-an-image-does-nothing).
