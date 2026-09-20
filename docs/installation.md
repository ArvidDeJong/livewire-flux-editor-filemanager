---
title: Installation
nav_order: 2
description: Install darvis/livewire-flux-editor-filemanager with the installer or by hand, protect the file manager routes and try the demo page.
---

# Installation

## Requirements

- PHP 8.2 or higher
- Laravel 11, 12 or 13
- Livewire 3 or 4
- Flux Pro 2.0.2 or newer. The editor is a Pro component, so your application already has the Flux composer repository and a licence
- Laravel Filemanager 2 (`unisharp/laravel-filemanager`), installed as a dependency of this package
- Vite with `resources/js/app.js`, the usual Laravel setup

## 1. Install the package

```bash
composer require darvis/livewire-flux-editor-filemanager
```

The service provider is discovered automatically.

## 2. Run the installer

```bash
php artisan flux-filemanager:install
```

Add `--no-interaction` to accept every step, for example in a deploy script. The installer:

1. Publishes the Laravel Filemanager config and assets (`lfm_config`, `lfm_public`)
2. Sets `use_package_routes` to `true` and `url_prefix` to `filemanager` in `config/lfm.php`
3. Runs `storage:link` and creates `public/storage/photos` and `public/storage/files`
4. Installs `@tiptap/core`, `@tiptap/pm`, `@tiptap/extension-image` and `@tiptap/extension-link` with npm
5. Publishes `config/flux-filemanager.php` (`--force` overwrites an existing file)
6. Adds the imports, the extension setup block and `initLaravelFilemanager()` to `resources/js/app.js`
7. Runs `npm run build`

Running it again is safe: it only adds what is missing. Coming from 1.1.x or 1.2.0, it rewrites the import of the package's JavaScript and the calls in your setup block to the namespace form, and warns if your setup block predates drag and drop.

## 3. Check it

```bash
php artisan flux-filemanager:check
```

This goes through the installation - the config, the routes, whether the file manager is behind authentication, the storage link, the npm packages, your `app.js` and the build - and prints what to do about everything that isn't right. It needs no demo routes, so you can run it on any environment, and it exits non-zero when something is broken, which makes it usable in a deploy script.

## 4. Protect the file manager

The editor only opens `/filemanager`; who may use it is decided by Laravel Filemanager. Set the middleware in `config/lfm.php`:

```php
'middlewares' => ['web', 'auth'],
```

Use `auth:staff` or your own guard if the editors log in elsewhere. Upload limits and allowed file types are Laravel Filemanager settings too.

## 5. Use the component

```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor wire:model="content" />
    <flux:error name="content" />
</flux:field>
```

Attributes: `toolbar` (`default`, `minimal`, `full`, or `false` for your own toolbar in the slot), `rows` and `id`. Everything else, such as `wire:model`, goes to `<flux:editor>`. See [Configuration](configuration.md#the-component).

Display the content as you would any editor HTML:

```blade
<div class="prose max-w-none">
    {!! $page->content !!}
</div>
```

The package does not sanitise this HTML. Editors can add classes, inline styles and base64 images, so give the editor to trusted users only, or sanitise before rendering.

## Manual installation

If you prefer to do the installer's work by hand:

```bash
php artisan vendor:publish --tag=lfm_config
php artisan vendor:publish --tag=lfm_public
php artisan storage:link
npm install @tiptap/core @tiptap/pm @tiptap/extension-image @tiptap/extension-link
```

In `config/lfm.php` set `use_package_routes` to `true` and `url_prefix` to `filemanager`, or register the routes yourself and set `url` in `config/flux-filemanager.php` to match.

Then copy [examples/app.js](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/blob/main/examples/app.js) from the repository into `resources/js/app.js`, or merge it with what you have, and run `npm run build`. It imports the package's JavaScript and CSS from `vendor/`, registers the Image and Link extensions on the `flux:editor` event, and calls `initLaravelFilemanager()`. It reads the package through one namespace import, `import * as fluxFilemanager`, so a vendor copy that is older than your setup block costs you only the feature it lacks; a named import of a missing export takes the whole file down.

## Demo pages

The package ships two pages to try the integration and check the setup:

- `/darvis/editor-demo`: a full editor with a preview, and a warning when `APP_URL` doesn't match the host you are using
- `/darvis/filemanager-checklist`: checks the config, the routes and `app.js`

They have no authentication, so they are off by default. Enable them locally:

```env
FLUX_FILEMANAGER_DEMO_ROUTES=true
```

Leave that out of production.

## Troubleshooting

Start with `php artisan flux-filemanager:check`: it knows about most of what follows.

**Nothing happens when I click the image or file link button.** The JavaScript isn't running. Open the browser console: one failing import in `app.js` stops the whole file, so every button goes dead at once. Check that `app.js` contains `initLaravelFilemanager()` and the setup block, run `npm run build`, and hard-refresh. The checklist page shows this too.

**The buttons stopped working right after I updated the package.** Restart `npm run dev`. Vite does not watch `vendor/` (Laravel's starter kits put `**/vendor/**` in `server.watch.ignored`), so the dev server keeps serving the package JavaScript it read at startup, and its pre-bundled dependencies are from before your `npm install`. A production build (`npm run build`) reads the current files and is not affected.

**The popup is blocked.** Allow popups for your site. The package shows the `popup_blocked` message from the config.

**The popup opens but shows an error or a login page.** The `/filemanager` routes are missing or protected by a middleware you don't pass. Check `use_package_routes` and `middlewares` in `config/lfm.php`, and `url` in `config/flux-filemanager.php` if you changed the prefix.

**The image is inserted but doesn't load.** Laravel Filemanager builds URLs from `APP_URL`. When that host differs from the one in your browser, the image points to the wrong host. Set `APP_URL` to the host you actually use and run `php artisan config:clear`. The demo page warns about this.

**Drag and drop or paste doesn't work.** Your setup block predates 1.2.0. Compare it with `examples/app.js`: the Image extension needs `addProseMirrorPlugins()` with `createImageDropPastePlugin()`. Running the installer again rewrites the imports and the calls for you. When the console says the package JavaScript has no drop and paste plugin, the copy under `vendor/` is the older one: restart the dev server. See [Drag and drop](drag-and-drop.md).
