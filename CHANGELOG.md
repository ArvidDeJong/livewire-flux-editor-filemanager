# Changelog

All notable changes to **darvis/livewire-flux-editor-filemanager** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2026-09-18
### Added
- Drag and drop and paste of image files now work. `createImageDropPastePlugin()` in `laravel-filemanager.js` is a ProseMirror plugin that the installer and `examples/app.js` add to the Image extension. Base64 by default; `drag_drop.method = upload` posts the file to Laravel Filemanager. Size and type limits come from `drag_drop`. If you installed 1.1.x, run `php artisan flux-filemanager:install` again or add `addProseMirrorPlugins()` to your `app.js` as in `examples/app.js`
- The editor component renders its settings and translations as a JSON script tag, so `url`, `popup`, `resize_presets`, `custom_width` and `messages` from the config, and the Dutch and German translations, now reach the JavaScript menus and modals. Before, the JavaScript always used its built-in English defaults
- `demo_routes` config option and `FLUX_FILEMANAGER_DEMO_ROUTES` env variable
- Clicking a link in the editor edits it: the modal opens prefilled and Update replaces the link
- The resize menu takes its presets and custom width limits from the config, and the edit modal lists the current width when it isn't a preset
- Documentation site on GitHub Pages (https://arviddejong.github.io/livewire-flux-editor-filemanager/), built from `docs/`, with a FAQ, `llms.txt`, sitemap and structured data
- GitHub Actions: PHP 8.2-8.4 x Laravel 11/12/13 x lowest/stable, plus Pint and Larastan (level 8); `composer lint`, `format` and `analyse` scripts; Dependabot
- Laravel Boost guideline and `livewire-flux-editor-filemanager-development` skill in `resources/boost`
- `SECURITY.md` with private vulnerability reporting, `CODE_OF_CONDUCT.md`, issue forms, pull request template, `CLAUDE.md`, `.gitattributes`
- Tests for the translations (every key in every language, every key the JavaScript uses exists), the toolbar presets, the config output, the install stub and the docs site

### Changed
- The demo and checklist pages (`/darvis/editor-demo`, `/darvis/filemanager-checklist`) are off by default, because they have no authentication and were registered on every site that installed the package. Set `FLUX_FILEMANAGER_DEMO_ROUTES=true` to get them back
- `livewire/flux` and `livewire/flux-pro` now require `^2.0`, and the package depends on `laravel/framework` instead of `illuminate/support`. The Flux composer repository is declared in `composer.json` so the package installs on its own; host apps with Flux Pro already have it
- The installer no longer runs `composer require unisharp/laravel-filemanager` (it is a dependency of this package), installs `@tiptap/pm` too, publishes only the config instead of the config, views and a tag that didn't exist, and writes the `app.js` setup block from `resources/stubs/flux-filemanager-setup.js`. Publish the views with `--tag=flux-filemanager-views` if you want to change them
- `initLaravelFilemanagerForAllEditors()` is a deprecated alias of `initLaravelFilemanager()`; calling both no longer registers the image button twice
- The values in the image and link modals are HTML-escaped
- The demo layout and views use only components that exist in Flux 2.0: the mobile sidebar is gone and the callouts are cards. The toolbar partials no longer use `@blaze`, which older Flux versions printed as text
- `package.json` removed; the package isn't published on npm
- The README is shorter and links to `docs/`

### Fixed
- `:toolbar="false"` now renders the slot. The `toolbar` property was typed `string`, so `false` became `""` and the default toolbar appeared
- Resizing an image through the quick menu no longer drops its alt text, title, extra classes and styles; aligning keeps the extra classes and styles too
- Clicking a link inserted a second link instead of editing the one clicked
- The service provider registered the Blade component as a Livewire component, which fails when used
- `tests/Feature/ServiceProviderTest.php` had no line breaks, so PHP treated it as text and its tests never ran

## [1.1.4] - 2026-03-25
### Changed
- Laravel 13 allowed in the package constraints (`illuminate/support: ^11.0|^12.0|^13.0`); README and installation docs mention it

## [1.1.3] - 2026-02-20
### Added
- `APP_URL` diagnostic callout in the demo view that checks whether the `APP_URL` host matches the current request host, with a `php artisan config:clear` hint on a mismatch

### Changed
- Demo headings, text and callouts use Flux UI components (`flux:heading`, `flux:text`, `flux:callout`)
- Demo text in Blade views and the demo Livewire component uses translation keys; new keys for the demo labels and the `APP_URL` diagnostics in `en`, `de` and `nl`

### Fixed
- Runtime filemanager settings (`filemanager_url`, popup width and height) passed from the Blade config to JavaScript
- Malformed Blade JSON directive in the editor component config bootstrapping

## [1.1.2] - 2026-02-19
### Changed
- The installer's npm step installs the full TipTap stack: `@tiptap/core`, `@tiptap/extension-image`, `@tiptap/extension-link` and `prosemirror-state`
- The installer's "next steps" output shows the current route and asset import paths
- Documentation: local package development with a symlink, the `--no-interaction` installer example, and installation JS examples with the Link and Image extensions and the drag-drop helper imports

### Fixed
- The installer no longer depends on the unavailable `task()` helper, which crashed `php artisan flux-filemanager:install`

## [1.1.1] - 2026-01-22
### Changed
- Documentation restructured: shorter README, installation, drag-drop and workflow pages, all referencing `examples/app.js` for the complete code; route configuration examples reduced to one basic and one advanced option

## [1.1.0] - 2026-01-22
### Added
- Drag and drop and paste of images, with base64 encoding, multiple images at once and position control, documented in `docs/DRAG-DROP.md`
- `prosemirror-state` as a requirement, `handleDrop` and `handlePaste` handlers, FileReader integration

### Changed
- README, Image extension and installation instructions updated for drag and drop

## [1.0.1] - 2026-01-22
### Added
- File links: insert downloadable file links (PDF, Word, Excel, ZIP and others) through a modal with link text, target, CSS classes and styles, and a file link button in the toolbar

### Changed
- Dutch comments in Blade views, Dutch strings in the JavaScript modals and the documentation translated to English

### Fixed
- Undefined `isEdit` variable in the file link modal JavaScript

## [1.0.0] - 2026-01-22
### Added
- Laravel Filemanager integration for the Flux TipTap editor: image insertion through a popup, image resize (25%, 50%, 75%, 100% and a custom percentage) and alignment (left, center, right)
- `<x-flux-filemanager-editor>` Blade component with the `default`, `minimal` and `full` toolbar presets and a slot for a custom toolbar
- `php artisan flux-filemanager:install` command and `config/flux-filemanager.php` with the filemanager URL, popup dimensions, resize presets and error messages
- Clean HTML output without shortcodes, generic for every Livewire component
- Test suite, example files, README and workflow documentation, MIT license
- Requires PHP 8.2+, Laravel 11 or 12, Livewire 3 or 4, Flux UI with Flux Pro, Laravel Filemanager and the TipTap Image extension
