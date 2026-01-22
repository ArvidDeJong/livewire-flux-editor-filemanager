# Flux Filemanager

Laravel Filemanager integration for Flux TipTap Editor with image resize and align functionality.

## Features

- 🖼️ **Image Upload** - Upload and insert images via Laravel Filemanager
- 🎯 **Drag & Drop** - Drag images from your computer directly into the editor
- 📋 **Paste Images** - Paste images from clipboard (screenshots, copied images)
- 📐 **Image Resize** - Quick resize menu with preset sizes (25%, 50%, 75%, 100%)
- 🎯 **Image Alignment** - Left, center, right alignment options
- ✏️ **Image Editing** - Complete modal for editing alt text, title, size, alignment, classes, and styles
- 🔗 **File Links** - Add links to PDFs, documents, and other files
- 🎨 **Custom Styling** - Add CSS classes and inline styles to images and links
- 🌍 **Multilingual** - Built-in support for Dutch, English, and German
- ⚡ **Flux UI** - Seamless integration with Livewire Flux design system
- ✅ **WYSIWYG** - Images immediately visible in editor  
- ✅ **Native TipTap** - Uses standard Image extension  
- ✅ **Reusable Component** - `<x-flux-filemanager-editor>` with multiple toolbar presets  
- ✅ **KISS & DRY** - Simple, maintainable code without duplication

## Examples

Complete example files are available in the [`examples/`](examples/) directory:
- `app.js` - Full TipTap configuration with drag & drop
- `EditorDemo.php` - Simple demo Livewire component
- `editor-demo.blade.php` - Simple demo Blade view

See [examples/README.md](examples/README.md) for detailed usage instructions.

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Livewire 3.0+ or 4.0+
- Flux UI (with Flux Pro for Editor component)
- Laravel Filemanager (UniSharp)

## Installation

> **📖 For detailed installation instructions, see [docs/INSTALLATION.md](docs/INSTALLATION.md)**

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

```bash
composer require darvis/livewire-flux-editor-filemanager
```

### 2. Install Laravel Filemanager

Follow the [Laravel Filemanager installation guide](https://unisharp.github.io/laravel-filemanager/installation).

### 3. Configure Routes

Add Laravel Filemanager routes to your `routes/web.php` or separate route file:

**Basic Setup (Default Auth):**
```php
Route::prefix('cms/laravel-filemanager')->middleware(['auth'])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

**Custom Auth Guard:**
```php
Route::prefix('cms/laravel-filemanager')->middleware(['auth:staff'])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

**Multiple Middleware:**
```php
Route::prefix('cms/laravel-filemanager')->middleware([
    'auth:staff',
    \App\Http\Middleware\EnsureStaffEmailIsVerified::class
])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

**Important:** The route prefix must match the `FILEMANAGER_URL` in your `.env` file:
```env
FILEMANAGER_URL=/cms/laravel-filemanager
```

### 4. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=flux-filemanager-config
```

This will create `config/flux-filemanager.php` where you can customize:
- Laravel Filemanager URL (must match your route prefix)
- Popup window dimensions
- Image resize presets
- Custom width range
- Drag & drop settings (base64 vs server upload)
- Error messages

**Example `.env` configuration:**
```env
FILEMANAGER_URL=/cms/laravel-filemanager
FILEMANAGER_DRAG_DROP_METHOD=base64
FILEMANAGER_MAX_FILE_SIZE=5242880
```

### 5. Install NPM Dependencies

```bash
npm install @tiptap/extension-image
```

### 6. Build Assets

```javascript
import { Image } from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import { Plugin, PluginKey } from 'prosemirror-state'
import { initLaravelFilemanager } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'

// Add Image and Link extensions to Flux editor
document.addEventListener('flux:editor', (e) => {
    if (e.detail?.registerExtension) {
        // Register Link extension with target, class, and style attributes
        e.detail.registerExtension(Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                rel: 'noopener noreferrer nofollow',
            },
        }).extend({
            addAttributes() {
                return {
                    ...this.parent?.(),
                    target: {
                        default: '_blank',
                        parseHTML: element => element.getAttribute('target'),
                        renderHTML: attributes => {
                            if (!attributes.target) return {}
                            return { target: attributes.target }
                        },
                    },
                    class: {
                        default: null,
                        parseHTML: element => element.getAttribute('class'),
                        renderHTML: attributes => {
                            if (!attributes.class) return {}
                            return { class: attributes.class }
                        },
                    },
                    style: {
                        default: null,
                        parseHTML: element => element.getAttribute('style'),
                        renderHTML: attributes => {
                            if (!attributes.style) return {}
                            return { style: attributes.style }
                        },
                    },
                }
            },
        }))

        // Register Image extension with drag & drop, paste, and all attributes
        e.detail.registerExtension(Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: 'tiptap-image',
            },
        }).extend({
            addAttributes() {
                return {
                    ...this.parent?.(),
                    width: {
                        default: null,
                        parseHTML: element => element.getAttribute('width') || element.style.width,
                        renderHTML: attributes => {
                            if (!attributes.width) return {}
                            return {
                                width: attributes.width,
                                style: `width: ${attributes.width}`
                            }
                        },
                    },
                    style: {
                        default: null,
                        parseHTML: element => element.getAttribute('style'),
                        renderHTML: attributes => {
                            if (!attributes.style) return {}
                            return { style: attributes.style }
                        },
                    },
                    'data-align': {
                        default: null,
                        parseHTML: element => element.getAttribute('data-align'),
                        renderHTML: attributes => {
                            if (!attributes['data-align']) return {}
                            return { 'data-align': attributes['data-align'] }
                        },
                    },
                    class: {
                        default: 'tiptap-image',
                        parseHTML: element => element.getAttribute('class'),
                        renderHTML: attributes => {
                            if (!attributes.class) return {}
                            return { class: attributes.class }
                        },
                    },
                    alt: {
                        default: null,
                        parseHTML: element => element.getAttribute('alt'),
                        renderHTML: attributes => {
                            if (!attributes.alt) return {}
                            return { alt: attributes.alt }
                        },
                    },
                    title: {
                        default: null,
                        parseHTML: element => element.getAttribute('title'),
                        renderHTML: attributes => {
                            if (!attributes.title) return {}
                            return { title: attributes.title }
                        },
                    },
                }
            },
            addProseMirrorPlugins() {
                return [
                    new Plugin({
                        key: new PluginKey('imageDrop'),
                        props: {
                            handleDrop(view, event, slice, moved) {
                                if (!moved && event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
                                    const files = Array.from(event.dataTransfer.files)
                                    const imageFiles = files.filter(file => file.type.startsWith('image/'))
                                    
                                    if (imageFiles.length === 0) return false
                                    
                                    event.preventDefault()
                                    
                                    imageFiles.forEach(file => {
                                        const reader = new FileReader()
                                        
                                        reader.onload = (e) => {
                                            const { schema } = view.state
                                            const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY })
                                            
                                            const node = schema.nodes.image.create({
                                                src: e.target.result,
                                                class: 'tiptap-image',
                                            })
                                            
                                            const transaction = view.state.tr.insert(coordinates.pos, node)
                                            view.dispatch(transaction)
                                        }
                                        
                                        reader.readAsDataURL(file)
                                    })
                                    
                                    return true
                                }
                                return false
                            },
                            handlePaste(view, event, slice) {
                                const items = Array.from(event.clipboardData?.items || [])
                                const imageItems = items.filter(item => item.type.startsWith('image/'))
                                
                                if (imageItems.length === 0) return false
                                
                                event.preventDefault()
                                
                                imageItems.forEach(item => {
                                    const file = item.getAsFile()
                                    if (!file) return
                                    
                                    const reader = new FileReader()
                                    
                                    reader.onload = (e) => {
                                        const { schema } = view.state
                                        const { selection } = view.state
                                        
                                        const node = schema.nodes.image.create({
                                            src: e.target.result,
                                            class: 'tiptap-image',
                                        })
                                        
                                        const transaction = view.state.tr.replaceSelectionWith(node)
                                        view.dispatch(transaction)
                                    }
                                    
                                    reader.readAsDataURL(file)
                                })
                                
                                return true
                            },
                        },
                    }),
                ]
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

## Drag & Drop and Paste

Add images quickly by dragging them from your computer or pasting from clipboard:
- **Drag & Drop** - Drag images directly into the editor
- **Paste** - Paste screenshots or copied images with `Cmd/Ctrl + V`
- **Base64** - Images are automatically converted to base64 data URLs

See [docs/DRAG-DROP.md](docs/DRAG-DROP.md) for detailed drag & drop documentation.

## File Links

Add links to files (PDFs, Word documents, ZIP files, etc.) via Laravel Filemanager:
1. Click the file link button (🔗) in the toolbar
2. Select a file via Laravel Filemanager
3. Configure the link (text, target, classes, styles)

See [docs/FILE-UPLOAD.md](docs/FILE-UPLOAD.md) for detailed file link documentation.

## Image Editing

The package offers two ways to edit images:
- **Single click** - Quick resize menu for fast adjustments
- **Double click** - Complete edit modal with all options (alt, title, size, alignment, classes, styles)

See [docs/IMAGE-EDITING.md](docs/IMAGE-EDITING.md) for detailed image editing documentation.

## Localization

The package supports multiple languages out of the box:
- 🇳🇱 Dutch (nl)
- 🇬🇧 English (en)
- 🇩🇪 German (de)

See [docs/LOCALIZATION.md](docs/LOCALIZATION.md) for detailed localization documentation.

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
