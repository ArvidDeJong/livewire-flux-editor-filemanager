---
title: File links
nav_order: 6
description: Insert links to PDFs and other files from Laravel Filemanager with the file link button, and edit any link in the editor by clicking it.
---

# File links

## Inserting a file link

Click the file link button in the toolbar, next to the regular link button. Laravel Filemanager opens on the Files tab; upload or pick a file and confirm. A modal opens to configure the link:

| Field | What it sets |
| --- | --- |
| File | The URL, read-only |
| Link text | The visible text, prefilled with the file name. Say what the file is, for example "Price list 2026 (PDF)" |
| Target | `_blank` (default), `_self`, `_parent` or `_top` |
| Extra CSS classes | The `class` attribute, for example `btn btn-primary` |
| Extra styles | Inline CSS |

Insert adds the link at the cursor. Enter in the link text field inserts too, Escape cancels.

Which file types can be uploaded and picked is a Laravel Filemanager setting (`config/lfm.php`).

## Editing a link

Click any link in the editor, including links made with Flux's own link button, and the same modal opens with the current text, target, classes and styles. Update replaces the whole link. The URL can't be changed here; delete the link and insert a new one for another file.

## The HTML

```html
<a href="/storage/files/1/price-list-2026.pdf" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-primary">Price list 2026 (PDF)</a>
```

The Link extension adds `rel="noopener noreferrer nofollow"` to every link, so a page opened in a new tab can't reach back to yours and search engines don't follow file links. Change that in the `Link.configure()` call in your `app.js` if you need to.
