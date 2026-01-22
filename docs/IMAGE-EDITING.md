# Image Editing

The package offers two ways to edit images in the editor: a **quick resize menu** (single click) and a **complete edit modal** (double click).

## Overview

| Action | Functionality | Use |
|-------|----------------|---------|
| **Single click** | Resize menu | Quickly adjust size and alignment |
| **Double click** | Edit modal | Edit all properties (alt, title, size, alignment, classes, styles) |

## Single Click - Resize Menu

### How to Use

1. **Click once** on an image in the editor
2. A **floating menu** appears next to the image
3. Choose an option from the menu

### Available Options

#### Resize Buttons

Choose from predefined sizes:

- **25%** - Quarter of the width
- **50%** - Half of the width
- **75%** - Three quarters of the width
- **100%** - Full width

#### Custom Width

- **Input field** - Enter a custom percentage (1-100)
- **OK button** - Apply the custom width

#### Alignment Buttons

Choose the alignment of the image:

- **Left** (←) - Align image left
- **Center** (↔) - Center image
- **Right** (→) - Align image right

### Example Usage

```
1. Click on image
2. Resize menu appears
3. Click on "50%" button
4. Image is immediately adjusted to 50% width
5. Menu disappears automatically
```

### Result in HTML

```html
<img 
    class="tiptap-image" 
    src="https://example.com/image.jpg" 
    width="50%" 
    style="width: 50%"
>
```

With alignment:

```html
<img 
    class="tiptap-image align-center" 
    src="https://example.com/image.jpg" 
    width="50%" 
    style="width: 50%; margin-left: auto; margin-right: auto; display: block;"
    data-align="center"
>
```

## Double Click - Edit Modal

### How to Use

1. **Double-click** on an image in the editor
2. A **modal** opens with all properties
3. Edit the desired fields
4. Click **"Update"** to save the changes

### Available Fields

#### 1. Image (readonly)

The URL of the image. This field is read-only.

```
https://example.com/storage/photos/image.jpg
```

#### 2. Alt Text

Alternative text for the image (important for SEO and accessibility).

**Why important?**
- **SEO**: Search engines use alt text to index images
- **Accessibility**: Screen readers read alt text for visually impaired users
- **Fallback**: Shown when the image cannot be loaded

**Example:**
```
A modern office space with large windows and plants
```

#### 3. Title

Tooltip text that appears on hover over the image.

**Example:**
```
Our new office in Amsterdam
```

#### 4. Width

Dropdown with predefined sizes:

- 25%
- 50%
- 75%
- 100%

#### 5. Alignment

Dropdown with alignment options:

- **None** - Default alignment
- **Left** - Align image left
- **Center** - Center image
- **Right** - Align image right

#### 6. Extra CSS Classes

Add custom CSS classes for styling.

**Examples:**

**Tailwind CSS:**
```
rounded-lg shadow-xl border-2 border-gray-300
```

**Bootstrap:**
```
img-fluid rounded shadow
```

**Custom classes:**
```
featured-image hover-zoom
```

#### 7. Extra Styles

Add inline CSS styling.

**Examples:**

**Border:**
```
border: 2px solid #3b82f6; border-radius: 8px;
```

**Shadow:**
```
box-shadow: 0 10px 25px rgba(0,0,0,0.2);
```

**Transform:**
```
transform: rotate(2deg); transition: all 0.3s;
```

### Example Usage

```
1. Double-click on image
2. Edit modal opens
3. Fill in:
   - Alt text: "Product photo of our new laptop"
   - Title: "MacBook Pro 2024"
   - Width: 75%
   - Alignment: Center
   - Extra CSS Classes: "rounded-lg shadow-xl"
   - Extra Styles: "border: 1px solid #e5e7eb;"
4. Click "Update"
5. Modal closes and changes are saved
```

### Result in HTML

```html
<img 
    class="tiptap-image align-center rounded-lg shadow-xl" 
    src="https://example.com/storage/photos/laptop.jpg" 
    width="75%" 
    style="width: 75%; margin-left: auto; margin-right: auto; display: block; border: 1px solid #e5e7eb;"
    alt="Product photo of our new laptop"
    title="MacBook Pro 2024"
    data-align="center"
>
```

## Workflow Comparison

### Quick Adjustment (Single Click)

**Use when:**
- You only want to adjust the size
- You only want to change the alignment
- You want to work quickly without a modal

**Advantages:**
- ⚡ Very fast
- 🎯 Immediate result
- 📍 Menu stays with the image

**Disadvantages:**
- Limited options
- No alt/title editing
- No custom classes/styles

### Complete Editing (Double Click)

**Use when:**
- You want to set all properties
- You want to optimize SEO (alt text)
- You want to add custom styling
- You want to improve accessibility

**Advantages:**
- 🎨 All options available
- ♿ SEO and accessibility
- 🎯 Custom styling possible
- 📋 Clear form

**Disadvantages:**
- Slightly slower (modal opens)
- More fields to fill in

## Best Practices

### 1. Always Use Alt Text

```html
<!-- Good ✓ -->
<img alt="Team photo during company outing 2024" src="..." />

<!-- Bad ✗ -->
<img src="..." />
```

**Why?**
- Required for WCAG 2.1 accessibility
- Improves SEO ranking
- Better user experience

### 2. Keep Alt Text Descriptive but Short

```
<!-- Good ✓ -->
alt="Modern living room with gray sofa and wooden floor"

<!-- Too long ✗ -->
alt="This is a photo of a modern living room with a large gray sofa, a wooden coffee table, a white cabinet, plants in the corner, and a beautiful rug on the wooden floor"

<!-- Too short ✗ -->
alt="Living room"
```

### 3. Use Title for Extra Context

```html
<img 
    alt="CEO John Doe during presentation"
    title="Annual meeting 2024 - Keynote speech"
    src="..."
/>
```

### 4. Consistent Sizes

Use consistent sizes for similar images:

```
- Hero images: 100%
- Content images: 75%
- Thumbnails: 25%
- Icons: 50px (via custom styles)
```

### 5. Responsive Classes

Use Tailwind responsive classes for different screen sizes:

```
Extra CSS Classes:
w-full md:w-3/4 lg:w-1/2 rounded-lg shadow-md
```

### 6. Performance Optimization

Add lazy loading via custom attributes:

```
Extra Styles:
loading: lazy;
```

## Keyboard Shortcuts

| Action | Shortcut | Description |
|-------|----------|--------------|
| Open modal | Double click | Open edit modal |
| Close modal | `Esc` | Close modal without saving |
| Save | `Enter` | Save changes (in last field) |
| Cancel | `Esc` or click outside modal | Close without saving |

## Troubleshooting

### Resize Menu Doesn't Appear

**Problem:** Menu doesn't appear on single click

**Solutions:**
1. Check if JavaScript is loaded correctly
2. Hard refresh: `Cmd + Shift + R` (Mac) or `Ctrl + Shift + R` (Windows)
3. Check browser console for errors

### Edit Modal Doesn't Open

**Problem:** Modal doesn't open on double click

**Solutions:**
1. Check if you're really double-clicking (not too slow)
2. Try single click first, then double click
3. Refresh the page

### Changes Not Saved

**Problem:** After "Update" changes are not visible

**Solutions:**
1. Check if Livewire is working correctly
2. Check browser console for JavaScript errors
3. Save the page in the CMS
4. Refresh the page

### Custom Classes Don't Work

**Problem:** CSS classes have no effect

**Solutions:**
1. Check if CSS classes are loaded (Tailwind, Bootstrap, etc.)
2. Check if classes are spelled correctly
3. Use browser inspector to see if classes are applied
4. Check CSS specificity

## Technical Details

### HTML Attributes

The following attributes are saved:

| Attribute | Description | Example |
|-----------|--------------|-----------|
| `src` | Image URL | `https://example.com/image.jpg` |
| `alt` | Alternative text | `Image description` |
| `title` | Tooltip text | `Extra information` |
| `width` | Width percentage | `50%` |
| `style` | Inline CSS | `width: 50%; border: 1px solid red;` |
| `class` | CSS classes | `tiptap-image align-center rounded` |
| `data-align` | Alignment data | `center` |

### CSS Classes

Automatically added classes:

- `tiptap-image` - Base class (always present)
- `align-left` - For left alignment
- `align-center` - For center alignment
- `align-right` - For right alignment

### Inline Styles

Automatically generated styles:

```css
/* Width */
width: 50%;

/* Alignment left */
margin-left: 0;
margin-right: auto;

/* Alignment center */
margin-left: auto;
margin-right: auto;
display: block;

/* Alignment right */
margin-left: auto;
margin-right: 0;
```

## Examples

### Example 1: Product Photo

```html
<img 
    class="tiptap-image align-center rounded-lg shadow-xl" 
    src="/storage/products/laptop.jpg" 
    width="75%" 
    style="width: 75%; margin-left: auto; margin-right: auto; display: block; border: 2px solid #e5e7eb;"
    alt="MacBook Pro 16 inch with M3 chip"
    title="Latest MacBook Pro - Now available"
    data-align="center"
>
```

### Example 2: Team Photo

```html
<img 
    class="tiptap-image rounded-full" 
    src="/storage/team/john-doe.jpg" 
    width="25%" 
    style="width: 25%;"
    alt="John Doe - CEO"
    title="Founder and CEO since 2010"
>
```

### Example 3: Hero Image

```html
<img 
    class="tiptap-image w-full" 
    src="/storage/hero/office.jpg" 
    width="100%" 
    style="width: 100%;"
    alt="Modern office building with glass facade"
    title="Our headquarters in Amsterdam"
>
```

## See Also

- [LOCALIZATION.md](LOCALIZATION.md) - Multi-language support
- [README.md](../README.md) - General documentation
- [CONTRIBUTING.md](../CONTRIBUTING.md) - Contributing to the project
