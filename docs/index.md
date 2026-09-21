---
title: "Home"
nav_order: 1
description: "Laravel package that puts Laravel Filemanager in the Flux Pro editor for Livewire: insert images and file links, resize and align images, drop or paste images."
permalink: /
---

# Flux Editor Filemanager

`darvis/livewire-flux-editor-filemanager` is a Laravel package that connects [Laravel Filemanager](https://github.com/UniSharp/laravel-filemanager) (a file browser and uploader, `unisharp/laravel-filemanager`) to the rich text editor of [Flux Pro](https://fluxui.dev/components/editor) in a Livewire application. It adds an image button and a file link button to the editor toolbar, a resize and align menu and an edit modal for images, and drop and paste of image files.

![The Flux editor with the image button, a selected image with its resize menu, and a file link](assets/images/social-preview.png)

## Who it is for

Laravel developers who already use the Flux Pro editor, or plan to, and want their editors to pick images and documents from a file manager instead of typing URLs.

## What it does not do

- It does not upload, store, crop or serve files. Laravel Filemanager does that, with its own routes and its own `config/lfm.php`.
- It does not decide who may use the file manager. That is the `middlewares` setting in `config/lfm.php`.
- It does not check uploads on the server. The size and type limits for dropped images are checked in the browser only.
- It does not sanitise the HTML your editors write.
- It does not include the editor itself. The editor is a paid Flux Pro component.

[Uploads and security](uploads-and-security.md) lists exactly what is checked, and where.

## Requirements

- PHP 8.2 or higher
- Laravel 11, 12 or 13
- Livewire 3 or 4
- Flux Pro 2.0.2 or newer, which needs a paid licence from [fluxui.dev](https://fluxui.dev)
- Laravel Filemanager 2, installed automatically as a dependency
- Node and Vite with `resources/js/app.js`, the standard Laravel front-end setup

## Install

```bash
composer require darvis/livewire-flux-editor-filemanager
php artisan flux-filemanager:install
php artisan flux-filemanager:check
```

Then use the component where you would use `<flux:editor>`:

```blade
<x-flux-filemanager-editor wire:model="content" />
```

## Pages

- [Installation](installation.md): the Flux Pro requirement, the installer step by step, and how to check that it works
- [Your first editor](first-editor.md): one complete example, from migration to the public page
- [Configuration](configuration.md): every key in `config/flux-filemanager.php` and the component attributes
- [Image editing](image-editing.md): the image button, the resize menu, the edit modal and the HTML they write
- [File links](file-links.md): insert a link to a PDF or another file, and edit a link
- [Drag and drop](drag-and-drop.md): drop or paste images, as base64 or uploaded to Laravel Filemanager
- [Uploads and security](uploads-and-security.md): what the package checks, what Laravel Filemanager checks, and what nobody checks
- [Localization](localization.md): the language files, changing a text, adding a language
- [Testing](testing.md): test a Livewire form that uses the editor in your own application
- [Troubleshooting](troubleshooting.md): symptoms, causes and fixes, with the literal messages
- [How it works](how-it-works.md): the JavaScript and DOM contract, for debugging and extending
- [FAQ](faq.md): short answers to common questions
