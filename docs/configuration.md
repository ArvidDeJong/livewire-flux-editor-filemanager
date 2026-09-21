---
title: "Configuration"
nav_order: 4
description: "Every key in config/flux-filemanager.php with its default, the two env variables, the attributes of the editor component, custom toolbars and the publish tags."
---

# Configuration

## Publish the config file

The installer publishes `config/flux-filemanager.php`. If you skipped that step:

```bash
php artisan vendor:publish --tag=flux-filemanager-config
```

Without a published file the package uses the defaults below. After a change, run `php artisan config:clear` if your application caches its config.

## Every option and its default

| Key | Default | What it does |
| --- | --- | --- |
| `checklist_url` | `/darvis/filemanager-checklist` | The page the checklist toolbar button opens in a new tab. Env variable `FLUX_FILEMANAGER_CHECKLIST_URL` |
| `custom_width.min`, `custom_width.max` | `1`, `100` | Limits of the custom percentage in the resize menu |
| `demo_routes` | `false` | Registers the demo page and the checklist page. Env variable `FLUX_FILEMANAGER_DEMO_ROUTES`. The pages have no authentication; keep this off in production |
| `drag_drop.method` | `base64` | `base64` embeds dropped and pasted images in the HTML. `upload` posts them to `drag_drop.upload_url` |
| `drag_drop.upload_url` | `/filemanager/upload` | The upload endpoint for the `upload` method. This is Laravel Filemanager's upload route |
| `drag_drop.max_file_size` | `5242880` (5 MB) | Bytes. A larger dropped or pasted image is refused. Checked in the browser only |
| `drag_drop.allowed_types` | `image/jpeg`, `image/png`, `image/gif`, `image/webp`, `image/svg+xml` | MIME types that may be dropped or pasted. Checked in the browser only |
| `messages.popup_blocked` | `Popup was blocked by your browser. Please allow popups for this site.` | Alert shown when the browser blocks the file manager popup |
| `messages.filemanager_not_found` | `Laravel Filemanager could not be loaded. Please check your installation.` | Alert shown when opening the popup throws an error |
| `messages.no_images_selected` | `No images were selected.` | Not used by the package. It is not passed to the JavaScript |
| `popup.width`, `popup.height` | `900`, `600` | Size of the file manager popup in pixels |
| `resize_presets` | `25%`, `50%`, `75%`, `100%` | The buttons in the resize menu and the width options in the edit modal |
| `url` | `/filemanager` | Where the editor opens Laravel Filemanager. Must match `url_prefix` in `config/lfm.php`, or the URL of your own route |

The `drag_drop` limits only apply to images dropped on or pasted into the editor. Files uploaded inside the file manager popup are limited by `config/lfm.php`. See [Uploads and security](uploads-and-security.md).

The two `messages` come from the config, not from the language files. They stay English in every locale until you change them here.

### The checklist button

The `default` and `full` toolbars contain a checklist button that opens `checklist_url`. That page only exists while `demo_routes` is on, so with the defaults the button opens a 404 page. For a production form, either set `checklist_url` to a page of your own or use a custom toolbar without the button.

### How the settings reach the JavaScript

The component renders `url`, `popup`, `resize_presets`, `custom_width`, the two messages and the translations as a `<script type="application/json" data-flux-filemanager-config>` tag, once per page, and the `drag_drop` settings as data attributes on the editor element. A change in the config needs no rebuild of `app.js`.

If your application defines `window.fluxFilemanagerConfig` before the first button is used, the JavaScript reads that object instead of the JSON tag, as a whole. Keys you leave out of it fall back to the JavaScript's built-in English defaults.

## The component attributes

`resources/views/livewire/page-form.blade.php`:

```blade
<x-flux-filemanager-editor wire:model="content" toolbar="full" id="content" />
```

| Attribute | Default | What it does |
| --- | --- | --- |
| `toolbar` | `default` | `default`: heading, bold, italic, strike, bullet list, ordered list, blockquote, image, link, file link, checklist, align. `minimal`: bold, italic, link. `full`: the default plus underline and code. `:toolbar="false"`: you render your own toolbar in the slot |
| `id` | none | Passed to `<flux:editor>` |
| `rows` | `12` | Rendered as a `rows` attribute on the editor element. Flux's editor has no `rows` prop (checked against Flux Pro 2.20), so it does not change the height |

Every other attribute goes to `<flux:editor>`. `wire:model`, `wire:model.blur` and `class` work as they do on the Flux component.

To change the height, use the class the [Flux editor documentation](https://fluxui.dev/components/editor) gives for it:

```blade
<x-flux-filemanager-editor wire:model="content" class="**:data-[slot=content]:min-h-[300px]!" />
```

## A custom toolbar

`resources/views/livewire/page-form.blade.php`:

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

With `:toolbar="false"` the component prints your slot instead of a preset. The two package buttons are the views `flux-filemanager::flux.editor.image` and `flux-filemanager::flux.editor.file-link`. What makes a button work is the attribute `data-editor="image"` or `data-editor="file-link"` on a `<flux:editor.button>`, so you can also write your own button with your own icon.

## Publish the views or the translations

```bash
php artisan vendor:publish --tag=flux-filemanager-views
php artisan vendor:publish --tag=flux-filemanager-lang
```

Published views live in `resources/views/vendor/flux-filemanager`. They no longer receive updates from the package, so publish them only when you change them. Translations are covered in [Localization](localization.md).

## The JavaScript and the stylesheets

`resources/js/laravel-filemanager.js` exports two functions:

- `initLaravelFilemanager()`: connects the toolbar buttons, the resize menu, the edit modal and link editing for every Flux editor on the page, including editors that Livewire renders later. Call it once.
- `createImageDropPastePlugin()`: the ProseMirror plugin that handles dropped and pasted image files. The setup block adds it to the Image extension in `addProseMirrorPlugins()`.

`initLaravelFilemanagerForAllEditors()` still exists as a deprecated alias of `initLaravelFilemanager()`.

The two stylesheets, `resources/css/tiptap-image.css` and `resources/css/file-link-modal.css`, style the images in the editor, the resize menu and the modals. Override the classes in your own CSS after importing them.
