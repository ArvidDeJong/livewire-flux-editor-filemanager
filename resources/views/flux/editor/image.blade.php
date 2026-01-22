@blaze

@props([
    'kbd' => null,
])

<flux:tooltip content="{{ __('Afbeelding invoegen') }}" :$kbd class="contents">
    <flux:editor.button data-editor="image">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path
                d="M2.5 13.3333L6.66667 9.16667C7.08333 8.75 7.75 8.75 8.16667 9.16667L11.6667 12.6667M10.8333 11.8333L12.5 10.1667C12.9167 9.75 13.5833 9.75 14 10.1667L17.5 13.6667M10.8333 5.83333H10.8417M4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5H4.16667C3.25 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.25 17.5 4.16667 17.5Z"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </flux:editor.button>
</flux:tooltip>
