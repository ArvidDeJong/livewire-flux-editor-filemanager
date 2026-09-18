# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package overview

`darvis/livewire-flux-editor-filemanager` is a Laravel package (PHP 8.2+, Laravel 11/12/13, Livewire 3/4, Flux Pro 2) that puts Laravel Filemanager inside the Flux Pro editor: toolbar buttons that open the file manager for images and file links, a resize/align menu and an edit modal for images, link editing, and drag and drop or paste of image files. Host apps consume it via Composer; this repo only contains the library.

- Namespace: `Darvis\FluxFilemanager\` → `src/`
- Service provider auto-registered via `extra.laravel.providers` in [composer.json](composer.json)
- Config key: `flux-filemanager`; view namespace and translation group: `flux-filemanager`

## Commands

```bash
composer install              # needs a Flux Pro licence: composer config http-basic.composer.fluxui.dev email key
composer test                 # Pest
vendor/bin/pest --filter "toolbar"
composer lint                 # Pint (check only); composer format fixes
composer analyse              # Larastan, level 8
node --input-type=module --check < resources/js/laravel-filemanager.js   # the only JS check there is
```

CI (`.github/workflows/tests.yml`) runs PHP 8.2–8.4 × Laravel 11/12/13 × lowest/stable, plus Pint and Larastan. It authenticates against composer.fluxui.dev with the `FLUX_USERNAME` and `FLUX_LICENSE_KEY` secrets and turns off Composer's advisory blocking, because every Laravel 11 release has an open advisory. `livewire/flux-pro` is private: `composer.json` declares the Flux repository so the package installs on its own, and host apps have it already.

## Architecture

- Most of the behaviour is in [resources/js/laravel-filemanager.js](resources/js/laravel-filemanager.js), not in PHP. It is event delegation on `document`: `[data-editor="image"]` and `[data-editor="file-link"]` buttons, `.ProseMirror img` (click: resize menu, dblclick: modal) and `.ProseMirror a` (click: link modal in edit mode). It finds the TipTap instance as `editor` on the closest `ui-editor` element, which Flux sets. Nothing in the JS may assume a single editor on the page.
- The JS reads settings from the `<script type="application/json" data-flux-filemanager-config>` tag that [Editor.php](src/View/Components/Editor.php) renders through `jsConfig()`, falling back to `window.fluxFilemanagerConfig` when the host app sets it. The translations travel in that JSON as `i18n`; `t(key, fallback)` reads them. Drag and drop settings are data attributes on the `ui-editor` element, read per editor by [drag-drop-config.js](resources/js/drag-drop-config.js). Don't add a second way to pass config to the JS.
- Images are changed with `updateAttributes('image', …)` through `updateImage()`, never by deleting and re-inserting the node: before 1.2.0 the resize menu re-inserted and lost alt, title and extra classes. `imageAttributes()` recomputes the generated part of `class` and `style` (`tiptap-image`, `align-*`, `width`, margins, `display: block`) and `extraClasses()`/`extraStyles()` keep the rest. After a command the JS dispatches `input` and `blur` on the `ui-editor`, because Flux syncs `wire:model` on those.
- Editing a link is `extendMarkRange('link')` + `insertContent()` with a new link mark; inserting is the same without the extend. `showFileLinkModal(editor, url, existing)` does both.
- `createImageDropPastePlugin()` is a ProseMirror plugin (`@tiptap/pm/state`, which `@tiptap/core` depends on). It only claims events that carry image files. The host app must add it to the Image extension's `addProseMirrorPlugins()`; the setup block does that.
- The setup block that registers the Image and Link extensions lives once, in [resources/stubs/flux-filemanager-setup.js](resources/stubs/flux-filemanager-setup.js). The installer writes it into the host's `app.js`, and [examples/app.js](examples/app.js) is imports + that block + `initLaravelFilemanager()`. `tests/Feature/InstallStubTest.php` fails when they differ; regenerate `examples/app.js` from the stub, don't edit it by hand.
- The Image extension in the stub returns null from `addNodeView()` and sets `resize: false`, because Flux's editor already renders images with its own node view; two node views on the same node break selection. Don't "fix" that.
- The demo and checklist routes in [routes/web.php](routes/web.php) are loaded only when `flux-filemanager.demo_routes` is true. They have no auth; before 1.2.0 they were on every site that installed the package. Never register them unconditionally.
- `Editor::$toolbar` is `string|bool`. It was `string`, and `:toolbar="false"` became `""`, so the custom toolbar slot never rendered.
- The views use only components that exist in Flux 2.0.0, because `prefer-lowest` in CI installs that version. `flux:callout`, the `flux:sidebar.*` sub-components and `@blaze` came later; an unknown directive like `@blaze` is printed as text by Blade. Raise the lower bound in `composer.json` before using newer components.
- The installer ([InstallCommand.php](src/Console/InstallCommand.php)) publishes only the config. Publishing views by default means users stop receiving view updates.

## Testing

- [TestCase](tests/TestCase.php) registers Livewire, Flux, Flux Pro and the package, and shares an empty `ViewErrorBag` as `errors`: Flux's views read `$errors`, and `$this->blade()` has no web middleware to share it. [DemoTestCase](tests/DemoTestCase.php) turns the demo routes on and calls `withoutVite()`, because the demo layout uses `@vite`. `tests/Demo` uses it (see `tests/Pest.php`); `tests/Feature` and `tests/Unit` use the plain case.
- Larastan can't resolve the package's view namespace, so `render()` methods put the view name in a `/** @var view-string */` variable instead of ignoring the error.
- There is no JavaScript test runner. Changes to the JS need a syntax check and a look in a host app with the demo page.

## Conventions

- `docs/` holds the user documentation and is also the GitHub Pages site (Jekyll, Just the Docs, `docs/_config.yml`); the README only has the quick start and links. Keep both in step with behaviour changes. Every page needs `title`, `description` and `nav_order` front matter, unique per page, and no `: ` in an unquoted value. Don't write `{{ }}` or `{% %}` in pages: Jekyll's Liquid renders it. Liquid is intended only in `faq.md`, `llms.txt` and `_includes/`. Package facts live in `docs/_config.yml` (`package`, `developer`) and FAQ answers in `docs/_data/faq.yml`; the pages, the structured data and `llms.txt` read from there. The footer credit is `ARVID.NL` only, no personal name. `tests/DocsSiteTest.php` guards these rules.
- `resources/boost/` holds the Laravel Boost guideline and skill that host apps receive. Update them when public behaviour or config changes. The guideline is a Blade file: no `{{ }}` or `{!! !!}` outside `@verbatim`.
- Keep the public API compatible within 1.x: `<x-flux-filemanager-editor>` with `id`, `rows` and `toolbar`; the config keys; `flux-filemanager:install`; `initLaravelFilemanager()`, `createImageDropPastePlugin()` and the paths of `resources/js/laravel-filemanager.js` and the two CSS files, which host apps import from `vendor/`; the `data-editor` attributes; the translation keys.
- A change a site owner notices (messages, defaults, generated HTML, what the installer does) is a minor release, not a patch.
- Everything is in English: code, comments, messages, README and CHANGELOG. Translations live in `resources/lang/{en,nl,de}`; add every new key to all three, and any key the JS uses through `t()` must exist in `en`. `tests/Feature/TranslationsTest.php` checks both.
- No `version` field in `composer.json`: Packagist reads the git tag and silently skips tags that don't match the field.
