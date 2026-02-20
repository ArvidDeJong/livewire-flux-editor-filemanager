@props([
    'kbd' => null,
])

<flux:tooltip content="{{ __('flux-filemanager::filemanager.open_checklist') }}" :$kbd class="contents">
    <flux:editor.button type="button"
        data-filemanager-checklist="{{ config('flux-filemanager.checklist_url', '/darvis/filemanager-checklist') }}">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path
                d="M6.25 5.5H13.75M6.25 9.5H13.75M6.25 13.5H10M4.75 2.5H15.25C16.2165 2.5 17 3.2835 17 4.25V15.75C17 16.7165 16.2165 17.5 15.25 17.5H4.75C3.7835 17.5 3 16.7165 3 15.75V4.25C3 3.2835 3.7835 2.5 4.75 2.5Z"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </flux:editor.button>
</flux:tooltip>
