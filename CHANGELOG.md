# Changelog

All notable changes to **darvis/livewire-flux-editor-filemanager** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Fixed
Documentation only; nothing in the package changes. Every page, the README, the FAQ and the Boost files were checked against the code.
- `rows` was documented as the height of the editor. It is printed as a plain `rows` attribute, and Flux's editor has no such prop, so it changes nothing. The docs now show the class Flux documents for the height
- `drag_drop.max_file_size` and `drag_drop.allowed_types` were described as limits without saying where they are checked. They are checked in the browser only; on the server only `config/lfm.php` and PHP count. Laravel Filemanager's default image types have no WebP or SVG, so with `drag_drop.method = upload` such a file passes the package's check, is refused by Laravel Filemanager, and nothing is inserted without a message
- "Running the installer again is safe: it only adds what is missing" is true for `app.js` only. The routes step sets `url_prefix` in `config/lfm.php` back to `filemanager`, and `--force` replaces `config/flux-filemanager.php`
- "The image is inserted but doesn't load" blamed `APP_URL` alone. For images picked with the image button the package already drops a foreign host from a `/storage/` URL; a missing storage link is the other cause, and only images uploaded by drag and drop keep the wrong host
- The HTML examples showed relative `src` and `href` values. The package stores the absolute URL Laravel Filemanager returns, and only makes it relative when the host differs from the page
- `messages.no_images_selected` was described as reserved for host apps that build on the JavaScript. It is not passed to the JavaScript at all
- The two alert texts come from `messages.*` in the config and win over the language files, so they stay English in every locale; the alerts for a refused dropped image are fixed English strings. The localization page said all texts follow the locale
- "English, Dutch and German": the Dutch file has every key, but most of its values are still English
- The default and `full` toolbars have a checklist button that opens a 404 while `demo_routes` is off. That was not mentioned
- The first-editor page said MySQL truncates a `text` column silently; with Laravel's strict mode it fails instead. Its component had no `render()` method and no file names, and `make:livewire` creates a different file layout in Livewire 4
- The requirements differed between pages ("Flux Pro 2" and "Flux Pro 2.0.2"). They now follow `composer.json` everywhere, and say that Flux Pro is a paid licence and that the application needs the Flux composer repository before this package can be required
- Unverifiable wording removed ("nine times out of ten", what Laravel's starter kits put in the Vite config, what search engines do with `nofollow`)
- The FAQ went from 12 questions to 10, with the questions on what the package is, what it costs and whether it is safe added

### Added
- Documentation pages: [Troubleshooting](https://arviddejong.github.io/livewire-flux-editor-filemanager/troubleshooting.html) (symptom, cause, fix, with the literal messages from the code), [Testing](https://arviddejong.github.io/livewire-flux-editor-filemanager/testing.html) (a complete test for a Livewire form with the editor) and [Uploads and security](https://arviddejong.github.io/livewire-flux-editor-filemanager/uploads-and-security.html) (what the package checks, what Laravel Filemanager checks, and what nobody checks)
- The installation page explains the Flux Pro requirement and has a "Check that it works" section with the expected output of `flux-filemanager:check`
- `tests/DocsSiteTest.php` checks that every relative link between the pages resolves, that the home page links every page, and that the messages quoted on the troubleshooting page exist in the code

### Changed
- The README follows the order shared by all darvis packages, and gained the Laravel Boost, Changelog and Security sections. The troubleshooting section moved from the installation page to its own page

## [1.4.0] - 2026-09-20
### Added
- `FluxFilemanagerConfig` with named accessors is the one place that reads the package config. Every default is written down once, so a caller cannot quietly disagree with the config file about what it is. Two Blade views read `drag_drop.upload_url` and `drag_drop.max_file_size` without a fallback at all

### Changed
- The config keys are in alphabetical order. No key, default or behaviour changed

### Fixed
- `livewire/flux` and `livewire/flux-pro` now require `^2.0.2` instead of `^2.0`. Flux 2.0.0 and 2.0.1 cannot resolve their own anonymous component views on Laravel 12, so the editor rendered a 500 with `Flux component [icon.loading] does not exist`. Flux 2.0.2 fixed that; nothing else changed
- The test for "the build is older than this package" compared the manifest against a fixed `time() - 86400`, so it only held while the package JavaScript had been touched that same day. It passed in CI, where a checkout stamps every file, and failed on any working copy older than a day. Both timestamps are now set relative to the package file

## [1.3.0] - 2026-09-18
### Added
- `php artisan flux-filemanager:check` goes through the installation and prints what to do about every problem: Laravel Filemanager, `config/lfm.php`, the file manager routes, whether those routes are behind authentication, the storage link, the four TipTap npm packages, the setup in `resources/js/app.js` and the build. It needs no demo routes and exits non-zero when something is broken, so it also works in CI or a deploy script
- A missing `auth` in `lfm.middlewares` is reported as a failure, not a note: the file manager has its own routes, and without it anyone can browse and upload
- The check notices two things that are hard to see otherwise: a build older than the package's JavaScript, and a running Vite dev server, which does not watch `vendor/` and keeps serving the copy it read at startup
- [Your first editor](https://arviddejong.github.io/livewire-flux-editor-filemanager/first-editor.html) in the documentation: the whole path from migration to published page, with the `longText` column, validation, rendering the HTML in a `prose` container, the CSS for `.tiptap-image` and the alignment classes, and the three problems people hit the first time
- Three FAQ entries about the buttons doing nothing, showing saved content and checking the installation

### Fixed
- One toolbar button that stops working no longer means all of them do. The setup block in `app.js` read the package with a named import, so a `vendor/` copy without `createImageDropPastePlugin` (a running Vite dev server serves the copy it read at startup, because `vendor/` is in `server.watch.ignored`) was a fatal module error: `app.js` never ran, no listeners were registered, and clicking the image button did nothing. The block now uses one namespace import and calls the plugin factory with `?.()`, so a stale copy costs only drag and drop and logs what to do

### Changed
- `php artisan flux-filemanager:install` rewrites the named imports and calls that 1.1.x and 1.2.0 wrote into the namespace form, whatever their order and quoting, and running it twice changes nothing. Its next steps now say to restart `npm run dev` after an update. **Run it again after updating**, or change the import in your `app.js` to `import * as fluxFilemanager from '…/laravel-filemanager.js'` yourself
- The checklist page at `/darvis/filemanager-checklist` runs the same checks as the command, through `Darvis\FluxFilemanager\Support\InstallationCheck`, and shows the fix under every failed check. It gained the authentication, storage, npm and build checks
- `examples/app.js`, the installer stub, the documentation and the Boost guideline and skill describe the namespace import; the troubleshooting pages name the stale dev server as the cause of every button going dead at once
- `composer.lock` is no longer in the dist archive

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
