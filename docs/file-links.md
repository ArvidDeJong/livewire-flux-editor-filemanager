---
title: "File links"
nav_order: 6
description: "How editors insert a link to a PDF or another file from Laravel Filemanager with the file link button, edit an existing link, and the HTML that is saved."
---

# File links

## Insert a link to a file

1. Click the file link button in the toolbar, next to Flux's own link button. Laravel Filemanager opens in a popup window, in its file category (`?type=Files`).
2. Upload or pick a file and confirm. When several files are selected, the first one is used.
3. A modal opens to set up the link:

| Field | What it sets |
| --- | --- |
| File | The URL, read-only |
| Link text | The visible text, prefilled with the file name. Say what the file is, for example "Price list 2026 (PDF)". An empty text is refused with the alert `Please enter link text` |
| Target | `_blank` (default), `_self`, `_parent` or `_top` |
| Extra CSS classes | The `class` attribute, for example `btn btn-primary` |
| Extra styles | Inline CSS |

Insert adds the link at the cursor. Enter in the link text field inserts too. Escape, Cancel or a click outside the modal closes it without inserting.

Which file types can be uploaded and picked is a Laravel Filemanager setting, `folder_categories.file.valid_mime` in `config/lfm.php`.

## Edit a link

Click any link in the editor, including links made with Flux's own link button. The same modal opens with the current text, target, classes and styles. Update replaces the whole link. The URL cannot be changed here; delete the link and insert a new one for another file.

## The HTML that is saved

```html
<a href="https://example.com/storage/files/1/price-list-2026.pdf" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-primary">Price list 2026 (PDF)</a>
```

The Link extension in the setup block in your `app.js` is configured with `rel="noopener noreferrer nofollow"`. `noopener` and `noreferrer` stop a page opened in a new tab from reaching back to yours, and `nofollow` asks search engines not to follow the link. Change it in the `Link.configure()` call in `resources/js/app.js` if you need something else.

## Files are public

With Laravel Filemanager's default disk, `public`, a linked file is a plain URL under `/storage/`. Your web server sends it to anyone who has the URL, logged in or not. Do not use file links for documents that only some visitors may see.
