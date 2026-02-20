# Localization

The `livewire-flux-editor-filemanager` package supports multi-language functionality for the user interface.

## Supported Languages

The package comes with translations for:

- 🇳🇱 **Dutch** (`nl`)
- 🇬🇧 **English** (`en`)
- 🇩🇪 **German** (`de`)

## Configuration

### Set Default Language

The language is automatically determined by the `locale` setting in your Laravel application:

```php
// config/app.php
'locale' => 'nl', // or 'en', 'de'
```

### Change Language per Request

You can also dynamically change the language per request:

```php
// In a controller or middleware
App::setLocale('de');
```

Or via a route parameter:

```php
Route::get('/{locale}/cms/pages', function ($locale) {
    App::setLocale($locale);
    // ...
});
```

## Override Translations

If you want to customize or extend the default translations, publish the language files:

```bash
php artisan vendor:publish --tag=flux-filemanager-lang
```

This copies the translation files to:

```
lang/vendor/flux-filemanager/
├── nl/
│   └── filemanager.php
├── en/
│   └── filemanager.php
└── de/
    └── filemanager.php
```

### Example: Customize Dutch Translation

```php
// lang/vendor/flux-filemanager/nl/filemanager.php

return [
    'insert_image' => 'Insert photo',  // Previously: 'Insert image'
    'edit_image' => 'Edit photo',      // Previously: 'Edit image'
    // ... rest of the translations
];
```

## Add New Language

### 1. Create a New Translation File

For example for French (`fr`):

```bash
mkdir -p lang/vendor/flux-filemanager/fr
touch lang/vendor/flux-filemanager/fr/filemanager.php
```

### 2. Copy and Translate

Copy the contents of an existing file and translate the strings:

```php
<?php
// lang/vendor/flux-filemanager/fr/filemanager.php

return [
    // Image button
    'insert_image' => 'Insérer une image',

    // File link button
    'insert_file_link' => 'Insérer un lien de fichier',

    // Image edit modal
    'edit_image' => 'Modifier l\'image',
    'image' => 'Image',
    'alt_text' => 'Texte alternatif',
    'alt_text_placeholder' => 'Description de l\'image',
    'title' => 'Titre',
    'title_placeholder' => 'Texte de l\'info-bulle au survol',
    'width' => 'Largeur',
    'alignment' => 'Alignement',
    'alignment_none' => 'Aucun',
    'alignment_left' => 'Gauche',
    'alignment_center' => 'Centre',
    'alignment_right' => 'Droite',
    'extra_css_classes' => 'Classes CSS supplémentaires',
    'extra_css_classes_placeholder' => 'par ex. rounded shadow-lg',
    'extra_styles' => 'Styles supplémentaires',
    'extra_styles_placeholder' => 'par ex. border: 1px solid red;',

    // File link modal
    'edit_link' => 'Modifier le lien',
    'insert_link' => 'Insérer un lien de fichier',
    'file' => 'Fichier',
    'link_text' => 'Texte du lien',
    'link_text_placeholder' => 'Cliquez ici pour télécharger',
    'target' => 'Cible',
    'target_blank' => 'Nouvelle fenêtre (_blank)',
    'target_self' => 'Même fenêtre (_self)',
    'target_parent' => 'Fenêtre parente (_parent)',
    'target_top' => 'Fenêtre supérieure (_top)',
    'link_css_classes_placeholder' => 'par ex. btn btn-primary',
    'link_styles_placeholder' => 'par ex. color: blue; font-weight: bold;',

    // Buttons
    'cancel' => 'Annuler',
    'insert' => 'Insérer',
    'update' => 'Mettre à jour',

    // Validation
    'enter_link_text' => 'Veuillez saisir le texte du lien',
];
```

### 3. Set the Locale

```php
// config/app.php
'locale' => 'fr',
```

## Available Translation Keys

### Tooltips

- `insert_image` - Tooltip for insert image button
- `insert_file_link` - Tooltip for file link button

### Image Edit Modal

- `edit_image` - Modal title
- `image` - Label for image URL
- `alt_text` - Label for alt text
- `alt_text_placeholder` - Placeholder for alt text
- `title` - Label for title
- `title_placeholder` - Placeholder for title
- `width` - Label for width
- `alignment` - Label for alignment
- `alignment_none` - Option: no alignment
- `alignment_left` - Option: align left
- `alignment_center` - Option: align center
- `alignment_right` - Option: align right
- `extra_css_classes` - Label for CSS classes
- `extra_css_classes_placeholder` - Placeholder for CSS classes
- `extra_styles` - Label for inline styles
- `extra_styles_placeholder` - Placeholder for inline styles

### File Link Modal

- `edit_link` - Modal title (edit)
- `insert_link` - Modal title (insert)
- `file` - Label for file URL
- `link_text` - Label for link text
- `link_text_placeholder` - Placeholder for link text
- `target` - Label for target
- `target_blank` - Option: new window
- `target_self` - Option: same window
- `target_parent` - Option: parent window
- `target_top` - Option: top window
- `link_css_classes_placeholder` - Placeholder for link CSS classes
- `link_styles_placeholder` - Placeholder for link inline styles

### Buttons

- `cancel` - Cancel button
- `insert` - Insert button
- `update` - Update button

### Validation

- `enter_link_text` - Error message: link text is required

## Usage in Blade Templates

Translations are automatically used in Blade components:

```blade
{{-- Automatically translated to the active locale --}}
<flux:tooltip content="{{ __('flux-filemanager::filemanager.insert_image') }}">
    ...
</flux:tooltip>
```

## JavaScript Modals

**Note:** The JavaScript modals currently use hardcoded English text. These will be made translatable via a JavaScript localization helper in a future version.

## Fallback Behavior

If a translation is not found in the active locale, Laravel automatically falls back to the `fallback_locale` (default `en`):

```php
// config/app.php
'fallback_locale' => 'en',
```

## Best Practices

### 1. Always Use Translation Keys

```blade
{{-- Good ✓ --}}
{{ __('flux-filemanager::filemanager.insert_image') }}

{{-- Bad ✗ --}}
Insert image
```

### 2. Publish Translations for Customizations

Never modify files in `vendor/`. Publish first:

```bash
php artisan vendor:publish --tag=flux-filemanager-lang
```

### 3. Keep Translations Consistent

Use the same terminology throughout your application:

```php
// Choose one term and stick with it
'image' => 'Image',  // or 'Photo', but not both
```

### 4. Test All Locales

Test your application in all supported languages to check if:

- All strings are correctly translated
- The UI doesn't break due to longer texts
- Tooltips and labels are clear

## Troubleshooting

### Translations Not Loading

1. **Clear cache:**

   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. **Check locale:**

   ```php
   dd(App::getLocale()); // Should be 'nl', 'en' or 'de'
   ```

3. **Check file location:**
   ```
   lang/vendor/flux-filemanager/{locale}/filemanager.php
   ```

### Wrong Language Displayed

Check the locale setting:

```php
// In a controller or middleware
if (App::getLocale() !== 'nl') {
    App::setLocale('nl');
}
```

### New Translation Not Recognized

1. Publish the language files again
2. Clear all caches
3. Check if the key matches exactly

## Contributing

Want to add a new language or improve existing translations?

1. Fork the repository
2. Add your translation in `resources/lang/{locale}/filemanager.php`
3. Test the translation
4. Create a Pull Request

See [CONTRIBUTING.md](../CONTRIBUTING.md) for more information.
