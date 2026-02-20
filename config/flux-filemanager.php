<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Filemanager URL
    |--------------------------------------------------------------------------
    |
    | The URL where Laravel Filemanager is accessible. This should match
    | the route prefix you configured in your routes file.
    |
    */

    'url' => '/filemanager',

    /*
    |--------------------------------------------------------------------------
    | Installation Checklist URL
    |--------------------------------------------------------------------------
    |
    | External/internal link used by the editor toolbar to quickly open the
    | filemanager installation checklist.
    |
    */

    'checklist_url' => env('FLUX_FILEMANAGER_CHECKLIST_URL', '/darvis/filemanager-checklist'),

    /*
    |--------------------------------------------------------------------------
    | Popup Window Dimensions
    |--------------------------------------------------------------------------
    |
    | Configure the dimensions of the Laravel Filemanager popup window.
    |
    */

    'popup' => [
        'width' => 900,
        'height' => 600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Resize Presets
    |--------------------------------------------------------------------------
    |
    | Define the preset width percentages shown in the resize menu.
    | You can customize these to match your design requirements.
    |
    */

    'resize_presets' => [
        '25%',
        '50%',
        '75%',
        '100%',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Width Range
    |--------------------------------------------------------------------------
    |
    | Set the minimum and maximum values for custom width input.
    |
    */

    'custom_width' => [
        'min' => 1,
        'max' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    |
    | Customize error messages shown to users.
    |
    */

    'messages' => [
        'popup_blocked' => 'Popup was blocked by your browser. Please allow popups for this site.',
        'no_images_selected' => 'No images were selected.',
        'filemanager_not_found' => 'Laravel Filemanager could not be loaded. Please check your installation.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Drag & Drop Settings
    |--------------------------------------------------------------------------
    |
    | Configure how drag & drop and paste images are handled.
    |
    */

    'drag_drop' => [
        // Upload method: 'base64' or 'upload'
        // - base64: Images are embedded directly in the HTML (no server upload)
        // - upload: Images are uploaded to server via Laravel Filemanager
        'method' => 'base64',

        // Upload endpoint (only used when method is 'upload')
        'upload_url' => '/filemanager/upload',

        // Maximum file size in bytes (default: 5MB)
        'max_file_size' => 5242880,

        // Allowed image types
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
    ],

];
