# Contributing

Contributions are welcome: bug reports, fixes, documentation and ideas.

## Before you start

- **Bugs:** open an [issue](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/issues/new/choose) with the steps to reproduce.
- **Features:** open an issue first. This package stays small on purpose, so let's agree a feature fits before you build it.
- **Security issues:** don't open an issue; see [SECURITY.md](SECURITY.md).

## Development

```bash
git clone https://github.com/ArvidDeJong/livewire-flux-editor-filemanager.git
cd livewire-flux-editor-filemanager
composer config http-basic.composer.fluxui.dev your-email your-flux-license-key
composer install

composer test      # Pest
composer lint      # Pint, check only (composer format fixes)
composer analyse   # Larastan, level 8
```

`livewire/flux-pro` is a private package, so you need a [Flux Pro](https://fluxui.dev) licence to install the dependencies. CI runs the tests on PHP 8.2-8.4 with Laravel 11, 12 and 13, on the lowest and the latest dependencies, using the maintainer's licence.

To try the editor in a browser, install the package in a Laravel app with Flux Pro, set `FLUX_FILEMANAGER_DEMO_ROUTES=true` and open `/darvis/editor-demo`.

## Pull requests

- Add or update tests for every change in behaviour.
- Keep the public API compatible within 1.x: the `<x-flux-filemanager-editor>` component and its `id`, `rows` and `toolbar` attributes, the keys in `config/flux-filemanager.php`, the `flux-filemanager:install` command, `initLaravelFilemanager()` and the paths of `resources/js/laravel-filemanager.js` and the two stylesheets in `resources/css/`, which host apps import by path.
- Write code, comments and messages in English. New translation keys go into `resources/lang/en`, `nl` and `de`.
- Update `docs/`, `CHANGELOG.md` (under `Unreleased`) and `resources/boost/` when users will notice the change.
- The documentation in `docs/` is also the website. Don't write `{{ }}` or `{% %}` there; Jekyll would render it.

## Code of conduct

This project follows the [Contributor Covenant](CODE_OF_CONDUCT.md).
