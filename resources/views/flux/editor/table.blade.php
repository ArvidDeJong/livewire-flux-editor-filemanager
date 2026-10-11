<flux:dropdown position="bottom" align="start">
    <flux:editor.button data-editor="table-menu" tooltip="{{ __('flux-filemanager::filemanager.table') }}">
        {{-- Credit: Heroicons (https://heroicons.com), mini table-cells --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M.99 5.24A2.25 2.25 0 0 1 3.25 3h13.5A2.25 2.25 0 0 1 19 5.25l.01 9.5A2.25 2.25 0 0 1 16.76 17H3.26A2.267 2.267 0 0 1 1 14.74l-.01-9.5Zm8.26 9.52v-.625a.75.75 0 0 0-.75-.75H3.25a.75.75 0 0 0-.75.75v.615c0 .414.336.75.75.75h5.373a.75.75 0 0 0 .627-.74Zm1.5 0a.75.75 0 0 0 .627.74h5.373a.75.75 0 0 0 .75-.75v-.615a.75.75 0 0 0-.75-.75H11.5a.75.75 0 0 0-.75.75v.625Zm6.75-3.63v-.625a.75.75 0 0 0-.75-.75H11.5a.75.75 0 0 0-.75.75v.625c0 .414.336.75.75.75h5.25a.75.75 0 0 0 .75-.75Zm-8.25 0v-.625a.75.75 0 0 0-.75-.75H3.25a.75.75 0 0 0-.75.75v.625c0 .414.336.75.75.75H8.5a.75.75 0 0 0 .75-.75ZM17.5 7.5v-.625a.75.75 0 0 0-.75-.75H11.5a.75.75 0 0 0-.75.75V7.5c0 .414.336.75.75.75h5.25a.75.75 0 0 0 .75-.75Zm-8.25 0v-.625a.75.75 0 0 0-.75-.75H3.25a.75.75 0 0 0-.75.75V7.5c0 .414.336.75.75.75H8.5a.75.75 0 0 0 .75-.75Z"
                clip-rule="evenodd" />
        </svg>
    </flux:editor.button>

    <flux:menu>
        <flux:menu.item data-editor="table">{{ __('flux-filemanager::filemanager.insert_table') }}</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item data-editor="table-add-row-before">{{ __('flux-filemanager::filemanager.add_row_before') }}</flux:menu.item>
        <flux:menu.item data-editor="table-add-row-after">{{ __('flux-filemanager::filemanager.add_row_after') }}</flux:menu.item>
        <flux:menu.item data-editor="table-delete-row">{{ __('flux-filemanager::filemanager.delete_row') }}</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item data-editor="table-add-column-before">{{ __('flux-filemanager::filemanager.add_column_before') }}</flux:menu.item>
        <flux:menu.item data-editor="table-add-column-after">{{ __('flux-filemanager::filemanager.add_column_after') }}</flux:menu.item>
        <flux:menu.item data-editor="table-delete-column">{{ __('flux-filemanager::filemanager.delete_column') }}</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item data-editor="table-toggle-header-row">{{ __('flux-filemanager::filemanager.toggle_header_row') }}</flux:menu.item>
        <flux:menu.item data-editor="table-toggle-header-column">{{ __('flux-filemanager::filemanager.toggle_header_column') }}</flux:menu.item>
        <flux:menu.item data-editor="table-merge-or-split">{{ __('flux-filemanager::filemanager.merge_or_split_cells') }}</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item data-editor="table-delete" variant="danger">{{ __('flux-filemanager::filemanager.delete_table') }}</flux:menu.item>
    </flux:menu>
</flux:dropdown>
