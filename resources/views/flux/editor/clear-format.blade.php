@props([
    'kbd' => null,
])

<flux:tooltip content="{{ __('flux-filemanager::filemanager.clear_formatting') }}" :$kbd class="contents">
    <flux:editor.button data-editor="clear-format">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
            <path d="M3 3.75A.75.75 0 0 1 3.75 3h9.5a.75.75 0 0 1 .75.75v2a.75.75 0 0 1-1.5 0V4.5H9.25v9.75a.75.75 0 0 1-1.5 0V4.5H4.5v1.25a.75.75 0 0 1-1.5 0v-2Z" />
            <path d="M13.22 11.22a.75.75 0 0 1 1.06 0l1.22 1.22 1.22-1.22a.75.75 0 1 1 1.06 1.06l-1.22 1.22 1.22 1.22a.75.75 0 1 1-1.06 1.06l-1.22-1.22-1.22 1.22a.75.75 0 1 1-1.06-1.06l1.22-1.22-1.22-1.22a.75.75 0 0 1 0-1.06Z" />
        </svg>
    </flux:editor.button>
</flux:tooltip>
