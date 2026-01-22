# Changelog

All notable changes to `livewire-flux-editor-filemanager` will be documented in this file.

## [Unreleased]

## [1.0.1] - 2026-01-22

### Added
- File link functionality for inserting downloadable file links (PDFs, Word docs, Excel, ZIP, etc.)
- File link modal with configurable options (link text, target, CSS classes, styles)
- File link button in editor toolbar

### Changed
- Translated all Dutch comments in Blade views to English
- Translated all Dutch strings in JavaScript modals to English
- Translated complete documentation to English (FILE-UPLOAD.md, IMAGE-EDITING.md, LOCALIZATION.md)
- Updated JavaScript modal labels, placeholders, and buttons to English

### Fixed
- Removed undefined `isEdit` variable in file link modal JavaScript

## [1.0.0] - 2026-01-22

### Added
- Laravel Filemanager integration for Flux TipTap Editor
- Image resize functionality (25%, 50%, 75%, 100%, custom percentage)
- Image align functionality (left, center, right)
- Reusable Blade component `<x-flux-filemanager-editor>`
- Three toolbar presets: default, minimal, full
- Custom toolbar support via slots
- Automated installation command `php artisan flux-filemanager:install`
- Configuration file for customizable settings
- Complete test suite (Unit + Feature tests)
- Example files for basic and advanced usage
- Complete documentation (README + WORKFLOW)
- MIT License

### Features
- WYSIWYG image display in editor
- Native TipTap Image extension support
- Click-to-resize menu with visual feedback
- Browser-native image handling
- Clean HTML output (no shortcodes)
- Generic implementation for all Livewire components
- Configurable filemanager URL and popup dimensions
- Customizable resize presets and error messages

### Requirements
- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Livewire 3.0+ or 4.0+
- Flux UI with Flux Pro
- Laravel Filemanager (UniSharp)
- TipTap Image Extension
