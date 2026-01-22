@blaze

@props([
    'kbd' => null,
])

<flux:tooltip content="{{ __('flux-filemanager::filemanager.insert_file_link') }}" :$kbd class="contents">
    <flux:editor.button data-editor="file-link">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path
                d="M10.75 7.75H5.25C4.00736 7.75 3 8.75736 3 10C3 11.2426 4.00736 12.25 5.25 12.25H10.75M9.25 12.25H14.75C15.9926 12.25 17 11.2426 17 10C17 8.75736 15.9926 7.75 14.75 7.75H9.25M6.5 10H13.5"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </flux:editor.button>
</flux:tooltip>
