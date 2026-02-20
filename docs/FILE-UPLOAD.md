# File Upload & Links

The package makes it easy to add links to files (such as PDFs, Word documents, ZIP files, etc.) to your content via Laravel Filemanager.

## Overview

The process consists of three steps:

1. **Click the file link button** (🔗 icon in the toolbar)
2. **Select a file** via Laravel Filemanager
3. **Configure the link** in the modal (text, target, classes, styles)

## File Link Button

### Location

The file link button is located in the editor toolbar, next to the normal link button:

```
[Heading] [Bold] [Italic] ... [Image] [Link] [File Link] ...
```

### Icon

The icon shows two linked rings (🔗), similar to a link icon but specifically for files.

### Tooltip

On hover, the tooltip appears:

- 🇳🇱 Dutch: "Insert File Link"
- 🇬🇧 English: "Insert File Link"
- 🇩🇪 German: "Dateilink einfügen"

## Step 1: Select File

### Opening Laravel Filemanager

1. **Click the file link button** in the toolbar
2. **Laravel Filemanager popup** opens automatically
3. The popup shows the **Files** tab (not Images)

### Upload File (Optional)

If the file doesn't exist yet:

1. Click **"Upload"** in Laravel Filemanager
2. Select file(s) from your computer
3. Wait until upload is complete
4. File appears in the list

### Select File

1. **Navigate** through the folders
2. **Click** on the desired file
3. **Click** "Select" or double-click the file
4. Laravel Filemanager closes automatically

### Supported File Types

Laravel Filemanager supports by default:

| Type              | Extensions              | Use               |
| ----------------- | ----------------------- | ----------------- |
| **Documents**     | `.pdf`, `.doc`, `.docx` | Manuals, reports  |
| **Spreadsheets**  | `.xls`, `.xlsx`, `.csv` | Data, price lists |
| **Presentations** | `.ppt`, `.pptx`         | Presentations     |
| **Archives**      | `.zip`, `.rar`, `.7z`   | Multiple files    |
| **Text**          | `.txt`, `.md`           | Documentation     |
| **Other**         | Configurable            | Depends on setup  |

## Step 2: Configure Link

### Modal Fields

After selecting a file, a modal opens with the following fields:

#### 1. File (readonly)

The full URL to the file.

**Example:**

```
https://example.com/storage/files/3/manual.pdf
```

This field is read-only and shows the selected file.

#### 2. Link Text

The text that is visible in the editor and on the website.

**Examples:**

```
Download PDF
View manual
Download price list (PDF, 2.5MB)
Click here for more information
```

**Tips:**

- Make it descriptive
- Mention the file type
- Optionally mention the file size
- Use active language ("Download", "View")

#### 3. Target

Determines where the link opens:

| Option            | Value     | Behavior                   |
| ----------------- | --------- | -------------------------- |
| **New window**    | `_blank`  | Opens in new tab (default) |
| **Same window**   | `_self`   | Opens in the same tab      |
| **Parent window** | `_parent` | Opens in parent frame      |
| **Top window**    | `_top`    | Opens in top-level frame   |

**Recommendation:** Use `_blank` for downloads so users stay on the page.

#### 4. Extra CSS Classes

Add CSS classes for styling.

**Examples:**

**Tailwind CSS:**

```
inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700
```

**Bootstrap:**

```
btn btn-primary btn-lg
```

**Custom classes:**

```
download-button pdf-link
```

#### 5. Extra Styles

Add inline CSS styling.

**Examples:**

**Button styling:**

```
background: #3b82f6; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;
```

**Icon styling:**

```
padding-left: 24px; background: url('/icons/pdf.svg') no-repeat left center;
```

### Modal Actions

| Button     | Action                           |
| ---------- | -------------------------------- |
| **Cancel** | Closes modal without adding link |
| **Insert** | Adds link to the editor          |

**Keyboard shortcuts:**

- `Enter` - Insert (in last field)
- `Esc` - Cancel

## Step 3: Edit Link

### Edit Existing Link

1. **Click** on an existing file link in the editor
2. **Modal opens** automatically with current values
3. **Edit** the desired fields
4. **Click "Update"** to save changes

**Note:** The file URL cannot be changed. To link to a different file, delete the old link and create a new one.

## HTML Result

### Basic Link

```html
<a
  href="https://example.com/storage/files/handleiding.pdf"
  target="_blank"
  rel="noopener noreferrer nofollow"
>
  Download PDF
</a>
```

### Link with Classes

```html
<a
  href="https://example.com/storage/files/price-list.xlsx"
  target="_blank"
  rel="noopener noreferrer nofollow"
  class="btn btn-primary"
>
  Download price list
</a>
```

### Link with Styles

```html
<a
  href="https://example.com/storage/files/report.docx"
  target="_blank"
  rel="noopener noreferrer nofollow"
  style="background: #3b82f6; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;"
>
  View report
</a>
```

### Link with Classes and Styles

```html
<a
  href="https://example.com/storage/files/archive.zip"
  target="_blank"
  rel="noopener noreferrer nofollow"
  class="download-button inline-flex items-center"
  style="font-weight: bold; color: #1e40af;"
>
  Download archive (ZIP, 15MB)
</a>
```

## Best Practices

### 1. Descriptive Link Text

```html
<!-- Good ✓ -->
<a href="...">Download product catalog 2024 (PDF, 5.2MB)</a>

<!-- Mediocre ~ -->
<a href="...">Download PDF</a>

<!-- Bad ✗ -->
<a href="...">Click here</a>
```

**Why?**

- Users know what to expect
- Better for SEO
- More accessible for screen readers

### 2. Mention File Type and Size

```html
<a href="...">Annual report 2023 (PDF, 2.1MB)</a>
<a href="...">Price list (Excel, 156KB)</a>
<a href="...">Vacation photos (ZIP, 45MB)</a>
```

**Why?**

- Users know what they're downloading
- Prevents surprises with large files
- Users can decide whether to download

### 3. Use \_blank for Downloads

```html
<a href="/storage/files/document.pdf" target="_blank"> Download document </a>
```

**Why?**

- Users stay on the page
- Document opens in new tab
- Better user experience

### 4. Consistent Styling

Use the same classes for similar links:

```html
<!-- All download buttons -->
<a class="btn btn-primary" href="...">Download PDF</a>
<a class="btn btn-primary" href="...">Download Excel</a>

<!-- All inline links -->
<a class="text-blue-600 underline" href="...">View document</a>
```

### 5. Accessible Link Text

```html
<!-- Good ✓ -->
<a href="...">Download installation manual (PDF)</a>

<!-- Bad ✗ - Not clear for screen readers -->
<a href="...">Download</a>
<a href="...">Here</a>
<a href="...">Click</a>
```

### 6. Secure Links

The package automatically adds `rel="noopener noreferrer nofollow"` for security:

```html
<a href="..." target="_blank" rel="noopener noreferrer nofollow"></a>
```

**What does this do?**

- `noopener` - Prevents window.opener exploits
- `noreferrer` - Hides referrer information
- `nofollow` - Indicates that link should not be followed by search engines

## Examples

### Example 1: PDF Download Button

**Configuration:**

- Link text: `Download product catalog 2024 (PDF, 5.2MB)`
- Target: `_blank`
- Classes: `inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition`

**Result:**

```html
<a
  href="https://example.com/storage/files/catalog-2024.pdf"
  target="_blank"
  rel="noopener noreferrer nofollow"
  class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
>
  Download product catalog 2024 (PDF, 5.2MB)
</a>
```

### Example 2: Excel Price List

**Configuration:**

- Link text: `View current price list (Excel)`
- Target: `_blank`
- Classes: `text-green-600 underline hover:text-green-800`

**Result:**

```html
<a
  href="https://example.com/storage/files/price-list.xlsx"
  target="_blank"
  rel="noopener noreferrer nofollow"
  class="text-green-600 underline hover:text-green-800"
>
  View current price list (Excel)
</a>
```

### Example 3: ZIP Archive with Icon

**Configuration:**

- Link text: `Download all photos (ZIP, 45MB)`
- Target: `_blank`
- Styles: `padding-left: 28px; background: url('/icons/zip.svg') no-repeat left center; background-size: 20px;`

**Result:**

```html
<a
  href="https://example.com/storage/files/photos-2024.zip"
  target="_blank"
  rel="noopener noreferrer nofollow"
  style="padding-left: 28px; background: url('/icons/zip.svg') no-repeat left center; background-size: 20px;"
>
  Download all photos (ZIP, 45MB)
</a>
```

## Common Use Cases

### 1. Manuals and Documentation

```html
<a href="/storage/files/installation-manual.pdf" target="_blank">
  Installation manual (PDF, 1.2MB)
</a>
```

### 2. Forms and Templates

```html
<a href="/storage/files/application-form.docx" target="_blank">
  Download application form (Word)
</a>
```

### 3. Reports and Annual Reports

```html
<a
  href="/storage/files/annual-report-2023.pdf"
  target="_blank"
  class="btn btn-primary"
>
  Annual report 2023 (PDF, 8.5MB)
</a>
```

### 4. Price Lists and Specifications

```html
<a href="/storage/files/technical-specs.xlsx" target="_blank">
  Technical specifications (Excel, 245KB)
</a>
```

### 5. Software and Updates

```html
<a href="/storage/files/software-v2.1.zip" target="_blank" class="download-btn">
  Download Software v2.1 (ZIP, 125MB)
</a>
```

## Troubleshooting

### File Link Button Not Visible

**Problem:** The file link button doesn't appear in the toolbar

**Solutions:**

1. Check if you're using the package editor component
2. Check if the toolbar includes the file-link button
3. Refresh the page with `Cmd + Shift + R`

### Laravel Filemanager Doesn't Open

**Problem:** Popup doesn't open when clicking the button

**Solutions:**

1. Check if Laravel Filemanager is correctly installed
2. Check if the URL is correct in config
3. Check browser console for errors
4. Allow popups in your browser

### Wrong Tab in Filemanager

**Problem:** Images tab opens instead of Files tab

**Solution:**

- This is normal behavior - manually navigate to the Files tab
- Or use the image button for images

### Link Not Saved

**Problem:** After "Insert" the link doesn't appear

**Solutions:**

1. Check if Livewire is working correctly
2. Check browser console for JavaScript errors
3. Save the page in the CMS
4. Refresh the page

### File Not Accessible

**Problem:** Link works but file cannot be downloaded

**Solutions:**

1. Check if file still exists in storage
2. Check file permissions (must be readable)
3. Check if storage link is correct: `php artisan storage:link`
4. Test the URL directly in the browser

## Technical Details

### TipTap Link Extension

The package uses an extended TipTap Link extension with extra attributes:

```javascript
Link.configure({
    openOnClick: false,
    HTMLAttributes: {
        rel: 'noopener noreferrer nofollow',
    },
}).extend({
    addAttributes() {
        return {
            href: { ... },
            target: { ... },
            class: { ... },
            style: { ... },
        }
    },
})
```

### Attributes

| Attribute | Type   | Description                                            |
| --------- | ------ | ------------------------------------------------------ |
| `href`    | String | File URL (required)                                    |
| `target`  | String | Window target (\_blank, \_self, etc.)                  |
| `rel`     | String | Relationship (automatic: noopener noreferrer nofollow) |
| `class`   | String | CSS classes                                            |
| `style`   | String | Inline CSS                                             |

### Security

The package automatically implements security best practices:

1. **noopener** - Prevents window.opener attacks
2. **noreferrer** - Protects privacy
3. **nofollow** - SEO control

## See Also

- [IMAGE-EDITING.md](IMAGE-EDITING.md) - Image editing
- [LOCALIZATION.md](LOCALIZATION.md) - Multi-language support
- [README.md](../README.md) - General documentation
- [Laravel Filemanager Docs](https://unisharp.github.io/laravel-filemanager/) - Official documentation
