---
title: "HTML source"
nav_order: 7
description: "How editors open the HTML of the content in a modal with the HTML button, read it, change it and apply it, and what the editor does with tags it does not know."
---

# HTML source

## Open the HTML

Click the HTML button in the toolbar: a square with angle brackets, tooltip "View HTML". A modal opens with the HTML of the content in a text area, one block per line. It is the same HTML that `wire:model` receives.

It is not Flux's own code button. That one, `</>` with the tooltip "Code" and the shortcut ⌘E, marks the selected text as inline `<code>`, and it shows nothing when no text is selected.

## Edit and apply

Change the HTML and click Apply, or press Cmd/Ctrl + Enter. The content of the editor is replaced with the new HTML and Livewire receives it like any other change. Escape, Cancel, the close button or a click outside the modal closes it without applying.

What the editor does not know is dropped on Apply: a tag without an extension (for example `<table>` without the Table extension, `<iframe>`, `<script>`) and an attribute no extension declares. Images keep `src`, `alt`, `title`, `width`, `class`, `style` and `data-align`; links keep `href`, `target`, `class` and `style`. That is TipTap's schema at work, not a sanitiser; do not rely on it to clean untrusted HTML, see [Uploads and security](uploads-and-security.md).

In a disabled editor the modal opens read-only: the text area cannot be changed and there is no Apply button.

## Where the button is

The `full` toolbar preset has it, after Flux's code button. The `default` and `minimal` presets do not. In a custom toolbar, include it where you want it:

```blade
<x-flux-filemanager-editor wire:model="content" :toolbar="false">
    <flux:editor.toolbar>
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.separator />
        @include('flux-filemanager::flux.editor.html-source')
    </flux:editor.toolbar>
</x-flux-filemanager-editor>
```

The tooltip and the modal texts come from the language files: `view_html`, `edit_html`, `html_source`, `html_source_hint`, `apply`, `cancel` and `close`. See [Localization](localization.md).
