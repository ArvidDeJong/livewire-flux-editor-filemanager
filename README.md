# Flux Filemanager

Laravel Filemanager integration for Flux TipTap Editor with powerful image and file management.

## Features

- 🖼️ **Image Upload** - Upload and insert images via Laravel Filemanager
- 🎯 **Drag & Drop** - Drag images directly into the editor
- 📋 **Paste Images** - Paste screenshots and copied images
- 📐 **Image Resize** - Quick resize menu (25%, 50%, 75%, 100%, custom)
- 🎯 **Image Alignment** - Left, center, right alignment
- ✏️ **Image Editing** - Edit alt text, title, size, alignment, classes, and styles
- 🔗 **File Links** - Add links to PDFs, documents, and other files
- 🎨 **Custom Styling** - Add CSS classes and inline styles
- 🌍 **Multilingual** - Dutch, English, and German support
- ⚡ **Flux UI** - Seamless Livewire Flux integration

## Requirements

- PHP 8.2+
- Laravel 11+ or 12+ or 13+
- Livewire 3+ or 4+
- Flux UI with Flux Pro
- Laravel Filemanager

## Quick Start

### 1. Install Package

```bash
composer require darvis/livewire-flux-editor-filemanager
```

### 2. Run Automated Installation

```bash
php artisan flux-filemanager:install
```

For CI/non-interactive environments:

```bash
php artisan flux-filemanager:install --no-interaction
```

This interactive command will:

- ✅ Install Laravel Filemanager
- ✅ Publish configurations and assets
- ✅ Enable standard LFM routes (`/filemanager`) automatically
- ✅ Create storage directories
- ✅ Install NPM dependencies
- ✅ Configure `resources/js/app.js` automatically
- ✅ Build assets

### 3. Configure Routes

Use standard Laravel Filemanager package routes via `config/lfm.php`:

- `use_package_routes` => `true`
- `url_prefix` => `filemanager`

The installer configures this automatically.

### 4. Add to Your JavaScript

Add to `resources/js/app.js`:

```javascript
import { initLaravelFilemanager } from "../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js";
import Link from "@tiptap/extension-link";
import Image from "@tiptap/extension-image";
import "../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css";
import "../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css";

// See examples/app.js for the Flux-safe extension registration block.

initLaravelFilemanager();
```

When you run `php artisan flux-filemanager:install`, this setup (including Flux-safe extension registration) is added to `resources/js/app.js` automatically.

Then build:

```bash
npm run build
```

> **📖 For detailed installation with TipTap configuration, see [docs/INSTALLATION.md](docs/INSTALLATION.md)**

## Usage

### Basic Usage

```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor wire:model="content" />
    <flux:error name="content" />
</flux:field>
```

### Toolbar Presets

```blade
{{-- Minimal toolbar --}}
<x-flux-filemanager-editor wire:model="description" toolbar="minimal" />

{{-- Full toolbar --}}
<x-flux-filemanager-editor wire:model="content" toolbar="full" />

{{-- Custom toolbar --}}
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.image />
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

### Display Content

```blade
<div class="prose max-w-none">
    {!! $page->content !!}
</div>
```

## Advanced Features

### 🎯 Drag & Drop and Paste

- Drag images directly into the editor
- Paste screenshots with `Cmd/Ctrl + V`
- Automatic base64 conversion

[Read more →](docs/DRAG-DROP.md)

### 🔗 File Links

- Add links to PDFs, Word docs, Excel, ZIP files
- Configure link text, target, CSS classes, and styles
- Click file link button (🔗) in toolbar

[Read more →](docs/FILE-UPLOAD.md)

### ✏️ Image Editing

- **Single click** - Quick resize menu (25%, 50%, 75%, 100%, custom)
- **Double click** - Full edit modal (alt, title, alignment, classes, styles)

[Read more →](docs/IMAGE-EDITING.md)

### 🌍 Localization

- Built-in: Dutch, English, German
- Easy to add new languages
- Automatic locale detection

[Read more →](docs/LOCALIZATION.md)

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=flux-filemanager-config
```

Customize in `config/flux-filemanager.php`:

- Filemanager URL
- Popup dimensions
- Resize presets
- Error messages

[See full configuration options →](docs/INSTALLATION.md#configuration)

## Documentation

- [Installation Guide](docs/INSTALLATION.md) - Detailed installation with TipTap configuration
- [File Upload & Links](docs/FILE-UPLOAD.md) - Add downloadable file links
- [Image Editing](docs/IMAGE-EDITING.md) - Resize, align, and style images
- [Drag & Drop](docs/DRAG-DROP.md) - Drag and paste images
- [Localization](docs/LOCALIZATION.md) - Multi-language support
- [Workflow](docs/WORKFLOW.md) - Technical implementation details

## Examples

Complete working examples in [`examples/`](examples/) directory:

- `app.js` - Full TipTap configuration
- `EditorDemo.php` - Livewire component
- `editor-demo.blade.php` - Blade view

Quick package pages:

- Demo: `/darvis/editor-demo`
- Checklist: `/darvis/filemanager-checklist`

## Testing

```bash
composer test
```

## Author

**Arvid de Jong**  
Email: info@arvid.nl

## License

MIT
