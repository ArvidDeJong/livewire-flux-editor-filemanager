<?php

return [
    // Image button
    'insert_image' => 'Insert Image',
    
    // File link button
    'insert_file_link' => 'Insert File Link',
    
    // Image edit modal
    'edit_image' => 'Edit Image',
    'image' => 'Image',
    'alt_text' => 'Alt Text',
    'alt_text_placeholder' => 'Description of the image',
    'title' => 'Title',
    'title_placeholder' => 'Tooltip text on hover',
    'width' => 'Width',
    'alignment' => 'Alignment',
    'alignment_none' => 'None',
    'alignment_left' => 'Left',
    'alignment_center' => 'Center',
    'alignment_right' => 'Right',
    'extra_css_classes' => 'Extra CSS Classes',
    'extra_css_classes_placeholder' => 'e.g. rounded shadow-lg',
    'extra_styles' => 'Extra Styles',
    'extra_styles_placeholder' => 'e.g. border: 1px solid red;',
    
    // File link modal
    'edit_link' => 'Edit Link',
    'insert_link' => 'Insert File Link',
    'file' => 'File',
    'link_text' => 'Link Text',
    'link_text_placeholder' => 'Click here to download',
    'target' => 'Target',
    'target_blank' => 'New window (_blank)',
    'target_self' => 'Same window (_self)',
    'target_parent' => 'Parent window (_parent)',
    'target_top' => 'Top window (_top)',
    'link_css_classes_placeholder' => 'e.g. btn btn-primary',
    'link_styles_placeholder' => 'e.g. color: blue; font-weight: bold;',
    
    // Buttons
    'cancel' => 'Cancel',
    'insert' => 'Insert',
    'update' => 'Update',
    
    // Validation
    'enter_link_text' => 'Please enter link text',

    // Messages
    'popup_blocked_message' => 'Popup was blocked by your browser. Please allow popups for this site.',
    'filemanager_error_message' => 'Laravel Filemanager could not be loaded. Please check your installation.',

    // Checklist
    'open_checklist' => 'Open filemanager installation checklist',
    'checklist_title' => 'Darvis Filemanager Checklist',
    'checklist_summary' => 'Check of the main installation components. Status: :okCount/:totalCount passed.',
    'checklist_installed' => 'Package darvis/livewire-flux-editor-filemanager installed',
    'checklist_package_available' => 'Laravel Filemanager package available',
    'checklist_flux_config_available' => 'Config flux-filemanager.php available',
    'checklist_lfm_config_available' => 'Config lfm.php available',
    'checklist_routes_enabled' => 'LFM package routes enabled',
    'checklist_prefix_set' => 'LFM url_prefix is set to filemanager',
    'checklist_js_init_available' => 'Flux Filemanager JS init available (initLaravelFilemanager)',
    'checklist_app_url_matches_host' => 'APP_URL host komt overeen met huidige host',
    'checklist_status_ok' => 'OK',
    'checklist_status_missing' => 'MISSING',
    'url' => 'URL',

    // Image resize UI
    'align_left_title' => 'Align left',
    'align_center_title' => 'Align center',
    'align_right_title' => 'Align right',
    'apply' => 'Apply',

    // Demo page
    'demo_page_title' => 'Flux Filemanager Editor Demo',
    'demo_title' => 'Editor Demo',
    'demo_preview' => 'Preview',
    'demo_save' => 'Save',
    'demo_saved' => 'Content saved!',
    'demo_content_label' => 'Content',
    'demo_login_required_heading' => 'Login required',
    'demo_login_required_text' => 'You must be logged in to use the Laravel Filemanager features in this editor.',
    'demo_welcome_heading' => 'Welcome to the Editor Demo',
    'demo_welcome_text' => 'Start typing or use the toolbar to add images and links!',
    'demo_features_intro' => 'Try these features:',
    'demo_feature_upload_images' => 'Click 🖼️ to upload images',
    'demo_feature_add_file_links' => 'Click 🔗 to add file links',
    'demo_feature_drag_drop' => 'Drag & drop images directly into the editor',
    'demo_feature_paste' => 'Paste screenshots with Cmd/Ctrl + V',
    'demo_feature_single_click_resize' => 'Single click on images to resize',
    'demo_feature_double_click_edit' => 'Double click on images to edit details',
    'demo_not_set' => 'not set',
    'demo_app_url_heading' => 'APP_URL must match the current host for Laravel Filemanager',
    'demo_app_url_status' => 'APP_URL host: :appUrlHost · Current host: :currentHost',
    'demo_app_url_fix' => 'Fix: set APP_URL in your .env to this host (including scheme), then clear config cache.',
    'demo_app_url_command' => 'Command: php artisan config:clear',
];
