# Changelog

All notable changes to `livewire-flux-editor-filemanager` will be documented in this file.

## [Unreleased]

### Fixed

- Installer command no longer depends on unavailable `task()` helper, preventing runtime crashes on `php artisan flux-filemanager:install`.

### Changed

- Updated installer npm step to install the full required TipTap stack: `@tiptap/core`, `@tiptap/extension-image`, `@tiptap/extension-link`, and `prosemirror-state`.
- Refined installer “next steps” output to show current route and asset import paths.

### Documentation

- Added local package development workflow with symlink instructions.
- Added non-interactive installer example: `php artisan flux-filemanager:install --no-interaction`.
- Expanded installation JS examples to include Link/Image extensions and drag-drop helper imports.

## [1.1.1] - 2026-01-22

### Changed

- **Documentation Restructuring** - Improved KISS & DRY principles across all documentation
- Simplified INSTALLATION.md (47% shorter) - removed 160+ lines of duplicated TipTap code
- Simplified DRAG-DROP.md (38% shorter) - removed duplicated setup instructions
- Transformed WORKFLOW.md (61% shorter) - now a technical reference instead of installation guide
- All documentation now references `examples/app.js` for complete code (DRY principle)
- Simplified README.md (65% shorter) - focused on quick start instead of technical details
- Improved route configuration examples - reduced from 4 options to 1 basic + 1 advanced

### Improved

- Documentation is now more accessible for beginners
- Clear separation between installation, features, and technical reference
- No code duplication across documentation files
- Consistent structure and cross-references between docs

## [1.1.0] - 2026-01-22

### Added

- **Drag & Drop Support** - Drag images from computer directly into the editor
- **Paste Images** - Paste images from clipboard (screenshots, copied images)
- ProseMirror plugin for handling drop and paste events
- Base64 encoding for dropped/pasted images
- Support for multiple images at once
- Position control for dropped images
- Documentation for drag & drop functionality ([docs/DRAG-DROP.md](docs/DRAG-DROP.md))

### Changed

- Updated README with drag & drop features
- Enhanced Image extension with ProseMirror plugins
- Improved installation instructions with npm package requirements

### Technical

- Added `prosemirror-state` dependency requirement
- Implemented `handleDrop` and `handlePaste` handlers
- FileReader API integration for base64 conversion

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
