---
title: "Uploads and security"
nav_order: 8
description: "What darvis/livewire-flux-editor-filemanager checks and what it leaves to Laravel Filemanager and your app: access, file types, sizes, disks, HTML."
---

# Uploads and security

This package is a bridge between two other packages. It has no upload route, no controller and no storage code of its own. This page says who checks what, so you do not assume a protection that is not there.

## Who is responsible for what

| Question | Answer | Where you set it |
| --- | --- | --- |
| Who may open the file manager and upload? | Laravel Filemanager's routes, behind the middleware you list. This package adds none | `middlewares` in `config/lfm.php` |
| Which file types may be uploaded in the file manager? | Laravel Filemanager checks the MIME type on the server when `should_validate_mime` is `true` | `folder_categories.*.valid_mime`, `disallowed_mimetypes`, `disallowed_extensions` in `config/lfm.php` |
| How large may an upload be? | PHP's `upload_max_filesize` and `post_max_size`. Laravel Filemanager checks its own `max_size` only when `should_validate_size` is `true` | `php.ini` and `config/lfm.php` |
| Where are the files stored? | On the Laravel filesystem disk that Laravel Filemanager uses | `disk` in `config/lfm.php` |
| Which types and sizes may be dropped on the editor? | This package, **in the browser only** | `drag_drop.allowed_types` and `drag_drop.max_file_size` in `config/flux-filemanager.php` |
| Is the HTML from the editor safe to print? | Nobody checks that. Your application decides | Your own code |

The Laravel Filemanager defaults named on this page are those of version 2.15. Open your own `config/lfm.php` to see what applies to you.

## Access: the package adds no authentication

The editor buttons open `/filemanager` in a popup. Whether that URL answers is up to Laravel Filemanager's `middlewares`:

```php
'middlewares' => ['web', 'auth'],
```

- Without `auth` in that list, the file manager is a public upload endpoint. `php artisan flux-filemanager:check` reports that as a failure with the text `The file manager is behind authentication`.
- That check looks for the word `auth`, or a value starting with `auth:`, in `lfm.middlewares`. It does not send a request and it does not know your own middleware. If you protect the routes with a middleware of another name, the failure is a false alarm.
- `auth` lets in every logged-in user. On a site where visitors can create an account, a visitor can then open `/filemanager` and upload. Add a middleware that only lets your editors through.
- By default Laravel Filemanager gives each user a private folder named after the user ID, and a shared folder `shares` that every user can read and write (`allow_private_folder`, `allow_shared_folder`).

## File types and sizes

Inside the file manager popup, Laravel Filemanager validates the upload on the server. Its defaults:

- Images: `image/jpeg`, `image/pjpeg`, `image/png`, `image/gif`
- Files: the same, plus `application/pdf`. `text/plain` is listed too, but `disallowed_mimetypes` refuses it. Word, Excel and ZIP files are refused until you add their MIME types
- `should_validate_mime` is `true`
- `should_validate_size` is `false`, so `max_size` (50000 KB) is **not** enforced until you set it to `true`
- The extensions `php` and `html` are refused

For images dropped on or pasted into the editor, this package compares the file with `drag_drop.allowed_types` and `drag_drop.max_file_size`. That comparison runs in JavaScript, on the type the browser reports. Treat it as a hint for editors:

- With the `base64` method the image never passes a server-side file check at all. It arrives as text inside the HTML of your form field. Limit the size of that field with a validation rule such as `max:` if you need to.
- With the `upload` method the file goes to Laravel Filemanager's upload route, which applies the rules above. Someone who can reach that route can post to it without the editor, so only `config/lfm.php` counts.
- The package does not look inside files. SVG is in the default `allowed_types`; an SVG file can contain scripts, and the package does not clean it.

## Storage and visibility

With Laravel Filemanager's default disk, `public`, files land in `storage/app/public` and are served from `public/storage` through the symlink that `php artisan storage:link` creates. The installer creates the folders `photos` and `files` there.

Such a file is a public URL. The web server sends it to anyone who has the address, without a login and without passing through Laravel. There is no authorisation per file. Do not put documents in the file manager that only some people may read.

The package never reads, moves or deletes uploaded files. At runtime it only passes URLs from the file manager to the editor. The one change it makes to a URL is described in [Image editing](image-editing.md#which-url-ends-up-in-src).

## The HTML is not sanitised

The editor stores HTML. Editors can set any class and any inline style on images and links, and pasted images add long base64 strings. The package stores none of it and cleans none of it: your Livewire component receives the string and your view prints it.

- Give the editor only to people you trust, or
- run the HTML through a sanitiser before you print it with `{!! !!}`.

Validate the field like any other input. `required|string` checks that it is text, not that it is safe.

## The demo pages

`/darvis/editor-demo` and `/darvis/filemanager-checklist` have no authentication and are registered only when `demo_routes` is `true`. The checklist page shows details of your installation, and the demo page prints what you type as unescaped HTML in your own browser. Use them locally and leave `FLUX_FILEMANAGER_DEMO_ROUTES` out of production.

## Report a vulnerability

See [SECURITY.md](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/blob/main/SECURITY.md). Please do not open a public issue.
