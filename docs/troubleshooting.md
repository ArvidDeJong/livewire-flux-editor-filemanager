---
title: "Troubleshooting"
nav_order: 11
description: "Symptoms, causes and fixes for the Flux editor with Laravel Filemanager: dead toolbar buttons, popup problems, broken images and failed drops."
---

# Troubleshooting

Start here:

```bash
php artisan flux-filemanager:check
```

The command goes through the installation and prints the fix under every line that is not right. The sections below are for what it cannot see, and explain its messages.

After every change to `.env` or a config file, run `php artisan config:clear`. After every change to `resources/js/app.js` or an update of this package, run `npm run build`, or restart `npm run dev`.

## Nothing happens when I click the image or file link button

**Cause.** The package's JavaScript is not running. One failing import in `resources/js/app.js` stops the whole file, so every button stops at the same time.

**Fix.**

1. Open the browser console (F12) and reload. A red error names the import that fails.
2. Run the check. It names these causes:
   - `resources/js/app.js calls initLaravelFilemanager()` fails: the setup is missing. Run `php artisan flux-filemanager:install`.
   - `The TipTap packages are installed` fails: run the `npm install` line it prints.
   - `The assets are built` fails: run `npm run build`.
   - `The build is older than this package`: run `npm run build`.
3. Check that the layout of the page loads `resources/js/app.js` with `@vite` and contains `@fluxScripts`.

## The buttons stopped working right after I updated the package

**Cause.** `npm run dev` was running during the update. The check says `The Vite dev server is running` with the hint "It does not watch vendor/, so restart npm run dev after updating this package. Until you do, it serves the JavaScript it read at startup."

**Fix.** Stop and restart `npm run dev`. A production build with `npm run build` reads the current files and is not affected.

## The check says: resources/js/app.js still uses a named import

**Cause.** Versions 1.1.x and 1.2.0 wrote `import { … } from '…/laravel-filemanager.js'` into your `app.js`. When the copy under `vendor/` lacks one of those names, the whole file fails.

**Fix.** Run `php artisan flux-filemanager:install` again and accept the `app.js` step. It rewrites the line to `import * as fluxFilemanager from '…'`.

## The browser says the popup was blocked

**Message.** `Popup was blocked by your browser. Please allow popups for this site.`

**Cause.** The file manager opens with `window.open()`, and the browser blocks popups for your site.

**Fix.** Allow popups for the site in the browser. The text is `messages.popup_blocked` in `config/flux-filemanager.php`.

## The popup opens but shows a login page

**Cause.** You are not logged in, or you are logged in through another guard than the one in `middlewares` in `config/lfm.php`. A guard is a named way of logging in, such as `web` or `admin`.

**Fix.** Log in first. If your editors use another guard, write `auth:admin` (with your guard's name) in `middlewares`. The demo page says the same: "You must be logged in to use the Laravel Filemanager features in this editor."

## The popup opens but shows a 404

**Cause.** No route answers at the URL the editor opens. The check reports it as `The file manager answers at /filemanager`.

**Fix.** In `config/lfm.php`, set `use_package_routes` to `true` and `url_prefix` to `filemanager`. If you serve the file manager somewhere else, set `url` in `config/flux-filemanager.php` to that URL. If `config/lfm.php` does not exist, run `php artisan vendor:publish --tag=lfm_config`. Then run `php artisan config:clear`.

## The check says: The file manager is behind authentication

**Cause.** `middlewares` in `config/lfm.php` contains neither `auth` nor a value starting with `auth:`. Anyone can then browse and upload files.

**Fix.** Add `'auth'` to the list. If you protect the routes with a middleware of your own under another name, this failure is a false alarm; the check only reads the config. See [Uploads and security](uploads-and-security.md).

## The image is inserted but does not load

**Cause 1.** The storage link is missing. The check says `public/storage is linked` with the hint "Run php artisan storage:link. Uploads land outside the web root without it, so images 404."

**Cause 2.** `APP_URL` in `.env` is not the host you use in the browser. Laravel builds the URL of a file on the `public` disk from `APP_URL`. For images picked with the image button the package drops a foreign host from a `/storage/` URL, so those keep working. Images uploaded by drag and drop with the `upload` method keep the URL as it was returned, with the wrong host.

**Fix.** Run `php artisan storage:link`. Set `APP_URL` to the address you open in the browser, including `http://` or `https://`, and run `php artisan config:clear`. The demo page and the checklist page compare the two hosts for you.

## Laravel Filemanager refuses my file

**Cause.** The file manager checks the MIME type on the server against `valid_mime` in `config/lfm.php`. By default it accepts JPEG, PNG and GIF images, and PDF for files. WebP, SVG, Word, Excel and ZIP are refused until you add them.

**Fix.** Add the MIME type to `folder_categories.image.valid_mime` or `folder_categories.file.valid_mime` in `config/lfm.php`. This is a Laravel Filemanager setting; the package has no part in it.

## Dropping or pasting an image does nothing

**Cause 1.** The setup block in `app.js` predates version 1.2.0 and has no `addProseMirrorPlugins()`. The installer warns: "app.js has a setup block from an older version without drag and drop. Compare it with examples/app.js in the package."

**Fix.** Replace the block between `// flux-filemanager:start` and `// flux-filemanager:end` with the one in [examples/app.js](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/blob/main/examples/app.js), then run `npm run build`.

**Cause 2.** The console says: "flux-filemanager: the package JavaScript has no drop and paste plugin. Restart the Vite dev server after updating the package, because it does not watch vendor/."

**Fix.** Restart `npm run dev`.

**Cause 3.** With `drag_drop.method` set to `upload`: Laravel Filemanager refused the file. It answers with HTTP status 200 and an error text in the JSON, and the package only looks for a URL in the answer, so nothing is inserted and no message appears.

**Fix.** Open the Network tab of the browser tools, drop the image again and read the response of the `upload` request. The same error is in `storage/logs/laravel.log`. The usual reason is a type that `valid_mime` in `config/lfm.php` does not list, such as WebP or SVG.

## An alert says: Image too large. Maximum size is 5.0MB

**Cause.** The dropped or pasted image is larger than `drag_drop.max_file_size`.

**Fix.** Raise `drag_drop.max_file_size` in `config/flux-filemanager.php`; the value is in bytes. With the `upload` method, PHP's `upload_max_filesize` and `post_max_size` must allow the size too.

## An alert says: File type image/bmp is not allowed

**Cause.** The MIME type of the file is not in `drag_drop.allowed_types`.

**Fix.** Add the type to `drag_drop.allowed_types`. With the `upload` method, add it to `valid_mime` in `config/lfm.php` as well.

## An alert starts with: Upload failed:

**Cause.** With the `upload` method, the server answered with an error status. The alert shows the status text the browser received, which can be empty. Open the Network tab of the browser tools and read the status code of the `upload` request:

- 419: the CSRF token is missing. The page has no `<meta name="csrf-token">` tag.
- 401: you are not logged in with a user that passes `middlewares` in `config/lfm.php`.
- 404: `drag_drop.upload_url` does not match the file manager's URL.
- 413: the web server refuses the size of the request.

**Fix.** Add the meta tag to your layout as the [Laravel CSRF documentation](https://laravel.com/docs/csrf#csrf-x-csrf-token) shows, log in, or correct `drag_drop.upload_url`.

## The checklist button in the toolbar opens a 404

**Cause.** The button opens `checklist_url`, by default `/darvis/filemanager-checklist`. That page only exists when `demo_routes` is on.

**Fix.** Locally, set `FLUX_FILEMANAGER_DEMO_ROUTES=true`. In production, use a [custom toolbar](configuration.md#a-custom-toolbar) without the button, or point `checklist_url` at a page of your own.

## The demo pages return a 404

**Cause.** `FLUX_FILEMANAGER_DEMO_ROUTES` is not `true`, or the config is cached with the old value.

**Fix.** Set it in `.env` and run `php artisan config:clear`.

## The height of the editor does not change with rows

**Cause.** `rows` is printed as an HTML attribute, and Flux's editor does not read it.

**Fix.** Set the height with a class, as shown in [Configuration](configuration.md#the-component-attributes).

## The saved content is very large, or saving fails with a database error

**Cause.** Dropped and pasted images are stored as base64 text inside the HTML by default.

**Fix.** Use a `longText` column, and set `drag_drop.method` to `upload` so that the HTML holds a URL. See [Drag and drop](drag-and-drop.md).

## An update of the package did not change the toolbar or the modals' texts

**Cause.** You published the views or the language files. A published view in `resources/views/vendor/flux-filemanager` replaces the package's view, so a newer version of the package does not reach it. A published language file overrides the package's texts key by key.

**Fix.** Delete the published files you did not change. Compare the others with the package's version after an update.

## Still stuck

Open an [issue](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/issues) with the output of `php artisan flux-filemanager:check` and the first error in the browser console.
