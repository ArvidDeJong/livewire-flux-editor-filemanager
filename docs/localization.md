---
title: Localization
nav_order: 7
description: The editor buttons, menus and modals in English, Dutch and German, how to change a text and how to add a language.
---

# Localization

The package ships English (`en`), Dutch (`nl`) and German (`de`). The active Laravel locale decides which one you see, in the Blade tooltips and in the JavaScript resize menu and modals alike: the component renders the translations as JSON and the JavaScript reads them from there. Keys missing in a language fall back to `fallback_locale`, and the JavaScript falls back to English.

## Changing a text

Publish the language files:

```bash
php artisan vendor:publish --tag=flux-filemanager-lang
```

They land in `lang/vendor/flux-filemanager/{locale}/filemanager.php`. Change what you need; keys you leave out keep the package text.

## Adding a language

Copy `lang/vendor/flux-filemanager/en/filemanager.php` to a new locale directory and translate the values. Every key is in that file, with a comment per group: the toolbar tooltips, the image modal, the link modal, the buttons, the checklist page and the demo page. The package's own tests check that its three languages have the same keys; do the same for yours if you publish them.

Pull requests with a new language are welcome; add it to `resources/lang` in the repository.

## Using the keys yourself

The group is `flux-filemanager::filemanager`. In Blade:

```blade
@lang('flux-filemanager::filemanager.insert_image')
```

In PHP:

```php
__('flux-filemanager::filemanager.insert_file_link');
```
