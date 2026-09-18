---
title: Your first editor
nav_order: 3
description: A complete worked example, from migration to published page, with the three things that usually go wrong the first time.
---

# Your first editor

One page of content, edited with images and file links, saved to the database and shown on the site. Copy it as it is; it works after the installer has run.

New to Laravel Filemanager or Flux? You only need to know that this package adds two buttons to the Flux editor and that everything you save is plain HTML.

## 1. Somewhere to keep the content

```bash
php artisan make:model Page -m
```

In the migration:

```php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->longText('content')->nullable();
    $table->timestamps();
});
```

`longText`, not `text`: a base64 image (the default for dropped and pasted files) is easily larger than the 64 KB a `text` column holds, and MySQL truncates silently. In the model:

```php
class Page extends Model
{
    protected $fillable = ['title', 'content'];
}
```

Then `php artisan migrate`.

## 2. The form

```bash
php artisan make:livewire PageForm
```

```php
namespace App\Livewire;

use App\Models\Page;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PageForm extends Component
{
    public Page $page;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string')]
    public string $content = '';

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->title = $page->title;
        $this->content = (string) $page->content;
    }

    public function save(): void
    {
        $this->page->update($this->validate());

        session()->flash('status', 'Saved.');
    }
}
```

The editor is a form field like any other, so `wire:model` and `flux:error` work as you would expect:

```blade
<form wire:submit="save" class="space-y-6">
    <flux:input wire:model="title" label="Title" />

    <flux:field>
        <flux:label>Content</flux:label>

        <x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" />

        <flux:error name="content" />
    </flux:field>

    <flux:button type="submit" variant="primary">Save</flux:button>
</form>
```

Route it as a full-page component and open it. Clicking the image button opens the file manager; pick an image and it lands in the editor, where a single click resizes it and a double click opens the edit modal. See [Image editing](image-editing.md) and [File links](file-links.md).

## 3. Showing it on the site

What you saved is HTML with `img` and `a` tags. Render it unescaped, in a container that styles headings, lists and links:

```blade
<div class="prose max-w-none dark:prose-invert">
    {!! $page->content !!}
</div>
```

Two things about that:

- **Unescaped output means you trust whoever edits the page.** This package does not sanitise; an editor who can write HTML can write a script tag. That is fine for your own team, not for content from the public. Run it through a sanitiser first in that case.
- **The package's stylesheet only styles the editor.** On the public page, write your own rules for `.tiptap-image` and `.align-left`, `.align-center` and `.align-right`, or the alignment you chose in the editor is not what a visitor sees:

```css
.tiptap-image { max-width: 100%; height: auto; }
.align-center { display: block; margin-inline: auto; }
.align-left { float: left; margin-inline-end: 1rem; }
.align-right { float: right; margin-inline-start: 1rem; }
```

## 4. When something doesn't work

```bash
php artisan flux-filemanager:check
```

That goes through the installation and names what to do about every problem. The three that catch almost everyone:

- **Nothing happens when I click the image button.** The JavaScript isn't running. Open the browser console: one failing import stops all of `app.js`, so every button dies at the same time. Right after updating this package, restart `npm run dev` first, because Vite does not watch `vendor/`.
- **The image is inserted but doesn't load.** `APP_URL` doesn't match the host in your browser. Laravel Filemanager builds absolute URLs from it. Change it and run `php artisan config:clear`.
- **The popup shows a login page, or anyone can open it.** The file manager has its own routes and its own middleware. Set `middlewares` to `['web', 'auth']` in `config/lfm.php`, and make sure you are logged in as a user that passes it.

More in [Installation](installation.md#troubleshooting).

## Next

- [Configuration](configuration.md): the toolbar presets, the component attributes, every config key
- [Drag and drop](drag-and-drop.md): base64 or uploading to the file manager
- [Localization](localization.md): English, Dutch and German, and your own texts
