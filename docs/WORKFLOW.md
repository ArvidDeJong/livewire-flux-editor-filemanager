# Technical Workflow

Technical reference for understanding how the package integrates Laravel Filemanager with Flux TipTap Editor.

> **💡 For installation instructions, see [INSTALLATION.md](INSTALLATION.md)**

## Architecture Overview

The package uses a modular architecture:

1. **JavaScript Module** - `laravel-filemanager.js` handles popup and callbacks
2. **TipTap Extensions** - Image and Link extensions with custom attributes
3. **ProseMirror Plugins** - Drag & drop and paste handlers
4. **Blade Components** - Reusable editor component with toolbar presets
5. **CSS Styling** - Image and modal styling

## How It Works

### 1. Laravel Filemanager Integration

The package provides a JavaScript module that:
- Opens Laravel Filemanager in a popup window
- Listens for the `window.SetUrl()` callback from UniSharp
- Inserts selected images into the TipTap editor
- Works generically for all Flux editors on the page

**Key function:** `initLaravelFilemanager()`
- Attaches click listener to image buttons
- Finds the closest editor instance
- Opens filemanager popup
- Handles image insertion

### 2. TipTap Extensions

**Image Extension:**
- Extended with custom attributes: `width`, `alt`, `title`, `data-align`, `class`, `style`
- Includes ProseMirror plugins for drag & drop and paste
- Supports base64 and server upload methods

**Link Extension:**
- Extended with custom attributes: `target`, `class`, `style`
- Enables file link functionality
- Automatic security attributes (`noopener`, `noreferrer`, `nofollow`)

**Registration:**
- Uses Flux's `flux:editor` event
- Calls `e.detail.registerExtension()` (not `extensions.push()`)
- Works for all Flux editors automatically

### 3. Blade Components

**Custom Buttons:**
- Image button with `data-editor="image"` attribute
- File link button with `data-editor="file-link"` attribute
- Localized tooltips via Laravel translations

**Editor Component:**
- Reusable `<x-flux-filemanager-editor>` component
- Three toolbar presets: minimal, default, full
- Custom toolbar support via slots

### 4. Event Flow

**Image Upload:**
1. User clicks image button in toolbar
2. JavaScript detects click via event delegation
3. Finds closest `ui-editor` element
4. Opens Laravel Filemanager popup
5. User selects image(s)
6. Filemanager calls `window.SetUrl()` callback
7. JavaScript inserts image(s) into editor via TipTap commands

**Drag & Drop:**
1. User drags image file over editor
2. ProseMirror plugin intercepts `handleDrop` event
3. Reads file via FileReader API
4. Converts to base64 or uploads to server
5. Creates image node at drop coordinates
6. Inserts into editor via transaction

**Image Editing:**
1. Single click → Shows resize menu (JavaScript)
2. Double click → Opens edit modal (JavaScript)
3. User makes changes
4. JavaScript updates image attributes via TipTap commands

## Code Organization

**JavaScript Modules:**
- `laravel-filemanager.js` - Filemanager integration (generic)
- `drag-drop-config.js` - Drag & drop configuration helper
- `app.js` - Extension registration and initialization

**CSS Files:**
- `tiptap-image.css` - Image styling and selection states
- `file-link-modal.css` - Modal styling for file links

**Blade Components:**
- `components/editor.blade.php` - Main editor component
- `flux/editor/image.blade.php` - Image button
- `flux/editor/file-link.blade.php` - File link button

**Benefits:**
- ✅ Modular structure
- ✅ No code duplication
- ✅ Easy to maintain
- ✅ Generic implementation

## Key Technical Details

**Flux Editor API:**
- Event: `flux:editor` fired when editor initializes
- Method: `e.detail.registerExtension()` to add extensions
- Instance: `editorElement.editor` contains TipTap instance

**UniSharp Callback:**
- Function: `window.SetUrl(items)` expected by Laravel Filemanager
- Items: Array of objects with `url` property
- Cleanup: Delete callback after use to prevent memory leaks

**ProseMirror Integration:**
- Plugin key: `imageDrop` for drag & drop handler
- Events: `handleDrop` and `handlePaste`
- Transactions: Used to insert/update editor content

**HTML Output:**
- Images: Standard `<img>` tags with inline styles
- Links: Standard `<a>` tags with attributes
- No shortcodes or custom markup
- Works out-of-the-box on frontend

## Complete Code Example

For the complete, working implementation see:
- [`examples/app.js`](../examples/app.js) - Full TipTap configuration
- [`examples/EditorDemo.php`](../examples/EditorDemo.php) - Livewire component
- [`examples/editor-demo.blade.php`](../examples/editor-demo.blade.php) - Blade view

## See Also

- [Installation Guide](INSTALLATION.md) - Step-by-step setup
- [Image Editing](IMAGE-EDITING.md) - Image features
- [Drag & Drop](DRAG-DROP.md) - Drag & drop details
- [File Links](FILE-UPLOAD.md) - File link functionality
- [Localization](LOCALIZATION.md) - Multi-language support
