## darvis/livewire-flux-editor-filemanager

Laravel Filemanager inside the Flux Pro editor: toolbar buttons that open the file manager to insert images and file links, a resize and align menu and an edit modal on images, link editing, and drag and drop or paste of image files.

- Use `<x-flux-filemanager-editor wire:model="content" />` where you would use `<flux:editor>`. Attributes: `toolbar` (`default`, `minimal`, `full`, or `false` with your own `<flux:editor.toolbar>` in the slot), `rows` and `id`; everything else is passed to `<flux:editor>`.
- The buttons only work when `resources/js/app.js` registers the extensions and calls `initLaravelFilemanager()`. `php artisan flux-filemanager:install` writes that block; `examples/app.js` in the package shows the complete file. Never give the Image extension its own node view or `resize: true`: Flux's editor already renders images with one, and two node views break selection.
- The content is HTML with `<img>` tags (classes `tiptap-image` and `align-*`, inline width and margins, `data-align`) and `<a>` tags with `target` and `rel="noopener noreferrer nofollow"`. The package does not sanitise it; render it unescaped only for trusted editors.
- Settings live in `config/flux-filemanager.php` (`url`, `popup`, `resize_presets`, `custom_width`, `messages`, `drag_drop`). The component renders them and the translations as JSON for the JavaScript; never hardcode the file manager URL or texts in `app.js`.
- Dropped and pasted images are base64 by default. Set `drag_drop.method` to `upload` to post them to Laravel Filemanager; `max_file_size` and `allowed_types` apply to both.
- The demo pages `/darvis/editor-demo` and `/darvis/filemanager-checklist` exist only when `FLUX_FILEMANAGER_DEMO_ROUTES=true`. They have no auth; keep them off in production.
- Protect the file manager itself with `middlewares` in `config/lfm.php`; the editor only opens `/filemanager`. Image URLs come from `APP_URL`, so it must match the host in use.
- In tests, render the component with `$this->blade()` after sharing an empty `ViewErrorBag` as `errors`; Flux reads `$errors` and there is no web middleware there.

@verbatim
<code-snippet name="Editor in a Livewire form" lang="blade">
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" />
    <flux:error name="content" />
</flux:field>
</code-snippet>
@endverbatim
