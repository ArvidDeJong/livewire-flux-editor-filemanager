---
title: "Testing"
nav_order: 10
description: "Test a Livewire form that uses the Flux editor with Laravel Filemanager in your own application: saving the HTML, validation, the guest redirect and CI."
---

# Testing

## What you can and cannot test in PHP

The PHP side of this package is small: a Blade component, two commands and a config file. The buttons, the popup, the resize menu and drag and drop are JavaScript, and a PHP test does not run JavaScript. The package calls no external service, so there is nothing to fake.

In your application you test three things:

1. Your Livewire component saves and validates the HTML.
2. The page renders the editor.
3. The file manager is closed for guests.

Try the JavaScript by hand in a browser, for example on the demo page (see [Installation](installation.md#try-the-demo-pages-first-optional)).

## A complete test for the form

This test belongs to the `PageForm` component from [Your first editor](first-editor.md). It uses [Pest](https://pestphp.com), the test runner in new Laravel applications.

`tests/Feature/PageFormTest.php`:

```php
<?php

use App\Livewire\PageForm;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the editor with the file manager buttons', function () {
    $page = Page::create(['title' => 'About us']);

    Livewire::test(PageForm::class, ['page' => $page])
        ->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="file-link"', false);
});

it('saves the HTML from the editor', function () {
    $page = Page::create(['title' => 'About us']);

    Livewire::test(PageForm::class, ['page' => $page])
        ->set('content', '<p>Hello</p><img src="/storage/photos/1/office.jpg" class="tiptap-image" alt="Office">')
        ->call('save')
        ->assertHasNoErrors();

    expect($page->fresh()->content)->toContain('/storage/photos/1/office.jpg');
});

it('requires content', function () {
    $page = Page::create(['title' => 'About us']);

    Livewire::test(PageForm::class, ['page' => $page])
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['content' => 'required']);
});

it('sends a guest who opens the file manager to the login page', function () {
    $this->get('/filemanager')->assertRedirect('/login');
});
```

Run it with `php artisan test --filter=PageFormTest`.

`Livewire::test()` mounts the component without a browser. `set('content', …)` does what the editor does when it syncs `wire:model`: it puts an HTML string in the property. The last test asks Laravel Filemanager's own route for the page as a guest; with `auth` in `middlewares` in `config/lfm.php`, Laravel redirects to the route named `login`. Change `/login` if your login page has another URL.

## Rendering the component outside Livewire

`$this->blade()` renders a Blade string without the `web` middleware, so nothing shares the `$errors` variable that Flux's views read. Share an empty error bag first:

```php
use Illuminate\Support\ViewErrorBag;

it('renders the editor in a plain Blade view', function () {
    $this->app['view']->share('errors', new ViewErrorBag);

    $this->blade('<x-flux-filemanager-editor wire:model="content" />')
        ->assertSee('data-editor="image"', false)
        ->assertSee('data-flux-filemanager-config', false);
});
```

## Full page requests and Vite

A test that requests a full page, such as `$this->get('/pages/1/edit')`, renders your layout with `@vite`. On a machine without a build that throws an exception about the Vite manifest. Call `$this->withoutVite()` at the start of such a test.

## Check the installation in CI or a deploy

```bash
php artisan flux-filemanager:check
```

The command exits with code 1 when a check fails, so a pipeline stops on it. It checks the files on that machine: run it after `npm ci` and `npm run build`, because it looks for the TipTap packages in `node_modules` and for `public/build/manifest.json`.
