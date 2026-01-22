# Installation Guide

Complete installation guide for the Flux Filemanager Editor package.

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Livewire 3.0+ or 4.0+
- Flux UI (with Flux Pro for Editor component)
- Laravel Filemanager (UniSharp)

## Step-by-Step Installation

### 1. Install Package

```bash
composer require darvis/livewire-flux-editor-filemanager
```

The package will auto-register via Laravel's package discovery.

### 2. Install Laravel Filemanager

```bash
composer require unisharp/laravel-filemanager
```

Publish Laravel Filemanager assets and config:

```bash
php artisan vendor:publish --tag=lfm_config
php artisan vendor:publish --tag=lfm_public
```

### 3. Configure Routes

Add Laravel Filemanager routes to your application. You have several options:

#### Option A: Basic Auth (Default Guard)

Add to `routes/web.php`:

```php
Route::prefix('cms/laravel-filemanager')->middleware(['auth'])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

#### Option B: Custom Auth Guard

If you use a custom guard (e.g., `auth:staff`):

```php
Route::prefix('cms/laravel-filemanager')->middleware(['auth:staff'])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

#### Option C: Multiple Middleware

For additional security (email verification, permissions, etc.):

```php
Route::prefix('cms/laravel-filemanager')->middleware([
    'auth:staff',
    \App\Http\Middleware\EnsureStaffEmailIsVerified::class,
    \App\Http\Middleware\CheckPermission::class,
])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

#### Option D: Separate Route File

Create `routes/cms.php` for admin routes:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('cms/laravel-filemanager')->middleware(['auth:staff'])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

// Other CMS routes...
```

Register in `bootstrap/app.php` (Laravel 11+):

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('web')
            ->group(base_path('routes/cms.php'));
    }
)
```

### 4. Environment Configuration

Add to your `.env` file:

```env
# Laravel Filemanager URL (must match route prefix)
FILEMANAGER_URL=/cms/laravel-filemanager

# Drag & Drop Settings
FILEMANAGER_DRAG_DROP_METHOD=base64  # or 'upload'
FILEMANAGER_MAX_FILE_SIZE=5242880    # 5MB in bytes

# Upload endpoint (only for 'upload' method)
FILEMANAGER_UPLOAD_URL=/cms/laravel-filemanager/upload
```

**Important:** The `FILEMANAGER_URL` must match your route prefix exactly!

### 5. Publish Package Configuration (Optional)

```bash
php artisan vendor:publish --tag=flux-filemanager-config
```

This creates `config/flux-filemanager.php` where you can customize:

- Laravel Filemanager URL
- Popup window dimensions
- Image resize presets
- Custom width range
- Drag & drop settings
- Allowed image types
- Error messages

### 6. Install NPM Dependencies

```bash
npm install @tiptap/extension-image @tiptap/extension-link prosemirror-state
```

### 7. Configure TipTap Extensions

Add to your `resources/js/app.js`:

```javascript
import { Image } from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import { Plugin, PluginKey } from 'prosemirror-state'
import { initLaravelFilemanager } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'
import { getDragDropConfig, processImageFile } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/drag-drop-config.js'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'

// Register extensions
document.addEventListener('flux:editor', (e) => {
    if (e.detail?.registerExtension) {
        // Link extension
        e.detail.registerExtension(Link.configure({
            openOnClick: false,
        }).extend({
            addAttributes() {
                return {
                    ...this.parent?.(),
                    target: {
                        default: null,
                        parseHTML: element => element.getAttribute('target'),
                        renderHTML: attributes => attributes.target ? { target: attributes.target } : {},
                    },
                    class: {
                        default: null,
                        parseHTML: element => element.getAttribute('class'),
                        renderHTML: attributes => attributes.class ? { class: attributes.class } : {},
                    },
                    style: {
                        default: null,
                        parseHTML: element => element.getAttribute('style'),
                        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
                    },
                }
            },
        }))

        // Image extension with drag & drop
        e.detail.registerExtension(Image.extend({
            addAttributes() {
                return {
                    ...this.parent?.(),
                    width: {
                        default: null,
                        parseHTML: element => element.getAttribute('width') || element.style.width,
                        renderHTML: attributes => attributes.width ? { width: attributes.width } : {},
                    },
                    style: {
                        default: null,
                        parseHTML: element => element.getAttribute('style'),
                        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
                    },
                    'data-align': {
                        default: null,
                        parseHTML: element => element.getAttribute('data-align'),
                        renderHTML: attributes => attributes['data-align'] ? { 'data-align': attributes['data-align'] } : {},
                    },
                    class: {
                        default: 'tiptap-image',
                        parseHTML: element => element.getAttribute('class'),
                        renderHTML: attributes => ({ class: attributes.class }),
                    },
                    alt: {
                        default: null,
                        parseHTML: element => element.getAttribute('alt'),
                        renderHTML: attributes => attributes.alt ? { alt: attributes.alt } : {},
                    },
                    title: {
                        default: null,
                        parseHTML: element => element.getAttribute('title'),
                        renderHTML: attributes => attributes.title ? { title: attributes.title } : {},
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
                                    
                                    const editorElement = view.dom.closest('ui-editor')
                                    const config = getDragDropConfig(editorElement)
                                    
                                    imageFiles.forEach(async (file) => {
                                        try {
                                            const src = await processImageFile(file, config)
                                            
                                            const { schema } = view.state
                                            const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY })
                                            
                                            const node = schema.nodes.image.create({
                                                src: src,
                                                class: 'tiptap-image',
                                            })
                                            
                                            const transaction = view.state.tr.insert(coordinates.pos, node)
                                            view.dispatch(transaction)
                                        } catch (error) {
                                            console.error('Failed to process image:', error)
                                            alert(error.message || 'Failed to process image')
                                        }
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
                                
                                const editorElement = view.dom.closest('ui-editor')
                                const config = getDragDropConfig(editorElement)
                                
                                imageItems.forEach(async (item) => {
                                    const file = item.getAsFile()
                                    if (!file) return
                                    
                                    try {
                                        const src = await processImageFile(file, config)
                                        
                                        const { schema } = view.state
                                        
                                        const node = schema.nodes.image.create({
                                            src: src,
                                            class: 'tiptap-image',
                                        })
                                        
                                        const transaction = view.state.tr.replaceSelectionWith(node)
                                        view.dispatch(transaction)
                                    } catch (error) {
                                        console.error('Failed to process image:', error)
                                        alert(error.message || 'Failed to process image')
                                    }
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

// Initialize Laravel Filemanager
initLaravelFilemanager()
```

See [`examples/app.js`](../examples/app.js) for the complete example.

### 8. Build Assets

```bash
npm run build
```

### 9. Use in Livewire Components

```php
use Livewire\Component;

class PageEdit extends Component
{
    public string $content = '';

    public function render()
    {
        return view('livewire.page-edit');
    }
}
```

```blade
<x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="20" />
```

## Troubleshooting

### Filemanager Popup Blocked

If the filemanager popup is blocked by the browser:

1. Allow popups for your domain
2. Check browser console for errors
3. Verify the `FILEMANAGER_URL` matches your route prefix

### Images Not Uploading

1. Check Laravel Filemanager is properly installed
2. Verify routes are configured with correct middleware
3. Check file permissions on storage directories
4. Review browser console for JavaScript errors

### Drag & Drop Not Working

1. Ensure NPM packages are installed: `prosemirror-state`
2. Verify `app.js` includes drag & drop configuration
3. Run `npm run build` after changes
4. Hard refresh browser (Cmd/Ctrl + Shift + R)

### Upload Method Not Working

1. Check `.env` configuration: `FILEMANAGER_DRAG_DROP_METHOD=upload`
2. Clear config cache: `php artisan config:clear`
3. Verify upload endpoint exists and is accessible
4. Check browser console for upload errors

## Next Steps

- [Usage Examples](../examples/README.md)
- [Image Editing Features](IMAGE-EDITING.md)
- [Drag & Drop Documentation](DRAG-DROP.md)
- [Localization Guide](LOCALIZATION.md)
