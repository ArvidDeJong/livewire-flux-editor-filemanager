---
title: "Localization"
nav_order: 9
description: "The language files of the editor buttons, menus and modals (en, nl, de), which texts they do not cover, how to change a text and how to add a language."
---

# Localization

## Which languages ship with the package

The package has language files for English (`en`), Dutch (`nl`) and German (`de`), each with the same keys. German is translated. In version 1.4.0 most values in the Dutch file are still the English text; publish the language files and translate them if you need Dutch.

The active Laravel locale (`app.locale`, or what you set with `App::setLocale()`) decides which file is used. That goes for the Blade tooltips on the toolbar buttons and for the JavaScript resize menu and modals: the component renders the translations as JSON and the JavaScript reads them from there.

## Texts that are not in the language files

- The two alerts `messages.popup_blocked` and `messages.filemanager_not_found` come from `config/flux-filemanager.php`. The values in the config win over the keys `popup_blocked_message` and `filemanager_error_message` in the language files. Change them in the config.
- The alerts for a refused dropped image (`Image too large. Maximum size is …MB`, `File type … is not allowed`, `Upload failed: …`) are fixed English strings in the JavaScript.
- The output of `flux-filemanager:install` and `flux-filemanager:check` is English.
- The file manager popup has its own translations, from Laravel Filemanager.

## Change a text

Publish the language files:

```bash
php artisan vendor:publish --tag=flux-filemanager-lang
```

They land in `lang/vendor/flux-filemanager/{locale}/filemanager.php`. Change what you need. You can delete the keys you do not change; Laravel merges your file over the package's file.

## Add a language

Copy `lang/vendor/flux-filemanager/en/filemanager.php` to a new locale directory, for example `lang/vendor/flux-filemanager/fr/filemanager.php`, and translate the values. The file has a comment per group: the toolbar tooltips, the image modal, the link modal, the buttons, the checklist page and the demo page.

Translate every key. For a key that is missing in your language, Blade falls back to your `fallback_locale` and the JavaScript falls back to its built-in English text.

Pull requests with a new language are welcome; add the file to `resources/lang` in the repository.

## Use the keys in your own views

The translation group is `flux-filemanager::filemanager`.

In a Blade view:

```blade
@lang('flux-filemanager::filemanager.insert_image')
```

In PHP:

```php
__('flux-filemanager::filemanager.insert_file_link');
```
