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

Add Laravel Filemanager routes to `routes/web.php`:

```php
Route::prefix('cms/laravel-filemanager')->middleware(['auth'])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

**Custom auth guard?** Replace `['auth']` with `['auth:staff']` or your guard name.

**Need more security?** Add additional middleware:

```php
Route::prefix('cms/laravel-filemanager')->middleware([
    'auth:staff',
    \App\Http\Middleware\EnsureStaffEmailIsVerified::class,
])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
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
import { initLaravelFilemanager } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'
import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'

// Initialize Laravel Filemanager
initLaravelFilemanager()
```

**For complete TipTap configuration with Image and Link extensions, drag & drop, and paste support:**

See the complete working example in [`examples/app.js`](../examples/app.js) which includes:
- Image extension with all attributes (width, alt, title, alignment, classes, styles)
- Link extension with target, class, and style support
- Drag & drop handler for images
- Paste handler for clipboard images
- Full ProseMirror plugin configuration

### 8. Build Assets

```bash
npm run build
```

### 9. Use in Your Application

```blade
<x-flux-filemanager-editor wire:model="content" />
```

See [Usage Examples](../examples/README.md) for more examples.

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
