---
title: "Toolbar buttons"
nav_order: 4
description: "Turn the buttons of the Flux editor toolbar on and off with one string: the syntax, the three presets, every button from Flux and from the package, your own button, and a toolbar in Blade."
---

# Toolbar buttons

One string decides which buttons the toolbar shows, and in what order. This page has the syntax, the three presets, every button that Flux and this package have, and the two ways to add a button of your own.

## One line

`resources/views/livewire/page-form.blade.php`:

```blade
<x-flux-filemanager-editor wire:model="content" toolbar="heading | bold italic | image link file-link" />
```

The `toolbar` attribute lists button names separated by spaces. A `|` is a separator, a thin line between two groups; a `~` is a spacer that pushes the buttons after it to the right edge. It is the syntax of Flux's own `toolbar` prop on `<flux:editor>`, with the package buttons added as names, so a toolbar string from the [Flux documentation](https://fluxui.dev/components/editor#toolbar) works here as it is. More than one space between two names is fine.

A name that does not exist throws an exception when the page renders, and the message names it: `Flux component [editor.foo] does not exist.` Check the spelling against the table under [Every button](#every-button).

## The presets

- `minimal`: `bold italic | link`
- `default`: `heading | bold italic strike | bullet ordered blockquote | image link file-link | align`
- `full`: `heading | bold italic strike underline highlight subscript superscript | bullet ordered blockquote | image link file-link | align | code code-block hr clear-format | html-source ~ undo redo`

Without the attribute you get `default`. A preset name inside a string expands to its buttons: `toolbar="default | html-source"` is the default toolbar plus the HTML source button, and `toolbar="full | checklist"` is the full toolbar plus the checklist button. The three strings are the constant `Editor::PRESETS` in `src/View/Components/Editor.php`.

## Turn a button on or off

Copy the preset string and remove the name you do not want, or add the name you miss where the button should go. The default toolbar without the strike button:

```blade
<x-flux-filemanager-editor wire:model="content" toolbar="heading | bold italic | bullet ordered blockquote | image link file-link | align" />
```

The default toolbar with a horizontal rule button after the block quote:

```blade
<x-flux-filemanager-editor wire:model="content" toolbar="heading | bold italic strike | bullet ordered blockquote hr | image link file-link | align" />
```

When the new button may go at the end, the preset name is shorter: `toolbar="default | hr html-source"`.

## Every button

| Name | What it does | From | In presets |
| --- | --- | --- | --- |
| `heading` | A menu with text and heading 1 to 3 | Flux | default, full |
| `bold` | Bold | Flux | minimal, default, full |
| `italic` | Italic | Flux | minimal, default, full |
| `strike` | Strikethrough | Flux | default, full |
| `underline` | Underline | Flux | full |
| `highlight` | Highlights the selection, as `<mark>` | Flux | full |
| `subscript` | Subscript | Flux | full |
| `superscript` | Superscript | Flux | full |
| `bullet` | Bullet list | Flux | default, full |
| `ordered` | Numbered list | Flux | default, full |
| `blockquote` | Block quote | Flux | default, full |
| `link` | Flux's link dialog, for a URL you type | Flux | minimal, default, full |
| `align` | A menu with left, center and right | Flux | default, full |
| `code` | Inline code. It needs a selection and does nothing without one | Flux | full |
| `undo` | Undo | Flux | full |
| `redo` | Redo | Flux | full |
| \| | A separator, a thin line between two groups | Flux | minimal, default, full |
| `~` | A spacer, pushes the buttons after it to the right | Flux | full |
| `image` | Opens Laravel Filemanager in a popup and inserts the picked image, see [Image editing](image-editing.md) | Package | default, full |
| `file-link` | Opens Laravel Filemanager in a popup and inserts a link to the picked file, see [File links](file-links.md) | Package | default, full |
| `html-source` | Opens the HTML of the content in a modal, to read it or to edit it and apply it, see [HTML source](html-source.md) | Package | full |
| `hr` | Inserts a horizontal rule, `<hr>`. Flux's editor runs the command | Package | full |
| `code-block` | Turns the paragraph into a code block, `<pre><code>`, and back. Flux's editor runs the command, also for ⌘⌥C | Package | full |
| `clear-format` | Removes the marks (bold, italic, link and so on) and the block formatting (heading, list, quote) of the selection | Package | full |
| `checklist` | Opens the checklist page in a new tab. It needs `demo_routes`, so it is in no preset | Package | none |

## Your own button

A file in your application's `resources/views/flux/editor/` directory is a toolbar item with the name of the file, as the [Flux documentation](https://fluxui.dev/components/editor#toolbar) describes. `resources/views/flux/editor/copy.blade.php` copies the HTML of the content to the clipboard:

```blade
<flux:tooltip content="Copy the HTML" class="contents">
    <flux:editor.button x-on:click="navigator.clipboard?.writeText($el.closest('ui-editor').editor.getHTML())">
        <flux:icon.clipboard variant="mini" />
    </flux:editor.button>
</flux:tooltip>
```

Then `toolbar="default ~ copy"` puts it at the right edge of the default toolbar. The package buttons are built the same way: a `<flux:editor.button>` with a data attribute that a click handler reacts to (`data-editor`, or `data-filemanager-checklist` on the checklist button), Flux's own handler for `hr` and `code-block` and the package's JavaScript for the rest.

## A toolbar in Blade

With `:toolbar="false"` the component prints the slot instead of a preset, and you write the toolbar yourself:

```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.heading />
        <flux:editor.separator />
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.separator />
        <flux:editor.image />
        <flux:editor.link />
        <flux:editor.file-link />
        <flux:editor.spacer />
        <flux:editor.html-source />
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

Every name in the table is a component: `<flux:editor.image />` is the image button, `<flux:editor.separator />` the `|` and `<flux:editor.spacer />` the `~`. `@include('flux-filemanager::flux.editor.image')`, the form from before 1.9, renders the same button. A copy you published with `php artisan vendor:publish --tag=flux-filemanager-views` is used by all three forms: the string, the component tag and the include.

You need this form for a dropdown of your own, or for a button that needs something from the view, such as `wire:click` with an argument. For a plain button, a file in `resources/views/flux/editor/` and its name in the string is less code.

## Before 1.9

Up to 1.8, `toolbar` knew only the three preset names, and any other string silently rendered the `default` preset. The `default` and `full` presets contained the checklist button, which opens a 404 page unless the demo routes are on, and `full` had no highlight, subscript, superscript, undo or redo. A toolbar in Blade was the only way to change the buttons.
