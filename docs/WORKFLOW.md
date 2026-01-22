# Laravel Filemanager + Flux Editor Integration Workflow

This workflow describes how to integrate Laravel Filemanager with the Flux TipTap Editor for image uploads.

## Requirements

- Laravel 12
- Livewire 4
- FluxUI Pro (with Editor component)
- Laravel Filemanager (UniSharp)
- TipTap Image Extension

## Step 1: Install Dependencies

```bash
npm install @tiptap/extension-image
```

Add to `package.json`:
```json
{
  "dependencies": {
    "@tiptap/extension-image": "^2.10.5"
  }
}
```

## Step 2: Create Laravel Filemanager Module

File: `resources/js/laravel-filemanager.js`

```javascript
/**
 * Laravel Filemanager integration for Flux Editor
 * Generic implementation that works for all Flux editors
 */

/**
 * Open Laravel Filemanager popup
 * @param {string} type - 'Images' or 'Files'
 * @param {function} onPicked - Callback function with selected URLs
 */
function openLaravelFilemanager(type, onPicked) {
    const w = 900
    const h = 600
    const left = window.screenX + (window.outerWidth - w) / 2
    const top = window.screenY + (window.outerHeight - h) / 2

    const url = `/cms/laravel-filemanager?type=${encodeURIComponent(type)}`

    const fm = window.open(
        url,
        'LaravelFilemanager',
        `width=${w},height=${h},left=${left},top=${top}`
    )

    // UniSharp Laravel Filemanager callback
    window.SetUrl = function(items) {
        const urls = (items || [])
            .map(i => i?.url)
            .filter(Boolean)

        try {
            onPicked(urls)
        } finally {
            delete window.SetUrl
            if (fm && !fm.closed) fm.close()
        }
    }
}

/**
 * Insert image(s) via Laravel Filemanager
 * @param {object} editor - TipTap editor instance
 */
function insertImageFromFilemanager(editor) {
    openLaravelFilemanager('Images', (urls) => {
        if (!urls.length) return

        urls.forEach((src) => {
            editor.chain().focus().setImage({ src }).run()
        })
    })
}

/**
 * Initialize Laravel Filemanager for all Flux editors
 */
export function initLaravelFilemanager() {
    // Event listener for image button (works for all Flux editors)
    document.addEventListener('click', (e) => {
        const imageButton = e.target.closest('[data-editor="image"]')
        if (!imageButton) return

        e.preventDefault()
        e.stopPropagation()

        const editorElement = imageButton.closest('ui-editor')
        if (!editorElement?.editor) return

        insertImageFromFilemanager(editorElement.editor)
    })
}
```

## Step 3: Register TipTap Image Extension and Import Modules

File: `resources/js/app.js`

```javascript
import Image from '@tiptap/extension-image'
import { initLaravelFilemanager } from './laravel-filemanager'
import '../css/tiptap-image.css'

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

File: `resources/css/tiptap-image.css`

```css
/**
 * TipTap Image Resize Styles
 * Enables visual resize handles for images in the editor
 */

/* Image wrapper with resize handles */
.tiptap-image {
    max-width: 100%;
    height: auto;
    cursor: pointer;
    transition: box-shadow 0.2s;
}

/* Selected image state */
.ProseMirror img.ProseMirror-selectednode {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Image resize on hover */
.tiptap-image:hover {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

/* Make images resizable via browser */
.ProseMirror img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 1rem 0;
}

/* Allow inline images */
.ProseMirror img[style*="display: inline"] {
    display: inline-block;
    margin: 0 0.25rem;
}
```

**Important:** 
- Use Flux's `registerExtension()` function, not `extensions.push()`!
- Code is modularly organized for better maintainability
- Image resize is enabled via browser native resize (drag corners)
- Images get visual feedback when selected or hovered

## Step 4: Create Custom Image Button

File: `resources/views/flux/editor/image.blade.php`

```blade
@blaze

@props([
    'kbd' => null,
])

<flux:tooltip content="{{ __('Afbeelding invoegen') }}" :$kbd class="contents">
    <flux:editor.button data-editor="image">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M2.5 13.3333L6.66667 9.16667C7.08333 8.75 7.75 8.75 8.16667 9.16667L11.6667 12.6667M10.8333 11.8333L12.5 10.1667C12.9167 9.75 13.5833 9.75 14 10.1667L17.5 13.6667M10.8333 5.83333H10.8417M4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5H4.16667C3.25 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.25 17.5 4.16667 17.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </flux:editor.button>
</flux:tooltip>
```

## Step 5: Create Reusable Editor Component (Recommended)

File: `resources/views/components/flux-editor.blade.php`

```blade
@props([
    'id' => null,
    'rows' => 12,
    'toolbar' => 'default', // 'default', 'minimal', 'full', or false for custom
])

<flux:editor :id="$id" {{ $attributes->wire('model') }} :rows="$rows">
    @if($toolbar !== false)
        <flux:editor.toolbar>
            @if($toolbar === 'minimal')
                {{-- Minimal toolbar: basic formatting only --}}
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.separator />
                <flux:editor.link />
            @elseif($toolbar === 'full')
                {{-- Full toolbar: all options --}}
                <flux:editor.heading />
                <flux:editor.separator />
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.strike />
                <flux:editor.underline />
                <flux:editor.separator />
                <flux:editor.bullet />
                <flux:editor.ordered />
                <flux:editor.blockquote />
                <flux:editor.separator />
                <flux:editor.image />
                <flux:editor.link />
                <flux:editor.separator />
                <flux:editor.align />
                <flux:editor.separator />
                <flux:editor.code />
            @else
                {{-- Default toolbar: most commonly used options --}}
                <flux:editor.heading />
                <flux:editor.separator />
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.strike />
                <flux:editor.separator />
                <flux:editor.bullet />
                <flux:editor.ordered />
                <flux:editor.blockquote />
                <flux:editor.separator />
                <flux:editor.image />
                <flux:editor.link />
                <flux:editor.separator />
                <flux:editor.align />
            @endif
        </flux:editor.toolbar>
    @else
        {{-- Custom toolbar via slot --}}
        {{ $slot }}
    @endif
    
    <flux:editor.content />
</flux:editor>
```

### Usage in Livewire Components:

**Default toolbar:**
```blade
<flux:field>
    <flux:label>Content</flux:label>
    <x-flux-editor wire:model="content" />
    <flux:error name="content" />
</flux:field>
```

**Minimal toolbar:**
```blade
<x-flux-editor wire:model="description" toolbar="minimal" :rows="6" />
```

**Full toolbar:**
```blade
<x-flux-editor wire:model="content" toolbar="full" :rows="20" />
```

**Custom toolbar:**
```blade
<x-flux-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.image />
    </flux:editor.toolbar>
</x-flux-editor>
```

## Step 6: Build Assets

```bash
npm run build
```

## Usage

### In the CMS Editor
1. Click the image button (📷) in the toolbar
2. Laravel Filemanager popup opens
3. Select or upload an image
4. Image appears **immediately visible** in the editor as an `<img>` tag

### On the Website
Simply use the content property:

```blade
<div class="prose max-w-none">
    {!! $page->content !!}
</div>
```

Images are stored as HTML `<img>` tags and work out-of-the-box.

## Troubleshooting

### Error: "Cannot read properties of null (reading 'innerHTML')"
**Solution:** Add `<flux:editor.content />` after the toolbar in your editor component.

### Error: "Unknown node type: image"
**Solution:** The Image extension is not correctly registered. Check if:
- `@tiptap/extension-image` is installed
- `app.js` registers the extension via `registerExtension()`
- Assets are built with `npm run build`

### Images are not being inserted
**Solution:** Check in the console if:
- `flux:editor` event is received
- `registerExtension` function is available
- Editor instance is correctly found (`editorElement.editor`)

### setImage is not a function
**Solution:** Image extension is not loaded. Refresh the page (hard refresh: Cmd+Shift+R) after `npm run build`.

## Important Notes

1. **Generic Implementation:** All JavaScript is in `app.js` - works automatically for **all** Flux editors in your application
2. **Flux registerExtension API:** Use `e.detail.registerExtension(Image)`, not `extensions.push()`
3. **UniSharp Callback:** Laravel Filemanager expects `window.SetUrl()` callback function
4. **Editor Instance:** Flux stores the TipTap instance in `editorElement.editor` (not `__editor`)
5. **WYSIWYG:** Images are immediately visible in the editor, no shortcodes
6. **DRY Principle:** No code duplication - one implementation works everywhere

## Files Overview

- `resources/js/laravel-filemanager.js` - Laravel Filemanager integration module (generic)
- `resources/js/app.js` - Image extension registration + module imports
- `resources/css/tiptap-image.css` - Image resize styling
- `resources/views/flux/editor/image.blade.php` - Custom image button component
- `resources/views/components/flux-editor.blade.php` - Reusable editor component
- `package.json` - TipTap Image dependency

**Modular structure** - Code is neatly organized in separate files!

## Benefits

✅ **WYSIWYG** - Images immediately visible in editor  
✅ **Native TipTap** - Uses standard Image extension  
✅ **Image Resize** - Visual resize with browser native handles  
✅ **No shortcodes** - Content is clean HTML  
✅ **Laravel Filemanager** - Easy image management  
✅ **Generic & DRY** - One implementation works in all Livewire components  
✅ **KISS Principle** - Simple, maintainable code without duplication
