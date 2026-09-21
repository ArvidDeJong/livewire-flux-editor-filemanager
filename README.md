# darvis/livewire-flux-editor-filemanager

[![Latest version](https://img.shields.io/packagist/v/darvis/livewire-flux-editor-filemanager.svg)](https://packagist.org/packages/darvis/livewire-flux-editor-filemanager)
[![Tests](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/actions/workflows/tests.yml/badge.svg)](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/actions/workflows/tests.yml)
[![PHP version](https://img.shields.io/packagist/dependency-v/darvis/livewire-flux-editor-filemanager/php.svg)](https://packagist.org/packages/darvis/livewire-flux-editor-filemanager)
[![License](https://img.shields.io/packagist/l/darvis/livewire-flux-editor-filemanager.svg)](LICENSE)

A Laravel package that connects [Laravel Filemanager](https://github.com/UniSharp/laravel-filemanager) to the rich text editor of [Flux Pro](https://fluxui.dev/components/editor) in a Livewire application. Two toolbar buttons open the file manager in a popup to insert images and file links, images get a resize and align menu and an edit modal, and image files can be dropped on the editor or pasted from the clipboard.

![The Flux editor with the image button, a selected image with its resize menu, and a file link](https://arviddejong.github.io/livewire-flux-editor-filemanager/assets/images/social-preview.png)

## Features

- An image button and a file link button that open Laravel Filemanager in a popup, for every Flux editor on the page
- Single click on an image: preset widths, a custom percentage, and left, center and right alignment
- Double click on an image: alt text, title, width, alignment, extra CSS classes and inline styles
- File links with text, target, classes and styles; click a link in the editor to edit it
- Drop and paste of image files, embedded as base64 or uploaded to Laravel Filemanager
- Plain HTML output: `<img>` and `<a>` tags with attributes, no shortcodes
- `flux-filemanager:install` sets up Laravel Filemanager, the npm packages and your `app.js`; `flux-filemanager:check` verifies the result
- Language files for English, Dutch and German, also used by the JavaScript menus and modals

## Requirements

- PHP 8.2+, Laravel 11, 12 or 13, Livewire 3 or 4
- Flux and Flux Pro 2.0.2 or newer. The editor is a Flux Pro component, and Flux Pro needs a **paid licence** from [fluxui.dev](https://fluxui.dev). Set up Flux Pro in your application first (`php artisan flux:activate`), so that Composer can reach the private Flux repository
- Laravel Filemanager 2, installed as a dependency
- Node with npm and Vite

The package adds no authentication and does not sanitise the HTML from the editor. Read [Uploads and security](https://arviddejong.github.io/livewire-flux-editor-filemanager/uploads-and-security.html) before you give the editor to users.

## Installation

```bash
composer require darvis/livewire-flux-editor-filemanager
php artisan flux-filemanager:install
php artisan flux-filemanager:check
```

Then make sure the file manager is behind a login. In `config/lfm.php`:

```php
'middlewares' => ['web', 'auth'],
```

## Quick start

In the Blade view of a Livewire component with a `public string $content = '';` property:

```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor wire:model="content" />
    <flux:error name="content" />
</flux:field>
```

The component renders `<flux:editor>` with the image and file link buttons. `$content` receives the HTML. [Your first editor](https://arviddejong.github.io/livewire-flux-editor-filemanager/first-editor.html) has the complete example, from migration to public page.

## Documentation

Full documentation: **https://arviddejong.github.io/livewire-flux-editor-filemanager/**

- [Installation](docs/installation.md): the Flux Pro requirement, the installer step by step, and how to check that it works
- [Your first editor](docs/first-editor.md): one complete example, from migration to the public page
- [Configuration](docs/configuration.md): every config key and the component attributes
- [Image editing](docs/image-editing.md): the resize menu, the edit modal and the HTML they write
- [File links](docs/file-links.md): insert and edit links to files
- [Drag and drop](docs/drag-and-drop.md): base64 or upload, and where the limits are checked
- [Uploads and security](docs/uploads-and-security.md): what the package checks and what it leaves to Laravel Filemanager and your application
- [Localization](docs/localization.md): the language files, changing a text, adding a language
- [Testing](docs/testing.md): test a Livewire form that uses the editor
- [Troubleshooting](docs/troubleshooting.md): symptoms, causes and fixes, with the literal messages
- [How it works](docs/how-it-works.md): the JavaScript and DOM contract
- [FAQ](https://arviddejong.github.io/livewire-flux-editor-filemanager/faq.html): short answers to common questions

## Laravel Boost

The package ships a guideline and a skill for [Laravel Boost](https://github.com/laravel/boost). Run `php artisan boost:install`, or `php artisan boost:update --discover` in a project that already uses Boost.

## Testing

```bash
composer test      # Pest
composer lint      # Pint, check only
composer format    # Pint, fixes
composer analyse   # Larastan
```

Installing the development dependencies needs a Flux Pro licence: `composer config http-basic.composer.fluxui.dev your-email your-license-key`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

Found a security problem? Report it privately, see [SECURITY.md](SECURITY.md).

## License

MIT. See [LICENSE](LICENSE).
