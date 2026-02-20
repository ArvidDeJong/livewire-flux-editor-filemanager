# Drag & Drop and Paste

The package supports drag & drop and paste functionality for images, making it easy to add images to your content without using the file manager.

## Features

- **Drag & Drop** - Drag images from your computer directly into the editor
- **Paste from Clipboard** - Paste images from clipboard (screenshots, copied images)
- **Base64 Encoding** - Images are automatically converted to base64 data URLs
- **Multiple Images** - Drop or paste multiple images at once
- **Position Control** - Dropped images appear exactly where you drop them

## Configuration

### Upload Method

You can configure how dropped/pasted images are handled in `config/flux-filemanager.php`:

```php
'drag_drop' => [
    // Upload method: 'base64' or 'upload'
    'method' => env('FILEMANAGER_DRAG_DROP_METHOD', 'base64'),

    // Upload endpoint (only used when method is 'upload')
    'upload_url' => env('FILEMANAGER_UPLOAD_URL', '/filemanager/upload'),

    // Maximum file size in bytes (default: 5MB)
    'max_file_size' => env('FILEMANAGER_MAX_FILE_SIZE', 5242880),

    // Allowed image types
    'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
],
```

**Base64 Method (Default):**

- Images are embedded directly in HTML
- No server upload needed
- Best for small images and quick demos

**Upload Method:**

- Images are uploaded to Laravel Filemanager
- Stored on server like regular uploads
- Better for production and large images

### Environment Variables

Add to your `.env` file:

```env
# Use 'base64' or 'upload'
FILEMANAGER_DRAG_DROP_METHOD=base64

# Upload endpoint (only for 'upload' method)
FILEMANAGER_UPLOAD_URL=/filemanager/upload

# Max file size in bytes (5MB = 5242880)
FILEMANAGER_MAX_FILE_SIZE=5242880
```

## Setup

Drag & drop functionality is included in the standard installation.

**Prerequisites:**

1. Follow the [Installation Guide](INSTALLATION.md)
2. Ensure `prosemirror-state` is installed
3. Complete TipTap configuration from [`examples/app.js`](../examples/app.js)

The drag & drop handlers are part of the Image extension's ProseMirror plugins.

## Usage

### Drag & Drop

1. **Open your file explorer** or finder
2. **Select one or more images**
3. **Drag** them to the editor
4. **Drop** at the desired position
5. Images are inserted automatically

**Supported formats:**

- PNG
- JPG/JPEG
- GIF
- WebP
- SVG
- BMP
- Any format with `image/*` MIME type

### Paste from Clipboard

1. **Copy an image** to your clipboard:
   - Take a screenshot (`Cmd + Shift + 4` on Mac, `Win + Shift + S` on Windows)
   - Copy an image from a website
   - Copy an image from another application
2. **Click** in the editor to position cursor
3. **Paste** with `Cmd + V` (Mac) or `Ctrl + V` (Windows)
4. Image is inserted at cursor position

## How It Works

### Drag & Drop Handler

The `handleDrop` function:

1. **Checks** if files are being dropped (not moved content)
2. **Filters** for image files only
3. **Reads** each image file using FileReader
4. **Converts** to base64 data URL
5. **Inserts** at drop coordinates

### Paste Handler

The `handlePaste` function:

1. **Checks** clipboard for image items
2. **Extracts** image files from clipboard data
3. **Reads** each image using FileReader
4. **Converts** to base64 data URL
5. **Replaces** current selection with image

## Base64 vs Server Upload

### Base64 (Default)

**Advantages:**

- ✅ No server upload needed
- ✅ Works immediately
- ✅ No file management required
- ✅ Images embedded in content

**Disadvantages:**

- ❌ Larger HTML size
- ❌ Not ideal for large images
- ❌ Slower page load for many images

**Best for:**

- Small images (< 100KB)
- Icons and logos
- Screenshots
- Quick content creation

### Server Upload (Alternative)

To use server upload instead of base64:

1. Set in `.env`: `FILEMANAGER_DRAG_DROP_METHOD=upload`
2. Configure upload endpoint: `FILEMANAGER_UPLOAD_URL=/filemanager/upload`
3. The package handles the upload automatically

See [`examples/app.js`](../examples/app.js) for the implementation details.

## Editing Dropped Images

After dropping or pasting an image, you can edit it like any other image:

**Single click:**

- Quick resize menu (25%, 50%, 75%, 100%)
- Alignment buttons

**Double click:**

- Complete edit modal
- Alt text
- Title
- Width
- Alignment
- CSS classes
- Inline styles

See [IMAGE-EDITING.md](IMAGE-EDITING.md) for details.

## Troubleshooting

### Images Not Dropping

**Problem:** Nothing happens when dropping images

**Solutions:**

1. Check if `prosemirror-state` is installed
2. Verify the plugin is registered correctly
3. Check browser console for errors
4. Ensure `allowBase64: true` in Image config

### Paste Not Working

**Problem:** Paste doesn't insert images

**Solutions:**

1. Check if image is actually in clipboard
2. Try with a screenshot first
3. Check browser console for errors
4. Verify `handlePaste` is implemented

### Large Images Slow Down Editor

**Problem:** Editor becomes slow with large base64 images

**Solutions:**

1. Resize images before dropping
2. Use server upload instead of base64
3. Limit image size in handler:

```javascript
if (file.size > 500000) {
  // 500KB
  alert("Image too large. Please use an image smaller than 500KB.");
  return;
}
```

### Multiple Images Insert in Wrong Order

**Problem:** Multiple dropped images appear in random order

**Solution:** This is expected behavior as FileReader is asynchronous. To maintain order, process files sequentially:

```javascript
async function processFiles(files, view, coordinates) {
  for (const file of files) {
    await new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        // Insert image
        resolve();
      };
      reader.readAsDataURL(file);
    });
  }
}
```

## Best Practices

### 1. Optimize Images Before Dropping

- Resize large images
- Compress images
- Use appropriate formats (WebP for photos, PNG for graphics)

### 2. Provide User Feedback

Add loading indicators for large images:

```javascript
reader.onloadstart = () => {
  // Show loading indicator
};

reader.onload = (e) => {
  // Insert image
  // Hide loading indicator
};
```

### 3. Validate File Types

Ensure only images are processed:

```javascript
const imageFiles = files.filter((file) => {
  return file.type.startsWith("image/") && file.size < 1000000; // 1MB limit
});

if (imageFiles.length === 0) {
  alert("Please drop image files only (max 1MB)");
  return false;
}
```

### 4. Handle Errors

Add error handling for failed reads:

```javascript
reader.onerror = () => {
  alert("Failed to read image file");
};
```

## See Also

- [IMAGE-EDITING.md](IMAGE-EDITING.md) - Image editing features
- [FILE-UPLOAD.md](FILE-UPLOAD.md) - File link functionality
- [README.md](../README.md) - General documentation
