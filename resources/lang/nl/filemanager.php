<?php

return [
    // Image button
    'insert_image' => 'Afbeelding invoegen',

    // File link button
    'insert_file_link' => 'Bestandslink invoegen',

    // HTML source button and modal
    'view_html' => 'HTML bekijken',
    'edit_html' => 'HTML bewerken',
    'html_source' => 'HTML',
    'html_source_hint' => 'Toepassen vervangt de inhoud door deze HTML. Tags en attributen die de editor niet kent, vervallen.',
    'close' => 'Sluiten',

    // Image edit modal
    'edit_image' => 'Afbeelding bewerken',
    'image' => 'Afbeelding',
    'alt_text' => 'Alt-tekst',
    'alt_text_placeholder' => 'Omschrijving van de afbeelding',
    'title' => 'Titel',
    'title_placeholder' => 'Tooltiptekst bij hover',
    'width' => 'Breedte',
    'alignment' => 'Uitlijning',
    'alignment_none' => 'Geen',
    'alignment_left' => 'Links',
    'alignment_center' => 'Midden',
    'alignment_right' => 'Rechts',
    'extra_css_classes' => 'Extra CSS-classes',
    'extra_css_classes_placeholder' => 'bijv. rounded shadow-lg',
    'extra_styles' => 'Extra styles',
    'extra_styles_placeholder' => 'bijv. border: 1px solid red;',

    // File link modal
    'edit_link' => 'Link bewerken',
    'insert_link' => 'Bestandslink invoegen',
    'file' => 'Bestand',
    'link_text' => 'Linktekst',
    'link_text_placeholder' => 'Klik hier om te downloaden',
    'target' => 'Doel',
    'target_blank' => 'Nieuw venster (_blank)',
    'target_self' => 'Zelfde venster (_self)',
    'target_parent' => 'Bovenliggend venster (_parent)',
    'target_top' => 'Bovenste venster (_top)',
    'link_css_classes_placeholder' => 'bijv. btn btn-primary',
    'link_styles_placeholder' => 'bijv. color: blue; font-weight: bold;',

    // Buttons
    'cancel' => 'Annuleren',
    'insert' => 'Invoegen',
    'update' => 'Bijwerken',

    // Validation
    'enter_link_text' => 'Vul een linktekst in',

    // Messages
    'popup_blocked_message' => 'De pop-up is door je browser geblokkeerd. Sta pop-ups toe voor deze site.',
    'filemanager_error_message' => 'Laravel Filemanager kon niet worden geladen. Controleer je installatie.',

    // Checklist
    'open_checklist' => 'Installatiechecklist van de filemanager openen',
    'checklist_title' => 'Darvis Filemanager-checklist',
    'checklist_summary' => 'Controle van de belangrijkste onderdelen van de installatie. Status: :okCount/:totalCount geslaagd.',
    'checklist_installed' => 'Package darvis/livewire-flux-editor-filemanager geïnstalleerd',
    'checklist_package_available' => 'Laravel Filemanager-package beschikbaar',
    'checklist_flux_config_available' => 'Config flux-filemanager.php beschikbaar',
    'checklist_lfm_config_available' => 'Config lfm.php beschikbaar',
    'checklist_routes_enabled' => 'LFM-packageroutes ingeschakeld',
    'checklist_prefix_set' => 'LFM url_prefix staat op filemanager',
    'checklist_js_init_available' => 'Flux Filemanager JS-init beschikbaar (initLaravelFilemanager)',
    'checklist_app_url_matches_host' => 'APP_URL-host komt overeen met de huidige host',
    'checklist_status_ok' => 'OK',
    'checklist_status_missing' => 'ONTBREEKT',
    'checklist_protected' => 'De bestandsmanager zit achter authenticatie',
    'checklist_storage_link' => 'public/storage is gelinkt',
    'checklist_npm_packages' => 'De TipTap-packages zijn geïnstalleerd',
    'checklist_build_current' => 'De assets zijn gebouwd en actueel',
    'checklist_app_url' => 'APP_URL is ingesteld',
    'checklist_cli_hint' => 'Dezelfde checks in de terminal: php artisan flux-filemanager:check',
    'url' => 'URL',

    // Image resize UI
    'align_left_title' => 'Links uitlijnen',
    'align_center_title' => 'Centreren',
    'align_right_title' => 'Rechts uitlijnen',
    'apply' => 'Toepassen',

    // Demo page
    'demo_page_title' => 'Flux Filemanager Editor-demo',
    'demo_title' => 'Editor-demo',
    'demo_preview' => 'Voorbeeld',
    'demo_save' => 'Opslaan',
    'demo_saved' => 'Inhoud opgeslagen!',
    'demo_content_label' => 'Inhoud',
    'demo_login_required_heading' => 'Inloggen vereist',
    'demo_login_required_text' => 'Je moet ingelogd zijn om de Laravel Filemanager-functies in deze editor te gebruiken.',
    'demo_welcome_heading' => 'Welkom bij de editor-demo',
    'demo_welcome_text' => 'Begin met typen of gebruik de werkbalk om afbeeldingen en links toe te voegen!',
    'demo_features_intro' => 'Probeer deze functies:',
    'demo_feature_upload_images' => 'Klik op 🖼️ om afbeeldingen te uploaden',
    'demo_feature_add_file_links' => 'Klik op 🔗 om bestandslinks toe te voegen',
    'demo_feature_drag_drop' => 'Sleep afbeeldingen rechtstreeks in de editor',
    'demo_feature_paste' => 'Plak schermafbeeldingen met Cmd/Ctrl + V',
    'demo_feature_single_click_resize' => 'Klik één keer op een afbeelding om te schalen',
    'demo_feature_double_click_edit' => 'Dubbelklik op een afbeelding om de details te bewerken',
    'demo_not_set' => 'niet ingesteld',
    'demo_app_url_heading' => 'APP_URL moet voor Laravel Filemanager overeenkomen met de huidige host',
    'demo_app_url_status' => 'APP_URL-host: :appUrlHost · Huidige host: :currentHost',
    'demo_app_url_fix' => 'Oplossing: zet APP_URL in je .env op deze host (inclusief schema) en leeg daarna de configcache.',
    'demo_app_url_command' => 'Commando: php artisan config:clear',
];
