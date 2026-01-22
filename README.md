# Flux Filemanager

Laravel Filemanager integration for Flux TipTap Editor with image resize and align functionality.

## Features

✅ **WYSIWYG** - Images immediately visible in editor  
✅ **Native TipTap** - Uses standard Image extension  
✅ **Image Resize** - Quick presets (25%, 50%, 75%, 100%) + custom input  
✅ **Image Align** - Left, center, right alignment  
✅ **Laravel Filemanager** - Easy image management  
✅ **Reusable Component** - `<x-flux-filemanager-editor>` with multiple toolbar presets  
✅ **KISS & DRY** - Simple, maintainable code without duplication

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Livewire 3.0+ or 4.0+
- Flux UI (with Flux Pro for Editor component)
- Laravel Filemanager (UniSharp)

## Installation

### Quick Install (Recommended for Beginners)

Use the automated installation command:

```bash
php artisan flux-filemanager:install
```

This command will:
- ✅ Install Laravel Filemanager
- ✅ Publish all configurations and assets
- ✅ Create storage directories
- ✅ Install NPM dependencies
- ✅ Build assets

The command is interactive and will ask for confirmation at each step. You can force overwrite existing files with:

```bash
php artisan flux-filemanager:install --force
```

Then follow the post-installation steps shown by the command.

### Manual Installation

If you prefer to install manually, follow these steps:

### 1. Install Laravel Filemanager

This package requires UniSharp Laravel Filemanager. Install it first:

```bash
composer require unisharp/laravel-filemanager
```

Publish the configuration and assets:

```bash
php artisan vendor:publish --tag=lfm_config
php artisan vendor:publish --tag=lfm_public
```

Add the route to your `routes/web.php`:

```php
Route::group(['prefix' => 'cms', 'middleware' => ['auth:staff']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

**Important:** Adjust the prefix and middleware to match your application's structure.

Create the storage directories:

```bash
php artisan storage:link
mkdir -p public/storage/photos
mkdir -p public/storage/files
```

For more configuration options, see the [Laravel Filemanager documentation](https://unisharp.github.io/laravel-filemanager/installation).

### 2. Install Flux Editor Filemanager Package

Add the package to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../Packages/livewire-flux-editor-filemanager"
        }
    ]
}
```

Then install:

```bash
composer require darvis/livewire-flux-editor-filemanager
```

### 3. Install NPM Dependencies

```bash
npm install @tiptap/extension-image
```

### 4. Publish Assets

```bash
php artisan vendor:publish --tag=flux-filemanager-config
php artisan vendor:publish --tag=flux-filemanager-assets
php artisan vendor:publish --tag=flux-filemanager-views
```

This will publish:
- Configuration file to `config/flux-filemanager.php`
- JavaScript and CSS to `resources/js/vendor/flux-filemanager/` and `resources/css/vendor/flux-filemanager/`
- Blade components to `resources/views/components/`

### 5. Import JavaScript & CSS

In your `resources/js/app.js`:

```javascript
import Image from '@tiptap/extension-image'
import { initLaravelFilemanager } from './vendor/flux-filemanager/laravel-filemanager'
import '../css/vendor/flux-filemanager/tiptap-image.css'

// Add Image extension to Flux editor with resize support
document.addEventListener('flux:editor', (e) => {
    if (e.detail?.registerExtension) {
        e.detail.registerExtension(Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: 'tiptap-image',
            },
        }))
    }
})

// Initialize Laravel Filemanager integration
initLaravelFilemanager()
```

### 6. Build Assets

```bash
npm run build
```

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

**Minimal toolbar:**
```blade
<x-flux-filemanager-editor wire:model="description" toolbar="minimal" :rows="6" />
```

**Full toolbar:**
```blade
<x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="20" />
```

**Custom toolbar:**
```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.image />
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

### Image Resize & Align

When you click on an image in the editor, a menu appears with:

**Resize options:**
- Quick buttons: 25%, 50%, 75%, 100%
- Custom input: Type any percentage (1-100%)

**Align options:**
- Left align
- Center align
- Right align

## Frontend Display

Simply output the content:

```blade
<div class="prose max-w-none">
    {!! $page->content !!}
</div>
```

Images are stored as HTML `<img>` tags with inline styles and work out-of-the-box.

## Configuration

After publishing the config file, you can customize settings in `config/flux-filemanager.php`:

```php
return [
    // Laravel Filemanager URL
    'url' => env('FILEMANAGER_URL', '/cms/laravel-filemanager'),
    
    // Popup dimensions
    'popup' => [
        'width' => 900,
        'height' => 600,
    ],
    
    // Image resize presets
    'resize_presets' => ['25%', '50%', '75%', '100%'],
    
    // Custom width range
    'custom_width' => [
        'min' => 1,
        'max' => 100,
    ],
    
    // Error messages
    'messages' => [
        'popup_blocked' => 'Popup was blocked...',
        'no_images_selected' => 'No images were selected.',
        'filemanager_not_found' => 'Laravel Filemanager could not be loaded...',
    ],
];
```

## Testing

Run the test suite:

```bash
composer test
```

Or with PHPUnit directly:

```bash
vendor/bin/phpunit
```

## Examples

Check the `examples/` directory for:
- Basic usage examples
- Advanced configurations
- Livewire component examples

## Documentation

For detailed implementation steps, troubleshooting, and technical details, see the [complete workflow documentation](docs/WORKFLOW.md).

## Author

**Arvid de Jong**  
Email: info@arvid.nl

## License

MIT
