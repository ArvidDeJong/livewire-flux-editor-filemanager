---
name: livewire-flux-editor-filemanager-development
description: Work with darvis/livewire-flux-editor-filemanager. Use it to put a Flux editor with Laravel Filemanager images, file links, tables and an HTML source modal in a Livewire form, choose the toolbar buttons, configure or extend the editor, test views that render it, and debug buttons that do nothing.
---

# darvis/livewire-flux-editor-filemanager development

## When to use this skill

Use this skill when you add a rich text editor with images, file links or tables to a form in an application that has `darvis/livewire-flux-editor-filemanager` installed, when you choose which toolbar buttons show, when the toolbar buttons, the table menu or drag and drop do nothing, when a test renders the editor, or when you change how images, links and tables are stored.

## What each action does

| Action | Handled by | Result in the content |
| --- | --- | --- |
| Image button (`data-editor="image"`) | Popup to Laravel Filemanager, `?type=Images`, `window.SetUrl` callback | `<img src class="tiptap-image" width="400px">` per selected image |
| File link button (`data-editor="file-link"`) | Popup with `?type=Files`, then the link modal | `<a href target rel="noopener noreferrer nofollow" class style>text</a>` |
| Single click on an image | Resize menu: presets from `resize_presets`, custom `%`, align | `width`, `style` (width, margins, `display: block` for center), `class` (`align-*`), `data-align` |
| Double click on an image | Edit modal: alt, title, width, alignment, extra classes, extra styles | Same, plus `alt` and `title`; extra classes and styles are kept |
| Click on a link | Link modal in edit mode, `extendMarkRange('link')` | The whole link replaced with new text and attributes |
| HTML button (`data-editor="html-source"`) | Modal with the HTML from `editor.getHTML()`; Apply calls `setContent()` | The whole content replaced with the edited HTML; tags and attributes without an extension dropped, named in the hint under the text area beforehand |
| Horizontal rule button (`data-editor="hr"`) | Flux's own toolbar click handler (`setHorizontalRule`); no package JavaScript | `<hr>` |
| Code block button (`data-editor="code-block"`) | Flux's own toolbar click handler (`toggleCodeBlock`), which also sets the pressed state; no package JavaScript | `<pre><code>…</code></pre>` |
| Clear formatting button (`data-editor="clear-format"`) | Package JavaScript: `clearNodes().unsetAllMarks()` on the selection | Marks removed, blocks turned into paragraphs |
| Table menu (`data-editor="table-menu"` on the trigger; `table`, `table-add-row-before`, `table-add-row-after`, `table-delete-row`, `table-add-column-before`, `table-add-column-after`, `table-delete-column`, `table-toggle-header-row`, `table-toggle-header-column`, `table-merge-or-split` and `table-delete` on the items) | Flux's own toolbar click handler (`insertTable({ rows: 3, cols: 3, withHeaderRow: true })`, `addRowAfter()`, `deleteColumn()`, `toggleHeaderRow()`, `mergeOrSplit()`, `deleteTable()` and so on); the package JavaScript only disables the trigger when the editor has no `table` node | `<table>` with `<tbody>`, `<tr>`, `<th>` and `<td>` (`colspan`, `rowspan`) and a `<p>` in every cell |
| Drop or paste image files | `createImageDropPastePlugin()` on the Image extension | Base64 `<img>` or, with `drag_drop.method = upload`, the uploaded URL |

The package's part is event delegation on `document`, keyed on the closest `ui-editor` element and its `editor` property, so editors rendered later by Livewire work too. `hr`, `code-block` and the `table-*` commands belong to Flux's toolbar handler: never handle them in `app.js` as well, or the command runs twice.

## The app.js contract

`resources/js/app.js` must import the package's JavaScript and CSS from `vendor/`, register the Image and Link extensions on the `flux:editor` event with `e.detail.registerExtension()`, enable Flux's bundled table extensions in the same listener with `e.detail.enableExtension?.(name)` for `table`, `tableRow`, `tableHeader` and `tableCell`, and call `initLaravelFilemanager()` once. The Image extension is extended with `class`, `style` and `data-align` attributes, `addNodeView()` returning null, `resize: false`, `inline: true`, `allowBase64: true`, and `addProseMirrorPlugins()` returning the drop and paste plugin. The Link extension gets `target`, `class` and `style`, `openOnClick: false` and the `rel` attribute.

The package is read through one namespace import, `import * as fluxFilemanager from '…/laravel-filemanager.js'`, and the plugin factory is called as `fluxFilemanager.createImageDropPastePlugin?.()`. That is what keeps a `vendor/` copy that is older than the setup block from taking the whole file down: a named import of an absent export is a module error, and then no button works at all. Don't tidy it into named imports.

`php artisan flux-filemanager:install` writes exactly this block between `// flux-filemanager:start` and `// flux-filemanager:end`, and `vendor/darvis/livewire-flux-editor-filemanager/examples/app.js` is the complete file. When something is missing, compare with that file rather than writing the registration from memory.

The table extensions ship with Flux Pro 2.15.1, which the package requires since 1.10.0, and stay off until the block enables them. Never install them through npm or register them with `registerExtension()`: a second copy of ProseMirror breaks the editor. The installer adds the four `enableExtension` lines to a block written before 1.10.0, after its `e.detail.__fluxFilemanagerExtensionsRegistered = true` line; a block without that line is left alone with the warning "app.js has a setup block without the table extensions. Compare it with examples/app.js in the package.", and you copy the lines from that file.

## The toolbar

One string decides which buttons show and in what order: `toolbar="heading | bold italic | image link file-link"`. Names are separated by spaces, `|` is a separator and `~` a spacer, the same syntax as Flux's own `toolbar` prop; an unknown name throws "Flux component [editor.<name>] does not exist.". Whitespace runs are collapsed, and `toolbar=""` or `:toolbar="true"` gives the `default` preset.

The three presets, as `Darvis\FluxFilemanager\View\Components\Editor::PRESETS` holds them:

- `minimal`: `bold italic | link`
- `default`: `heading | bold italic strike | bullet ordered blockquote | image link file-link | align`
- `full`: `heading | bold italic strike underline highlight subscript superscript | bullet ordered blockquote | image link file-link table | align | code code-block hr clear-format | html-source ~ undo redo`

A preset name inside the string expands to its items: `toolbar="default | html-source"` is the default preset plus the HTML button, `toolbar="full | checklist"` the full preset plus the checklist button. To drop a button, copy the preset string and remove the word.

The package buttons are Flux toolbar items by file name, so they also work in a Blade toolbar: `<flux:editor.image />`, `<flux:editor.file-link />`, `<flux:editor.html-source />`, `<flux:editor.hr />`, `<flux:editor.code-block />`, `<flux:editor.clear-format />`, `<flux:editor.table />` and `<flux:editor.checklist />`. `@include('flux-filemanager::flux.editor.image')`, the form from before 1.9, renders the same button; a view published with `--tag=flux-filemanager-views` is used by every form, the string included. A Blade toolbar is for a dropdown or a button with your own logic; `:toolbar="false"` renders the slot instead of a preset:

```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.heading />
        <flux:editor.separator />
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.separator />
        <flux:editor.image />
        <flux:editor.link />
        <flux:editor.file-link />
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

A file `resources/views/flux/editor/<name>.blade.php` in the application is an item named `<name>` too, in the string and as `<flux:editor.<name> />`.

## Tables

The `table` button, in the `full` preset and as `<flux:editor.table />`, is a menu: insert a 3×3 table with a header row, add a row above or below, delete the row, add a column left or right, delete the column, header row and header column on or off, merge or split cells, delete the table. Flux's editor runs every command through the `data-editor` value of the menu item; the package adds the button, the menu and the setup lines. It needs Flux Pro 2.15.1 or newer and the four `enableExtension` lines in the setup block of `app.js`. An application that installed the package before 1.10.0 runs `php artisan flux-filemanager:install` again or copies the lines from `examples/app.js`; `php artisan flux-filemanager:check` warns while they are missing. Until then the button is disabled and the console says: `Flux Filemanager: the table extensions are not enabled, so the table button is disabled. Run php artisan flux-filemanager:install again; it adds the lines to resources/js/app.js.`

Flux's own editor stylesheet draws the tables in the editor (borders, header background, selected cells, the column resize handle, dark mode), with selectors that start with `[data-flux-editor] [data-slot="content"]` and outrank a `.ProseMirror` rule. `resources/css/tiptap-table.css` only adds the two rules Flux lacks, no margin on the paragraph in a cell and left-aligned header cells; `tiptap-image.css` imports it, so every application that imports that file has it without a change to `app.js`. Changing the look of tables inside the editor takes a selector more specific than Flux's, not a later stylesheet. The public page needs its own table CSS; Tailwind Typography's `prose` already styles tables. The HTML source modal keeps `<table>` since 1.10.0; its hint names what still goes, such as `<iframe>` and `<script>`.

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
| `drag_drop.max_file_size`, `.allowed_types` | 5 MB, jpeg/png/gif/webp/svg | Checked in the browser only; refused with an alert. Server-side limits are in `config/lfm.php` |

Translations: group `flux-filemanager::filemanager`, languages `en`, `nl`, `de`, publish with `--tag=flux-filemanager-lang`. They reach the JavaScript through the same JSON.

## Rendering the content

The editor stores HTML with classes, inline styles and possibly base64 images. Render it unescaped in a `prose` container for content written by trusted users. Sanitise it first when the editors aren't trusted; the package doesn't. Style `.tiptap-image` and `.align-left`, `.align-center`, `.align-right` in the front-end CSS, and give `table`, `th` and `td` borders and padding there too; the package's stylesheets only cover the editor.

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

`php artisan flux-filemanager:check` is the first thing to run when anything is off. It reports, with a fix for each: Laravel Filemanager present, `config/lfm.php` published, the configured URL registered as a route, the file manager behind `auth`, `public/storage` linked, the four TipTap npm packages present, `app.js` calling `initLaravelFilemanager()` through the namespace import, `app.js` enabling Flux's table extensions (label "resources/js/app.js enables Flux's table extensions"; a warning, not a failure, when the `enableExtension` lines are missing), and the build present and newer than the package's JavaScript. It exits non-zero on a failure, works without the demo routes, and the checklist page runs the same list through `Darvis\FluxFilemanager\Support\InstallationCheck`. Don't write a new check somewhere else; add it there.

## Pitfalls

- "View code" does nothing: Flux's `</>` button is the inline `code` mark and needs a selection. The package's HTML button opens the source: it is in the `full` preset, `toolbar="default | html-source"` adds it to the default one, and a Blade toolbar uses `<flux:editor.html-source />`.
- The checklist button opens a 404: it is in no preset since 1.9.0 and needs `demo_routes`. Put `checklist` in the toolbar string while setting up locally, `toolbar="full | checklist"`, with `FLUX_FILEMANAGER_DEMO_ROUTES=true`.
- A horizontal rule appears twice, a code block toggles straight back, or a table row is added twice: `app.js` handles `hr`, `code-block` or a `table-*` command itself. Flux's toolbar handler already runs those commands; the package JavaScript only handles `clear-format`.
- Buttons do nothing: `app.js` lacks the setup block or `initLaravelFilemanager()`, or the build is stale. The checklist page at `/darvis/filemanager-checklist` (with `FLUX_FILEMANAGER_DEMO_ROUTES=true`) shows what's missing.
- All buttons dead at once, right after updating the package: the running Vite dev server is serving the old package JavaScript, because `vendor/` is in `server.watch.ignored`. The console shows the failing import. Restart `npm run dev`; a `npm run build` is unaffected. Check the console before you change any code.
- Images insert but don't load: `APP_URL` doesn't match the host in the browser. Laravel Filemanager builds absolute URLs from it.
- The popup shows a login page or 404: `use_package_routes` and `middlewares` in `config/lfm.php`.
- Drag and drop ignored: the setup block predates 1.2.0 and lacks `addProseMirrorPlugins()`.
- The table button is disabled, or a click logs `insertTable is not a function`: the setup block predates 1.10.0 and lacks the `enableExtension` lines. Run `php artisan flux-filemanager:install` again or copy them from `examples/app.js`; `flux-filemanager:check` reports it as a warning.
- Drag and drop with `drag_drop.method = upload` inserts nothing and shows no message: Laravel Filemanager refused the file (it answers 200 with an error in the JSON). Its default `valid_mime` for images has no WebP or SVG. Read the response in the Network tab or `storage/logs/laravel.log`.
- `rows` on the component does not change the height: Flux's editor has no `rows` prop. Use a class such as `**:data-[slot=content]:min-h-[300px]!`.
- Never add a second Image extension or a node view for images; Flux already has one.
- Never install the TipTap table extensions through npm or register them with `registerExtension()`; Flux Pro 2.15.1+ bundles them, and a second copy of ProseMirror breaks the editor.
- Never enable `demo_routes` in production.
