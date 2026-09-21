## darvis/livewire-flux-editor-filemanager

Laravel Filemanager inside the Flux Pro editor: toolbar buttons that open the file manager to insert images and file links, a resize and align menu and an edit modal on images, link editing, and drag and drop or paste of image files.

- Use `<x-flux-filemanager-editor wire:model="content" />` where you would use `<flux:editor>`. Attributes: `toolbar` (`default`, `minimal`, `full`, or `false` with your own `<flux:editor.toolbar>` in the slot) and `id`; everything else is passed to `<flux:editor>`. `rows` is only printed as an HTML attribute and does not change the height; set the height with a class such as `**:data-[slot=content]:min-h-[300px]!`.
- The buttons only work when `resources/js/app.js` registers the extensions and calls `initLaravelFilemanager()`. `php artisan flux-filemanager:install` writes that block; `examples/app.js` in the package shows the complete file. Never give the Image extension its own node view or `resize: true`: Flux's editor already renders images with one, and two node views break selection.
- Import the package's JavaScript as a namespace (`import * as fluxFilemanager from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'`) and call the plugin factory with `?.()`. Never convert it to named imports: one missing export is a module error, and that stops all of `app.js`, so every toolbar button dies at once.
- Every button dead at the same time is an `app.js` problem, not a component problem: read the browser console first. Right after a package update, restart `npm run dev` before looking further, because Vite does not watch `vendor/` and keeps serving the JavaScript it read at startup.
- Run `php artisan flux-filemanager:check` before you start reading code when something doesn't work. It checks the config, the routes, the authentication on the file manager, the storage link, the npm packages, `app.js` and the build, and prints what to do. It needs no demo routes and exits non-zero when something is broken.
- The content is HTML with `<img>` tags (classes `tiptap-image` and `align-*`, inline width and margins, `data-align`) and `<a>` tags with `target` and `rel="noopener noreferrer nofollow"`. The package does not sanitise it; render it unescaped only for trusted editors.
- Settings live in `config/flux-filemanager.php` (`url`, `popup`, `resize_presets`, `custom_width`, `messages`, `drag_drop`). The component renders them and the translations as JSON for the JavaScript; never hardcode the file manager URL or texts in `app.js`.
- Dropped and pasted images are base64 by default. Set `drag_drop.method` to `upload` to post them to Laravel Filemanager; `max_file_size` and `allowed_types` apply to both, but they are checked in the browser only. On the server only `config/lfm.php` counts (`valid_mime`, `should_validate_size`), and its default image types do not include WebP or SVG.
- The demo pages `/darvis/editor-demo` and `/darvis/filemanager-checklist` exist only when `FLUX_FILEMANAGER_DEMO_ROUTES=true`. They have no auth; keep them off in production.
- Protect the file manager itself with `middlewares` in `config/lfm.php`; the package adds no authentication and the editor only opens `/filemanager`. `auth` admits every logged-in user, so add a stricter middleware when visitors can register. Image URLs come from `APP_URL`, so it must match the host in use.
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
