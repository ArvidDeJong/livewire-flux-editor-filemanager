---
title: Configuration
nav_order: 3
description: Every option in config/flux-filemanager.php, the component attributes, and how to publish the views and translations.
---

# Configuration

Publish the config file if the installer didn't:

```bash
php artisan vendor:publish --tag=flux-filemanager-config
```

## Options

| Key | Default | What it does |
| --- | --- | --- |
| `url` | `/filemanager` | Where Laravel Filemanager is served. Must match `url_prefix` in `config/lfm.php`, or your own route |
| `checklist_url` | `/darvis/filemanager-checklist` | The page the checklist toolbar button opens. Env `FLUX_FILEMANAGER_CHECKLIST_URL` |
| `demo_routes` | `false` | Registers the demo and checklist pages. Env `FLUX_FILEMANAGER_DEMO_ROUTES`. They have no auth, keep this off in production |
| `popup.width`, `popup.height` | `900`, `600` | Size of the file manager popup |
| `resize_presets` | `25%`, `50%`, `75%`, `100%` | The buttons in the resize menu and the options in the edit modal |
| `custom_width.min`, `custom_width.max` | `1`, `100` | Limits of the custom percentage in the resize menu |
| `messages.popup_blocked` | English text | Shown when the browser blocks the popup |
| `messages.filemanager_not_found` | English text | Shown when the popup can't be opened at all |
| `messages.no_images_selected` | English text | Reserved for host apps that build on the JavaScript |
| `drag_drop.method` | `base64` | `base64` embeds dropped and pasted images in the HTML, `upload` posts them to `drag_drop.upload_url` |
| `drag_drop.upload_url` | `/filemanager/upload` | The upload endpoint for the `upload` method |
| `drag_drop.max_file_size` | `5242880` | Bytes. Larger files are refused with a message |
| `drag_drop.allowed_types` | jpeg, png, gif, webp, svg | MIME types that may be dropped or pasted |

The component renders `url`, `popup`, `resize_presets`, `custom_width`, the two messages and the translations as a JSON script tag, and the drag and drop settings as data attributes on the editor. The JavaScript reads them from there, so a change in the config is a change in the editor without touching `app.js`. A host app can set `window.fluxFilemanagerConfig` before the editor loads to override the JSON.

## The component

```blade
<x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" id="content" />
```

| Attribute | Default | What it does |
| --- | --- | --- |
| `toolbar` | `default` | `default`: headings, marks, lists, quote, image, link, file link, checklist, align. `minimal`: bold, italic, link. `full`: default plus underline and code. `false`: render your own toolbar in the slot |
| `rows` | `12` | Height of the editor, passed to `<flux:editor>` |
| `id` | none | Passed to `<flux:editor>` |

Every other attribute goes to `<flux:editor>`, so `wire:model`, `wire:model.blur` and `class` work as they do on the Flux component.

A custom toolbar:

```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.separator />
        @include('flux-filemanager::flux.editor.image')
        @include('flux-filemanager::flux.editor.file-link')
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

The two package buttons are the views `flux-filemanager::flux.editor.image` and `flux-filemanager::flux.editor.file-link`. What makes them work is the `data-editor="image"` and `data-editor="file-link"` attribute on a `<flux:editor.button>`, so you can also use your own icon.

## Views and translations

```bash
php artisan vendor:publish --tag=flux-filemanager-views
php artisan vendor:publish --tag=flux-filemanager-lang
```

Published views live in `resources/views/vendor/flux-filemanager` and no longer receive updates from the package, so publish them only when you change them. Translations are covered in [Localization](localization.md).

## The JavaScript

`resources/js/laravel-filemanager.js` exports two functions:

- `initLaravelFilemanager()`: wires the toolbar buttons, the resize menu, the edit modal and link editing for every Flux editor on the page, now and later. Call it once.
- `createImageDropPastePlugin()`: the ProseMirror plugin that handles dropped and pasted image files. Add it to the Image extension in `addProseMirrorPlugins()`; `examples/app.js` shows where.

The two stylesheets, `resources/css/tiptap-image.css` and `resources/css/file-link-modal.css`, style the images in the editor, the resize menu and the modals. Override the classes in your own CSS after importing them.
