---
title: "Image editing"
nav_order: 5
description: "How editors insert an image from Laravel Filemanager, resize and align it with one click, edit alt text and classes, and which HTML is saved."
---

# Image editing

## Insert an image

1. Click the image button in the toolbar. Laravel Filemanager opens in a popup window, in its image category (`?type=Images`).
2. Upload or pick one or more images and confirm.
3. Each image is inserted at the cursor with the class `tiptap-image` and `width="400px"`. Resize it from there.

If the browser blocks the popup, an alert shows the `messages.popup_blocked` text from the config.

## Single click: resize and align

Click an image once. A menu appears below it with:

- The preset widths from `resize_presets`, by default 25%, 50%, 75% and 100%
- A field for a custom percentage between `custom_width.min` and `custom_width.max`, applied with the Apply button or Enter. A value outside the limits is ignored
- Align left, center and right

The menu changes the attributes of the image in place, so the alt text, the title and your own classes and styles stay. It closes when you click elsewhere.

## Double click: alt text, title, classes and styles

Double-click an image to open the edit modal:

| Field | What it sets |
| --- | --- |
| Image | The URL, read-only. To use another image, delete this one and insert the new one |
| Alt text | The `alt` attribute, the description that screen readers read out |
| Title | The `title` attribute, shown as a tooltip |
| Width | One of the presets, plus the current width if it is not a preset. An image without a width shows `100%` |
| Alignment | None, left, center or right |
| Extra CSS classes | Classes added after the generated ones, for example `rounded-lg shadow` |
| Extra styles | Inline CSS added after the generated width and margins |

Update saves. Enter in the title field saves too. Escape, Cancel or a click outside the modal closes it without saving.

The class and style fields are free text. The package does not check or clean what an editor types there.

## The HTML that is saved

The editor writes standard `<img>` tags. A centered image at 50% with an extra class:

```html
<img
    src="https://example.com/storage/photos/1/office.jpg"
    class="tiptap-image align-center rounded-lg"
    width="50%"
    style="width: 50%; margin-left: auto; margin-right: auto; display: block;"
    alt="Our office in Amsterdam"
    title="Head office"
    data-align="center"
>
```

The package generates these parts and rewrites them on every change:

- The class `tiptap-image`, plus `align-left`, `align-center` or `align-right`
- The `width` attribute and a `width` style
- The margins for the alignment, and `display: block` for center
- `data-align`

Everything else in `class` and `style` is yours and is kept. Style `.tiptap-image` and the `align-*` classes in the CSS of your public site; the package's stylesheets only style the editor. [Your first editor](first-editor.md#step-4-show-the-content-on-the-site) has an example.

## Which URL ends up in src

The image button inserts the URL that Laravel Filemanager returns. With Laravel Filemanager's default disk, `public`, that is a URL under `/storage/`, served through the `storage:link` symlink, for example `https://example.com/storage/photos/1/office.jpg`. The `1` is the ID of the logged-in user: Laravel Filemanager gives every user a private folder by default.

One correction is applied: when the returned URL points to another host than the page you are on, and its path starts with `/storage/`, `/filemanager` or `/laravel-filemanager`, the host is dropped and the path is saved. That keeps images working when `APP_URL` does not match the host in the browser.

Uploads, folders, thumbnails and who may upload what are Laravel Filemanager's job; see [Uploads and security](uploads-and-security.md).

Dropped and pasted images are different: they are embedded as base64 by default. See [Drag and drop](drag-and-drop.md).
