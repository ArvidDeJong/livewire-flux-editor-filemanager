# Examples

This directory contains example files showing how to use the Flux Filemanager Editor package in your Laravel application.

## Files

### JavaScript Configuration

**`app.js`** - Complete TipTap extension setup
- Image extension with drag & drop and paste support
- Link extension with target, class, and style attributes
- All image attributes (width, style, align, class, alt, title)
- Laravel Filemanager integration

### Simple Demo

**`EditorDemo.php`** - Minimal Livewire component
- Simple demo without database persistence
- Just the editor with save button
- Perfect for testing and learning

**`editor-demo.blade.php`** - Simple demo view
- Minimal layout with editor and preview
- No complex forms or validation
- Quick start example

## Quick Start

### 1. Copy JavaScript Configuration

Copy `app.js` to your `resources/js/app.js` and install dependencies:

```bash
npm install @tiptap/extension-image @tiptap/extension-link prosemirror-state
npm run build
```

### 2. Create Demo Component

```bash
php artisan make:livewire EditorDemo
```

Copy content from `examples/EditorDemo.php` to `app/Livewire/EditorDemo.php`

Copy `examples/editor-demo.blade.php` to `resources/views/livewire/editor-demo.blade.php`

### 3. Add Route

```php
use App\Livewire\EditorDemo;

Route::get('/editor-demo', EditorDemo::class);
```

### 4. Visit the Demo

Navigate to `/editor-demo` in your browser and start testing!

## Customization

### Toolbar Configurations

The package supports three toolbar presets:

**Full Toolbar (Default):**
```blade
<x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="20" />
```

**Minimal Toolbar:**
```blade
<x-flux-filemanager-editor wire:model="content" toolbar="minimal" :rows="6" />
```

**Custom Toolbar:**
```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.image />
        <flux:editor.file-link />
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

### Image Features

**Upload via Filemanager:**
- Click the image button (🖼️) in the toolbar
- Select image from Laravel Filemanager
- Image is inserted at cursor position

**Drag & Drop:**
- Drag images from your computer
- Drop them directly into the editor
- Images are converted to base64

**Paste:**
- Copy an image or take a screenshot
- Paste with `Cmd/Ctrl + V`
- Image is inserted at cursor position

**Resize:**
- Single click on image → Quick resize menu
- Choose 25%, 50%, 75%, 100%, or custom size

**Edit:**
- Double click on image → Complete edit modal
- Edit alt text, title, size, alignment, classes, styles

### File Links

**Add File Link:**
- Click the file link button (🔗) in the toolbar
- Select file from Laravel Filemanager
- Configure link text, target, classes, styles

**Edit File Link:**
- Click on existing link in editor
- Modal opens with current values
- Update and save changes

## Output

The editor stores content as HTML:

```html
<h2>Welcome to Our Website</h2>

<p>This is a paragraph with <strong>bold</strong> and <em>italic</em> text.</p>

<img 
    class="tiptap-image align-center rounded-lg" 
    src="https://example.com/storage/photos/image.jpg" 
    width="75%" 
    style="width: 75%; margin-left: auto; margin-right: auto; display: block;" 
    alt="Product photo"
    title="Our latest product"
    data-align="center"
>

<p>Download our <a href="/storage/files/brochure.pdf" target="_blank" rel="noopener noreferrer nofollow" class="text-blue-600 underline">product brochure (PDF)</a>.</p>
```

## Displaying Content

In your frontend views:

```blade
<div class="prose max-w-none">
    {!! $page->content !!}
</div>
```

The HTML is rendered directly with all images and links working out-of-the-box.

## See Also

- [Main README](../README.md) - Package documentation
- [IMAGE-EDITING.md](../docs/IMAGE-EDITING.md) - Image editing features
- [FILE-UPLOAD.md](../docs/FILE-UPLOAD.md) - File link functionality
- [DRAG-DROP.md](../docs/DRAG-DROP.md) - Drag & drop documentation
- [LOCALIZATION.md](../docs/LOCALIZATION.md) - Multilingual support
