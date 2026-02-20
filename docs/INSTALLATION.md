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

Use standard Laravel Filemanager package routes in `config/lfm.php`:

```php
'use_package_routes' => true,
'middlewares' => ['web', 'auth'],
'url_prefix' => 'filemanager',
```

> If you use `php artisan flux-filemanager:install`, these settings are configured automatically.

**Custom auth guard?** Replace `['auth']` with `['auth:staff']` or your guard name.

**Need more security?** Add additional middleware:

```php
Route::prefix('filemanager')->middleware([
    'auth:staff',
    \App\Http\Middleware\EnsureStaffEmailIsVerified::class,
])->group(function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

### 4. Environment Configuration

No package-specific `FILEMANAGER_*` environment variables are required for the default setup.

Use standard LFM defaults:

- Filemanager URL: `/filemanager`
- Upload endpoint: `/filemanager/upload`
- Drag & drop method: `base64`
- Max file size: `5242880`

Set `APP_URL` to the actual host/port you use locally.

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
npm install @tiptap/core @tiptap/extension-image @tiptap/extension-link
```

> Use consistent TipTap major versions across all TipTap packages in your project.

### 7. Initialize Package in app.js

Add to your `resources/js/app.js`:

```javascript
import { initLaravelFilemanager } from "../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js";
import Link from "@tiptap/extension-link";
import Image from "@tiptap/extension-image";
import "../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css";
import "../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css";

// See examples/app.js for the Flux-safe extension registration block.

initLaravelFilemanager();
```

Use the Flux-safe extension registration block from `examples/app.js`.

> If you use `php artisan flux-filemanager:install`, this `resources/js/app.js` setup is added automatically.

### 8. Build Assets

```bash
npm run build
```

### 9. Use in Your Application

```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-filemanager-editor id="content-editor" wire:model="content" rows="12" />
    <flux:error name="content" />
</flux:field>
```

See [Usage Examples](../examples/README.md) for more examples.

## Local Package Development (Symlink)

If you maintain this package locally and want instant changes in your Laravel app:

```bash
ln -sfn ../../../Packages/livewire-flux-editor-filemanager vendor/darvis/livewire-flux-editor-filemanager
```

Confirm the symlink:

```bash
ls -la vendor/darvis | grep livewire-flux-editor-filemanager
```

## Troubleshooting

### Filemanager Popup Blocked

If the filemanager popup is blocked by the browser:

1. Allow popups for your domain
2. Check browser console for errors
3. Verify `/filemanager` routes are available and authenticated

### Images Not Uploading

1. Check Laravel Filemanager is properly installed
2. Verify routes are configured with correct middleware
3. Check file permissions on storage directories
4. Review browser console for JavaScript errors

### Drag & Drop Not Working

1. Ensure NPM packages are installed: `@tiptap/extension-image` and `@tiptap/extension-link`
2. Verify `app.js` includes drag & drop configuration
3. Run `npm run build` after changes
4. Hard refresh browser (Cmd/Ctrl + Shift + R)

### Upload Method Not Working

1. Update `config/flux-filemanager.php`: set `drag_drop.method` to `upload`
2. Clear config cache: `php artisan config:clear`
3. Verify `/filemanager/upload` exists and is accessible
4. Check browser console for upload errors

## Next Steps

- [Usage Examples](../examples/README.md)
- [Image Editing Features](IMAGE-EDITING.md)
- [Drag & Drop Documentation](DRAG-DROP.md)
- [Localization Guide](LOCALIZATION.md)
