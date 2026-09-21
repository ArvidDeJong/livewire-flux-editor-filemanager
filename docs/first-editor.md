---
title: "Your first editor"
nav_order: 3
description: "A complete, copy-paste example of a Livewire form with the Flux editor and Laravel Filemanager: migration, model, component, view, route and the public page."
---

# Your first editor

This page builds one working result: a form that edits a page with images and file links, saves the HTML to the database, and shows it on the site. Finish [Installation](installation.md) first, including "Check that it works".

The example has five files. Every block says which file it belongs to.

## Step 1: Create a table for the content

```bash
php artisan make:model Page -m
```

`database/migrations/xxxx_xx_xx_xxxxxx_create_pages_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
```

Use `longText`, not `text`. A dropped or pasted image is stored inside the HTML as base64 text by default, and one photo is larger than the 64 KB that a MySQL `text` column holds.

`app/Models/Page.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'content'];
}
```

Run `php artisan migrate`.

## Step 2: Create the Livewire component

`app/Livewire/PageForm.php`:

```php
<?php

namespace App\Livewire;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PageForm extends Component
{
    public Page $page;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string')]
    public string $content = '';

    public bool $saved = false;

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->title = $page->title;
        $this->content = (string) $page->content;
    }

    public function save(): void
    {
        $this->page->update($this->validate());

        $this->saved = true;
    }

    public function render(): View
    {
        return view('livewire.page-form');
    }
}
```

The editor is a form field like any other. `$content` holds the HTML as a string, `validate()` returns the title and the content, and `update()` saves both.

`resources/views/livewire/page-form.blade.php`:

```blade
<form wire:submit="save" class="space-y-6">
    <flux:input wire:model="title" label="Title" />

    <flux:field>
        <flux:label>Content</flux:label>
        <x-flux-filemanager-editor wire:model="content" />
        <flux:error name="content" />
    </flux:field>

    <flux:button type="submit" variant="primary">Save</flux:button>

    @if ($saved)
        <flux:text>Saved.</flux:text>
    @endif
</form>
```

`<x-flux-filemanager-editor>` renders `<flux:editor>` with the package's toolbar. `wire:model="content"` connects it to the `$content` property, and `<flux:error>` shows the validation message.

## Step 3: Add the routes

`routes/web.php`:

```php
use App\Livewire\PageForm;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/pages/{page}/edit', PageForm::class)->middleware('auth');

Route::get('/pages/{page}', function (Page $page) {
    return view('pages.show', ['page' => $page]);
});
```

The first route shows the form as a full page Livewire component, for logged-in users only. It renders inside your application's Livewire layout; see the [Livewire documentation on layouts](https://livewire.laravel.com/docs/components). That layout must load `resources/js/app.js` with `@vite` and contain `@fluxScripts`, otherwise the buttons do nothing.

Create a page to edit:

```bash
php artisan tinker --execute="App\Models\Page::create(['title' => 'About us'])"
```

Log in and open `/pages/1/edit`. Click the image button: Laravel Filemanager opens in a popup. Upload or pick an image and confirm, and it appears in the editor. Click it once to resize or align it, double-click it for the alt text. Press Save.

## Step 4: Show the content on the site

`resources/views/pages/show.blade.php`, inside your own layout:

```blade
<article class="prose max-w-none">
    {!! $page->content !!}
</article>
```

The content is HTML, so it is printed unescaped with `{!! !!}`. The `prose` class comes from the Tailwind typography plugin and styles headings, lists and links; leave it out if you do not use that plugin.

Two things about this step:

- **Unescaped output means you trust whoever edits the page.** The package does not sanitise the HTML. That is fine for your own team. For content from people you do not know, run it through an HTML sanitiser before you print it.
- **The package's stylesheets only style the editor.** On the public page you write the rules for the classes the editor puts on images, in `resources/css/app.css`:

```css
.tiptap-image { max-width: 100%; height: auto; }
.align-center { display: block; margin-inline: auto; }
.align-left { float: left; margin-inline-end: 1rem; }
.align-right { float: right; margin-inline-start: 1rem; }
```

Open `/pages/1`: the page shows the text, the image at the width you chose, and the file link.

## When something does not work

Run `php artisan flux-filemanager:check` and read [Troubleshooting](troubleshooting.md).

## Next

- [Configuration](configuration.md): the toolbar presets, the component attributes, every config key
- [Drag and drop](drag-and-drop.md): base64 or uploading to the file manager
- [Testing](testing.md): a test for the form you just built
