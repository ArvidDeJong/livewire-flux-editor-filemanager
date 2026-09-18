# Security policy

This package puts images and file links from Laravel Filemanager into the Flux editor and adds a few pages and an installer to your application. A problem that lets a visitor do more than that counts as a security issue.

## Supported versions

Only the latest minor release of 1.x receives security fixes. Upgrade before reporting.

## Reporting a vulnerability

Please do **not** open a public issue. Report it privately instead:

- via [GitHub private vulnerability reporting](https://github.com/ArvidDeJong/livewire-flux-editor-filemanager/security/advisories/new), or
- by email to info@arvid.nl.

Include the package version, the Laravel, Livewire and Flux Pro versions, and the steps or request that show the problem.

You will get a reply within a week. Once a fix is released, the advisory is published and you are credited, unless you prefer not to be.

## Out of scope

- **The HTML the editor produces.** Editors can add classes, inline styles and base64 images, and your application stores and renders that HTML. Deciding who may use the editor, and sanitising the content before showing it to other visitors, is your application's job. Treat editor content as trusted only when the editors are.
- **Laravel Filemanager and Flux.** Upload limits, file type checks and access to the `/filemanager` routes are configured in `config/lfm.php` and belong to [unisharp/laravel-filemanager](https://github.com/UniSharp/laravel-filemanager). Problems in the editor component itself belong to Flux Pro.
- **The demo pages.** `/darvis/editor-demo` and `/darvis/filemanager-checklist` are off by default (`flux-filemanager.demo_routes`) and have no authentication. Don't enable them in production. See [Installation](https://arviddejong.github.io/livewire-flux-editor-filemanager/installation.html#demo-pages).
