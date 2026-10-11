---
title: "HTML source"
nav_order: 8
description: "How editors open the HTML of the content in a modal with the HTML button, read it, change it and apply it, and what the editor does with tags it does not know."
---

# HTML source

## Open the HTML

Click the HTML button in the toolbar: a square with angle brackets, tooltip "View HTML". A modal opens with the HTML of the content in a text area, one block per line. It is the same HTML that `wire:model` receives.

It is not Flux's own code button. That one, `</>` with the tooltip "Code" and the shortcut ⌘E, marks the selected text as inline `<code>`, and it shows nothing when no text is selected.

## Edit and apply

Change the HTML and click Apply, or press Cmd/Ctrl + Enter. The content of the editor is replaced with the new HTML and Livewire receives it like any other change. Escape, Cancel, the close button or a click outside the modal closes it without applying.

What the editor does not know is dropped on Apply: a tag without an extension (for example `<iframe>` or `<script>`) and an attribute no extension declares. A `<table>` survives since 1.10, because the setup block enables Flux's table extensions; in an `app.js` from before 1.10 it is dropped like any other unknown tag, see [Tables](tables.md). Images keep `src`, `alt`, `title`, `width`, `class`, `style` and `data-align`; links keep `href`, `target`, `class` and `style`. That is TipTap's schema at work, not a sanitiser; do not rely on it to clean untrusted HTML, see [Uploads and security](uploads-and-security.md).

The modal tells you beforehand. While you type, the hint under the text area runs the HTML through the schema of the editor and names what does not survive: tags as `<iframe>`, attributes as `p[style]`. A `<div>` is listed too, because the tag goes even though its text stays. Tags the schema only renames, such as `<b>` to `<strong>`, are not listed. The warning never blocks Apply; it is there so a change that looks fine in the source does not vanish without a word.

In a disabled editor the modal opens read-only: the text area cannot be changed and there is no Apply button.

## Where the button is

The `full` toolbar preset has it, in the last group before the spacer. The `default` and `minimal` presets do not; `toolbar="default | html-source"` adds it to the default toolbar, and [Toolbar buttons](toolbar.md) has the syntax. In a toolbar you write in Blade, put it where you want it:

```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.separator />
        <flux:editor.html-source />
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

`@include('flux-filemanager::flux.editor.html-source')`, the form from before 1.9, renders the same button. A published copy of the view is used by both forms.

The tooltip and the modal texts come from the language files: `view_html`, `edit_html`, `html_source`, `html_source_hint`, `apply`, `cancel` and `close`. See [Localization](localization.md).
