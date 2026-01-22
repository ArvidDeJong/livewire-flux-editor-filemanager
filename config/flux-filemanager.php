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

    'url' => env('FILEMANAGER_URL', '/cms/laravel-filemanager'),

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

];
