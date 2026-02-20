<?php

return [
    // Image button
    'insert_image' => 'Bild einfügen',
    
    // File link button
    'insert_file_link' => 'Dateilink einfügen',
    
    // Image edit modal
    'edit_image' => 'Bild Bearbeiten',
    'image' => 'Bild',
    'alt_text' => 'Alt Text',
    'alt_text_placeholder' => 'Beschreibung des Bildes',
    'title' => 'Titel',
    'title_placeholder' => 'Tooltip-Text beim Hover',
    'width' => 'Breite',
    'alignment' => 'Ausrichtung',
    'alignment_none' => 'Keine',
    'alignment_left' => 'Links',
    'alignment_center' => 'Mitte',
    'alignment_right' => 'Rechts',
    'extra_css_classes' => 'Extra CSS Klassen',
    'extra_css_classes_placeholder' => 'z.B. rounded shadow-lg',
    'extra_styles' => 'Extra Styles',
    'extra_styles_placeholder' => 'z.B. border: 1px solid red;',
    
    // File link modal
    'edit_link' => 'Link Bearbeiten',
    'insert_link' => 'Dateilink Einfügen',
    'file' => 'Datei',
    'link_text' => 'Link Text',
    'link_text_placeholder' => 'Hier klicken zum Herunterladen',
    'target' => 'Ziel',
    'target_blank' => 'Neues Fenster (_blank)',
    'target_self' => 'Gleiches Fenster (_self)',
    'target_parent' => 'Eltern-Fenster (_parent)',
    'target_top' => 'Top-Fenster (_top)',
    'link_css_classes_placeholder' => 'z.B. btn btn-primary',
    'link_styles_placeholder' => 'z.B. color: blue; font-weight: bold;',
    
    // Buttons
    'cancel' => 'Abbrechen',
    'insert' => 'Einfügen',
    'update' => 'Aktualisieren',
    
    // Validation
    'enter_link_text' => 'Bitte Link-Text eingeben',

    // Messages
    'popup_blocked_message' => 'Das Popup wurde von Ihrem Browser blockiert. Bitte erlauben Sie Popups für diese Website.',
    'filemanager_error_message' => 'Laravel Filemanager konnte nicht geladen werden. Bitte prüfen Sie Ihre Installation.',

    // Checklist
    'open_checklist' => 'Filemanager-Installationscheckliste öffnen',
    'checklist_title' => 'Darvis Filemanager Checkliste',
    'checklist_summary' => 'Prüfung der wichtigsten Installationskomponenten. Status: :okCount/:totalCount erfolgreich.',
    'checklist_installed' => 'Paket darvis/livewire-flux-editor-filemanager installiert',
    'checklist_package_available' => 'Laravel Filemanager Paket verfügbar',
    'checklist_flux_config_available' => 'Config flux-filemanager.php verfügbar',
    'checklist_lfm_config_available' => 'Config lfm.php verfügbar',
    'checklist_routes_enabled' => 'LFM-Paketrouten aktiviert',
    'checklist_prefix_set' => 'LFM url_prefix ist auf filemanager gesetzt',
    'checklist_js_init_available' => 'Flux Filemanager JS-Init verfügbar (initLaravelFilemanager)',
    'checklist_app_url_matches_host' => 'APP_URL-Host entspricht dem aktuellen Host',
    'checklist_status_ok' => 'OK',
    'checklist_status_missing' => 'FEHLT',
    'url' => 'URL',

    // Image resize UI
    'align_left_title' => 'Links ausrichten',
    'align_center_title' => 'Zentriert ausrichten',
    'align_right_title' => 'Rechts ausrichten',
    'apply' => 'Anwenden',

    // Demo page
    'demo_page_title' => 'Flux Filemanager Editor Demo',
    'demo_title' => 'Editor-Demo',
    'demo_preview' => 'Vorschau',
    'demo_save' => 'Speichern',
    'demo_saved' => 'Inhalt gespeichert!',
    'demo_content_label' => 'Inhalt',
    'demo_login_required_heading' => 'Anmeldung erforderlich',
    'demo_login_required_text' => 'Sie müssen angemeldet sein, um die Laravel-Filemanager-Funktionen in diesem Editor zu verwenden.',
    'demo_welcome_heading' => 'Willkommen zur Editor-Demo',
    'demo_welcome_text' => 'Beginnen Sie zu tippen oder verwenden Sie die Toolbar, um Bilder und Links hinzuzufügen!',
    'demo_features_intro' => 'Probieren Sie diese Funktionen aus:',
    'demo_feature_upload_images' => 'Klicken Sie auf 🖼️, um Bilder hochzuladen',
    'demo_feature_add_file_links' => 'Klicken Sie auf 🔗, um Dateilinks hinzuzufügen',
    'demo_feature_drag_drop' => 'Ziehen Sie Bilder per Drag & Drop direkt in den Editor',
    'demo_feature_paste' => 'Fügen Sie Screenshots mit Cmd/Ctrl + V ein',
    'demo_feature_single_click_resize' => 'Einfachklick auf Bilder zum Ändern der Größe',
    'demo_feature_double_click_edit' => 'Doppelklick auf Bilder zum Bearbeiten von Details',
    'demo_not_set' => 'nicht gesetzt',
    'demo_app_url_heading' => 'APP_URL muss für Laravel Filemanager mit dem aktuellen Host übereinstimmen',
    'demo_app_url_status' => 'APP_URL-Host: :appUrlHost · Aktueller Host: :currentHost',
    'demo_app_url_fix' => 'Fix: Setzen Sie APP_URL in Ihrer .env auf diesen Host (inklusive Schema) und leeren Sie dann den Config-Cache.',
    'demo_app_url_command' => 'Befehl: php artisan config:clear',
];
