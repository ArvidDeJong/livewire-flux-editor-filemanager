---
name: livewire-flux-editor-filemanager-development
description: Work with darvis/livewire-flux-editor-filemanager. Use it to put a Flux editor with Laravel Filemanager images and file links in a Livewire form, configure or extend the editor, test views that render it, and debug buttons that do nothing.
---

# darvis/livewire-flux-editor-filemanager development

## When to use this skill

Use this skill when you add a rich text editor with images or file links to a form in an application that has `darvis/livewire-flux-editor-filemanager` installed, when the toolbar buttons or drag and drop do nothing, when a test renders the editor, or when you change how images and links are stored.

## What each action does

| Action | Handled by | Result in the content |
| --- | --- | --- |
| Image button (`data-editor="image"`) | Popup to Laravel Filemanager, `?type=Images`, `window.SetUrl` callback | `<img src class="tiptap-image" width="400px">` per selected image |
| File link button (`data-editor="file-link"`) | Popup with `?type=Files`, then the link modal | `<a href target rel="noopener noreferrer nofollow" class style>text</a>` |
| Single click on an image | Resize menu: presets from `resize_presets`, custom `%`, align | `width`, `style` (width, margins, `display: block` for center), `class` (`align-*`), `data-align` |
| Double click on an image | Edit modal: alt, title, width, alignment, extra classes, extra styles | Same, plus `alt` and `title`; extra classes and styles are kept |
| Click on a link | Link modal in edit mode, `extendMarkRange('link')` | The whole link replaced with new text and attributes |
| Drop or paste image files | `createImageDropPastePlugin()` on the Image extension | Base64 `<img>` or, with `drag_drop.method = upload`, the uploaded URL |

All of it is event delegation on `document`, keyed on the closest `ui-editor` element and its `editor` property, so editors rendered later by Livewire work too.

## The app.js contract

`resources/js/app.js` must import the package's JavaScript and CSS from `vendor/`, register the Image and Link extensions on the `flux:editor` event with `e.detail.registerExtension()`, and call `initLaravelFilemanager()` once. The Image extension is extended with `class`, `style` and `data-align` attributes, `addNodeView()` returning null, `resize: false`, `inline: true`, `allowBase64: true`, and `addProseMirrorPlugins()` returning the drop and paste plugin. The Link extension gets `target`, `class` and `style`, `openOnClick: false` and the `rel` attribute.

The package is read through one namespace import, `import * as fluxFilemanager from '…/laravel-filemanager.js'`, and the plugin factory is called as `fluxFilemanager.createImageDropPastePlugin?.()`. That is what keeps a `vendor/` copy that is older than the setup block from taking the whole file down: a named import of an absent export is a module error, and then no button works at all. Don't tidy it into named imports.

`php artisan flux-filemanager:install` writes exactly this block between `// flux-filemanager:start` and `// flux-filemanager:end`, and `vendor/darvis/livewire-flux-editor-filemanager/examples/app.js` is the complete file. When something is missing, compare with that file rather than writing the registration from memory.

## Configuration

`config/flux-filemanager.php`. The component renders these as JSON for the JavaScript, so changing the config changes the editor without a rebuild.

| Key | Default | Notes |
| --- | --- | --- |
| `url` | `/filemanager` | Must match `url_prefix` in `config/lfm.php` |
| `demo_routes` | `false` | Env `FLUX_FILEMANAGER_DEMO_ROUTES`. Unauthenticated demo and checklist pages |
| `popup.width`, `popup.height` | 900, 600 | |
| `resize_presets` | 25%, 50%, 75%, 100% | Resize menu and edit modal |
| `custom_width.min`, `.max` | 1, 100 | |
| `messages.popup_blocked`, `.filemanager_not_found` | English | |
| `drag_drop.method` | `base64` | Or `upload` to `drag_drop.upload_url` |
| `drag_drop.max_file_size`, `.allowed_types` | 5 MB, jpeg/png/gif/webp/svg | Refused with a message |

Translations: group `flux-filemanager::filemanager`, languages `en`, `nl`, `de`, publish with `--tag=flux-filemanager-lang`. They reach the JavaScript through the same JSON.

## Rendering the content

The editor stores HTML with classes, inline styles and possibly base64 images. Render it unescaped in a `prose` container for content written by trusted users. Sanitise it first when the editors aren't trusted; the package doesn't. Style `.tiptap-image` and `.align-left`, `.align-center`, `.align-right` in the front-end CSS; the package's stylesheet only covers the editor.

## Testing

```php
use Illuminate\Support\ViewErrorBag;

$this->app['view']->share('errors', new ViewErrorBag);

$this->blade('<x-flux-filemanager-editor wire:model="content" />')
    ->assertSee('data-editor="image"', false)
    ->assertSee('data-flux-filemanager-config', false);
```

Flux's views read `$errors`, which only the web middleware shares. A Livewire component that uses the editor tests like any other: `Livewire::test(PageForm::class)->set('content', '<p>Hi</p>')->call('save')`. The editor's JavaScript is not exercised in PHP tests; use the demo page in a browser for that.

## Diagnosing

`php artisan flux-filemanager:check` is the first thing to run when anything is off. It reports, with a fix for each: Laravel Filemanager present, `config/lfm.php` published, the configured URL registered as a route, the file manager behind `auth`, `public/storage` linked, the four TipTap npm packages present, `app.js` calling `initLaravelFilemanager()` through the namespace import, and the build present and newer than the package's JavaScript. It exits non-zero on a failure, works without the demo routes, and the checklist page runs the same list through `Darvis\FluxFilemanager\Support\InstallationCheck`. Don't write a new check somewhere else; add it there.

## Pitfalls

- Buttons do nothing: `app.js` lacks the setup block or `initLaravelFilemanager()`, or the build is stale. The checklist page at `/darvis/filemanager-checklist` (with `FLUX_FILEMANAGER_DEMO_ROUTES=true`) shows what's missing.
- All buttons dead at once, right after updating the package: the running Vite dev server is serving the old package JavaScript, because `vendor/` is in `server.watch.ignored`. The console shows the failing import. Restart `npm run dev`; a `npm run build` is unaffected. Check the console before you change any code.
- Images insert but don't load: `APP_URL` doesn't match the host in the browser. Laravel Filemanager builds absolute URLs from it.
- The popup shows a login page or 404: `use_package_routes` and `middlewares` in `config/lfm.php`.
- Drag and drop ignored: the setup block predates 1.2.0 and lacks `addProseMirrorPlugins()`.
- Never add a second Image extension or a node view for images; Flux already has one.
- Never enable `demo_routes` in production.
