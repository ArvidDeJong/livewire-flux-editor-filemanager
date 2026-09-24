---
title: "Installation"
nav_order: 2
description: "Install darvis/livewire-flux-editor-filemanager step by step: the paid Flux Pro licence, the installer, protecting the file manager, checking it works."
---

# Installation

## Requirements

- PHP 8.2 or higher
- Laravel 11, 12 or 13
- Livewire 3 or 4
- Flux 2.0.2 or newer **and** Flux Pro 2.0.2 or newer (`livewire/flux` and `livewire/flux-pro`)
- Laravel Filemanager 2 (`unisharp/laravel-filemanager`), installed automatically as a dependency of this package
- Node with npm, and Vite with `resources/js/app.js`, the standard Laravel front-end setup

## Flux Pro is required, and it is paid

This package does not contain an editor. It extends `<flux:editor>`, and the Flux documentation says about that component: "The rich text editor component is only available in the Pro version of Flux." Flux Pro is a commercial product of the Flux team; you buy a licence at [fluxui.dev](https://fluxui.dev). This package itself is free (MIT).

`livewire/flux-pro` is not on Packagist. Composer downloads it from the private repository `https://composer.fluxui.dev`, with your Flux account email as username and your licence key as password. Composer only reads repositories from your application's own `composer.json`, so your application must know that repository **before** you require this package.

If Flux Pro is not in your application yet, install it first, as the [Flux installation guide](https://fluxui.dev/docs/installation) describes:

```bash
composer require livewire/flux
php artisan flux:activate
```

`flux:activate` asks for your email and licence key. It stores them in `auth.json`, adds the `composer.fluxui.dev` repository to your `composer.json`, and runs `composer require livewire/flux-pro`. Do not commit `auth.json`.

On a server or in CI, where nobody can answer a prompt, run this before `composer install`:

```bash
composer config http-basic.composer.fluxui.dev your-email your-license-key
```

## Step 1: Install the package

```bash
composer require darvis/livewire-flux-editor-filemanager
```

Laravel discovers the service provider by itself. Composer installs Laravel Filemanager along with it.

## Step 2: Run the installer

```bash
php artisan flux-filemanager:install
```

The installer asks a yes or no question before each step. Press Enter to accept a step. Add `--no-interaction` to accept all of them, for example in a script. At the end it asks whether you want to star the repository on GitHub; yes opens the repository in your browser. With `--no-interaction` that question is skipped.

| Question | What the step does |
| --- | --- |
| Publish the Laravel Filemanager configuration and assets? | Runs `vendor:publish` with the tags `lfm_config` and `lfm_public`. You get `config/lfm.php` |
| Enable the Laravel Filemanager routes at /filemanager? | Rewrites two lines in `config/lfm.php`: `use_package_routes` becomes `true` and `url_prefix` becomes `filemanager` |
| Create the storage link and upload directories? | Runs `storage:link` and creates `public/storage/photos` and `public/storage/files` |
| Install the npm packages (TipTap)? | Runs `npm install @tiptap/core @tiptap/pm @tiptap/extension-image @tiptap/extension-link`. TipTap is the editor library that the Flux editor is built on |
| Publish config/flux-filemanager.php? | Runs `vendor:publish --tag=flux-filemanager-config`. With `--force` it overwrites an existing file |
| Add the editor setup to resources/js/app.js? | Adds five import lines at the top, the setup block between `// flux-filemanager:start` and `// flux-filemanager:end`, and the call `fluxFilemanager.initLaravelFilemanager()` |
| Build the assets with npm? | Runs `npm run build` |

It ends with `Installation complete.` and a list of next steps.

You can run the installer again after an update. The `app.js` step adds only what is missing, and rewrites the named imports that versions 1.1.x and 1.2.0 wrote into the namespace import. Two steps are not neutral when you run them again: the routes step sets `url_prefix` back to `filemanager`, and `--force` replaces your `config/flux-filemanager.php`. Answer `no` to a step you do not want.

## Step 3: Protect the file manager

This package adds no authentication. The file manager has its own routes, and `middlewares` in `config/lfm.php` decides who reaches them. Middleware is the code Laravel runs before a request reaches a route; `auth` is the one that requires a logged-in user.

Open `config/lfm.php` and make sure `auth` is in the list:

```php
'middlewares' => ['web', 'auth'],
```

Laravel Filemanager 2.15 ships with this value, but check your file: without `auth`, anyone on the internet can browse and upload files.

Two things to know:

- `auth` lets in **every** logged-in user. If visitors can register on your site, add a middleware of your own that only lets editors through, for example `can:edit-pages` with a gate that you define.
- If your editors log in through another guard (a guard is a named way of logging in, such as `web` or `admin`), use `auth:admin`.

Read [Uploads and security](uploads-and-security.md) before you go live.

## Step 4: Use the component

In the Blade view of a Livewire component, use the package's component where you would use `<flux:editor>`:

```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor wire:model="content" />
    <flux:error name="content" />
</flux:field>
```

The layout of that page must load `resources/js/app.js` with `@vite` and contain `@fluxScripts`, as any page with Flux components does. [Your first editor](first-editor.md) has the complete example.

## Check that it works

Run:

```bash
php artisan flux-filemanager:check
```

When everything is right you see nine check marks and this at the end:

```text
  ✓ Laravel Filemanager is installed
  ✓ config/lfm.php is published
  ✓ The file manager answers at /filemanager
  ✓ The file manager is behind authentication
  ✓ public/storage is linked
  ✓ The TipTap packages are installed
  ✓ resources/js/app.js calls initLaravelFilemanager()
  ✓ The assets are built
  ✓ APP_URL is set

The installation is complete.
```

Then log in to your application, open the page with the editor and click the image button. Laravel Filemanager opens in a popup window. Pick an image and confirm: the image appears in the editor.

When you see something else:

- A red `✗` line has the fix printed under it, and the command ends with `problem(s) to fix` and exit code 1. Do what the line says and run the check again.
- A yellow `!` line is a warning. `The Vite dev server is running` means `npm run dev` is active; restart it after every update of this package.
- The check passes but the buttons do nothing, the popup shows a login page, or the image does not load: see [Troubleshooting](troubleshooting.md).

The command changes nothing and needs no demo pages, so you can also run it on a server or in a deploy script.

## Try the demo pages first (optional)

The package has two pages to try the editor without writing a view. They have **no authentication**, so they are off by default. Turn them on in your local `.env` only:

```env
FLUX_FILEMANAGER_DEMO_ROUTES=true
```

| URL | What it shows |
| --- | --- |
| `/darvis/editor-demo` | An editor with the `full` toolbar, a preview of the HTML, and a warning when `APP_URL` does not match the host in your browser |
| `/darvis/filemanager-checklist` | The same checks as `flux-filemanager:check`, plus a check that the `APP_URL` host matches the current host |

If the pages return a 404 after you changed `.env`, run `php artisan config:clear`. The file manager popup on the demo page still needs you to be logged in.

## Install by hand instead

Each installer step can be done by hand:

```bash
php artisan vendor:publish --tag=lfm_config
php artisan vendor:publish --tag=lfm_public
php artisan storage:link
npm install @tiptap/core @tiptap/pm @tiptap/extension-image @tiptap/extension-link
php artisan vendor:publish --tag=flux-filemanager-config
```

In `config/lfm.php`, set `use_package_routes` to `true` and `url_prefix` to `filemanager`. If you register the file manager routes yourself under another URL, set `url` in `config/flux-filemanager.php` to that URL.

Then copy [examples/app.js](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/blob/main/examples/app.js) into `resources/js/app.js`, or merge it with what you have, and run `npm run build`. The file imports the package's JavaScript and CSS from `vendor/`, registers the Image and Link extensions on the `flux:editor` event, and calls `initLaravelFilemanager()`.

Keep the import as it is: `import * as fluxFilemanager from '…/laravel-filemanager.js'`. With a named import, one export that is missing in an older copy under `vendor/` is a module error, and then nothing in `app.js` runs.
