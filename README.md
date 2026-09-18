# darvis/livewire-flux-editor-filemanager

[![Latest version](https://img.shields.io/packagist/v/darvis/livewire-flux-editor-filemanager.svg)](https://packagist.org/packages/darvis/livewire-flux-editor-filemanager)
[![Tests](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/actions/workflows/tests.yml/badge.svg)](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/actions/workflows/tests.yml)
[![Total downloads](https://img.shields.io/packagist/dt/darvis/livewire-flux-editor-filemanager.svg)](https://packagist.org/packages/darvis/livewire-flux-editor-filemanager)
[![PHP version](https://img.shields.io/packagist/dependency-v/darvis/livewire-flux-editor-filemanager/php.svg)](https://packagist.org/packages/darvis/livewire-flux-editor-filemanager)
[![License](https://img.shields.io/packagist/l/darvis/livewire-flux-editor-filemanager.svg)](LICENSE)

**Laravel Filemanager** inside the **Flux Pro editor**. Two toolbar buttons open the file manager to insert images and file links, images get a resize and align menu and an edit modal, and image files can be dropped on the editor or pasted from the clipboard.

![The Flux editor with the image button, a selected image with its resize menu, and a file link](https://arviddejong.github.io/livewire-flux-editor-filemanager/assets/images/social-preview.png)

## Features

- 🖼️ Image button and file link button that open Laravel Filemanager in a popup, for every Flux editor on the page
- 📐 Single click on an image: resize presets, a custom percentage, and left, center and right alignment
- ✏️ Double click on an image: alt text, title, width, alignment, extra CSS classes and inline styles
- 🔗 File links with text, target, classes and styles; click any link to edit it
- 📋 Drag and drop and paste of image files, embedded as base64 or uploaded to Laravel Filemanager, with size and type limits
- 🧱 Clean HTML output: `<img>` and `<a>` tags with attributes, no shortcodes
- 🌍 English, Dutch and German, in the Blade tooltips and the JavaScript modals
- ⚙️ An installer that sets up Laravel Filemanager, the npm packages and your `app.js`
- 🤖 Laravel Boost guideline and skill included

## Requirements

PHP 8.2+, Laravel 11, 12 or 13, Livewire 3 or 4, Flux Pro 2 and Laravel Filemanager 2. The editor is a Flux Pro component, so your application needs the Flux composer repository and a licence.

## Installation

```bash
composer require darvis/livewire-flux-editor-filemanager
php artisan flux-filemanager:install
```

The installer publishes the Laravel Filemanager config, enables its routes at `/filemanager`, creates the storage link, installs the TipTap packages, publishes `config/flux-filemanager.php`, adds the setup to `resources/js/app.js` and builds. Add `--no-interaction` to accept every step. Then protect the file manager in `config/lfm.php`:

```php
'middlewares' => ['web', 'auth'],
```

## Quick start

```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor wire:model="content" />
    <flux:error name="content" />
</flux:field>
```

Toolbar presets: `toolbar="minimal"`, `toolbar="full"`, or `:toolbar="false"` with your own `<flux:editor.toolbar>` in the slot. Every other attribute goes to `<flux:editor>`.

Show the content like any editor HTML. The package does not sanitise it, so give the editor to trusted users only:

```blade
<div class="prose max-w-none">
    {!! $page->content !!}
</div>
```

To try it before wiring it into your own views, set `FLUX_FILEMANAGER_DEMO_ROUTES=true` locally and open `/darvis/editor-demo`.

## Documentation

Full documentation: **https://arviddejong.github.io/livewire-flux-editor-filemanager/**

- [Installation](docs/installation.md): the installer, the manual steps, protecting the file manager, the demo pages, troubleshooting
- [Configuration](docs/configuration.md): every option, the component attributes, views and translations
- [Image editing](docs/image-editing.md): the resize menu, the edit modal and the HTML they produce
- [File links](docs/file-links.md): insert and edit links to files
- [Drag and drop](docs/drag-and-drop.md): base64 or upload, limits, and what to check when it doesn't work
- [Localization](docs/localization.md)
- [How it works](docs/how-it-works.md): the JavaScript contract, for debugging and extending
- [FAQ](https://arviddejong.github.io/livewire-flux-editor-filemanager/faq.html)

## Contributing and security

See [CONTRIBUTING.md](CONTRIBUTING.md). Found a security problem? Please report it privately, see [SECURITY.md](SECURITY.md).

## Development

```bash
composer test      # Pest
composer lint      # Pint
composer analyse   # Larastan
```

Installing the dependencies needs a Flux Pro licence: `composer config http-basic.composer.fluxui.dev your-email your-license-key`.

## License

MIT © Arvid de Jong (info@arvid.nl)
