---
title: "Tables"
nav_order: 9
description: "The table button of the Flux editor: insert a 3×3 table, add and delete rows and columns, header row and column, merge and split cells, the HTML that is saved, the setup lines it needs and styling tables on your site."
---

# Tables

Since 1.10 the toolbar has a table button. It needs Flux Pro 2.15.1 or newer and four lines in the setup block of `resources/js/app.js`; [Requirements](#requirements) says how an installation from before 1.10 gets them.

## The button and its menu

The table button, a grid icon with the tooltip "Table", opens a menu. The first entry inserts a table; the others work on the table the cursor is in, and do nothing outside one.

| Entry | What it does |
| --- | --- |
| Insert table | A table of three columns and three rows at the cursor, with a header row |
| Add row above, Add row below | A row next to the row of the cursor |
| Delete row | Removes the row of the cursor |
| Add column left, Add column right | A column next to the column of the cursor |
| Delete column | Removes the column of the cursor |
| Header row on or off | Turns the first row into header cells and back |
| Header column on or off | The same for the first column |
| Merge or split cells | Merges the selected cells into one, or splits a merged cell again |
| Delete table | Removes the whole table |

Drag across cells to select more than one; selected cells get a blue tint. Drag the border between two columns to change their widths. Flux's editor runs every command in the menu and its own stylesheet draws the table; the package adds the button, the menu and the setup lines, see [How it works](how-it-works.md).

## Where the button is

The `full` preset has it, right after the file link button. `default` and `minimal` do not; `toolbar="default | table"` adds it to the default toolbar, and [Toolbar buttons](toolbar.md) has the syntax. In a toolbar you write in Blade it is `<flux:editor.table />`.

## The HTML that is saved

The editor writes a plain `<table>`. Two columns with a header row and one merged cell, as the editor writes it, with line breaks added:

```html
<table style="min-width: 50px">
  <colgroup>
    <col style="min-width: 25px">
    <col style="min-width: 25px">
  </colgroup>
  <tbody>
    <tr>
      <th colspan="1" rowspan="1"><p>Product</p></th>
      <th colspan="1" rowspan="1"><p>Price</p></th>
    </tr>
    <tr>
      <td colspan="1" rowspan="1"><p>Chair</p></td>
      <td colspan="1" rowspan="1"><p>49</p></td>
    </tr>
    <tr>
      <td colspan="2" rowspan="1"><p>Prices without VAT</p></td>
    </tr>
  </tbody>
</table>
```

- Every cell holds a `<p>`. A cell can hold more than one paragraph, and a list, an image or a link as well.
- Merged cells carry `colspan` and `rowspan` larger than 1.
- Header cells are `<th>` elements inside the same `<tbody>` as the other rows; the editor writes no `<thead>`.
- The `<colgroup>` and the `min-width` styles come from the table extension: every column has `min-width: 25px` and the table the sum. A column you widened with the resize handle gets `<col style="width: 150px">` there, and the cells of that column carry `colwidth="150"`; the widths are pixels.

The [HTML source](html-source.md) modal shows the exact output of your editor.

## Requirements

Flux Pro 2.15.1 is the first version that bundles the TipTap table extensions (Table, TableRow, TableHeader and TableCell). They are off by default, and four lines in the setup block of `resources/js/app.js` turn them on, inside the `flux:editor` listener:

```javascript
    // Tables are bundled with Flux Pro 2.15.1+ but off by default. Enable them here and never install the
    // table extensions through npm: a second copy of ProseMirror breaks the editor.
    const tableExtensions = ['table', 'tableRow', 'tableHeader', 'tableCell']
    tableExtensions.forEach((name) => e.detail.enableExtension?.(name))
```

A new installation gets them from `php artisan flux-filemanager:install`. A setup block from before 1.10 does not have them, and without them the editor has no table node, so the button inserts nothing. Two ways to add them:

- Run `php artisan flux-filemanager:install` again and accept the `app.js` step. It adds the four lines after `e.detail.__fluxFilemanagerExtensionsRegistered = true` and changes nothing else in the block.
- Or copy them from [examples/app.js](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/blob/main/examples/app.js) to the same place yourself.

Then run `npm run build`, or restart `npm run dev`.

While the lines are missing, `php artisan flux-filemanager:check` warns with `resources/js/app.js enables Flux's table extensions` and prints the fix under it. In the editor the table button is disabled, and the browser console says why: "Flux Filemanager: the table extensions are not enabled, so the table button is disabled. Run php artisan flux-filemanager:install again; it adds the lines to resources/js/app.js." See [Troubleshooting](troubleshooting.md#the-table-button-does-nothing).

## Never install the table extensions through npm

Flux's documentation is explicit about this: use the table extensions Flux bundles, turned on with `e.detail.enableExtension()`, and do not install `@tiptap/extension-table` or its row, header and cell packages through npm, or register them with `e.detail.registerExtension()`. That loads a second copy of ProseMirror next to Flux's, and the editor then throws runtime errors. The four lines above are the whole setup.

## Styling

Inside the editor Flux's own stylesheet draws the tables: borders, the header cells, a tint on selected cells and the column resize handle, with dark mode variants. The package adds only what Flux has no rule for, in `resources/css/tiptap-table.css`: no margin on the paragraph in a cell, and header cells aligned left instead of centred. `resources/css/tiptap-image.css` imports it, and every installation already imports that file in `app.js`, so those two rules arrive with the package update without a change to your imports. To change the look of tables inside the editor, override Flux's rules with a selector more specific than theirs, which start with `[data-flux-editor] [data-slot="content"]`: Flux links its stylesheet where the editor renders, after your own CSS, so an equally specific rule of yours loses.

On the public page the editor's stylesheets do nothing, as with images. If you print the content inside Tailwind Typography's `prose` class, as [Your first editor](first-editor.md#step-4-show-the-content-on-the-site) does, tables are styled already. Without it, a few lines in `resources/css/app.css` are enough, with `content` as the class of the element that prints the HTML:

```css
.content table { width: 100%; border-collapse: collapse; }
.content th,
.content td { border: 1px solid #d4d4d8; padding: 0.5rem 0.75rem; vertical-align: top; }
.content th { background: #f4f4f5; font-weight: 600; text-align: left; }
.content th p,
.content td p { margin: 0; }
```

## The HTML source modal keeps tables

The [HTML source](html-source.md) modal applies the edited HTML through the schema of the editor, so a tag without an extension is dropped. With the table extensions on, `<table>` and its rows and cells survive Apply. In an `app.js` without the four lines the editor has no table node, and the hint under the text area then names `<table>` as a tag that Apply would drop.
