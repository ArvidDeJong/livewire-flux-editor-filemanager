# Examples

Files to copy into a Laravel application that uses this package.

- **`app.js`**: the complete `resources/js/app.js` setup. The imports, the Image and Link extensions registered on the `flux:editor` event (including the drop and paste plugin), and `initLaravelFilemanager()`. The block between `// flux-filemanager:start` and `// flux-filemanager:end` is what `php artisan flux-filemanager:install` writes; the package's tests keep this file and the installer stub identical.
- **`EditorDemo.php`** and **`editor-demo.blade.php`**: a minimal Livewire component with the editor and a preview, without a database. The same demo ships inside the package at `/darvis/editor-demo` when `FLUX_FILEMANAGER_DEMO_ROUTES=true`.

## Use the demo in your own app

```bash
php artisan make:livewire EditorDemo
```

Copy `EditorDemo.php` to `app/Livewire/EditorDemo.php` (change the namespace) and `editor-demo.blade.php` to `resources/views/livewire/editor-demo.blade.php`, then add a route:

```php
use App\Livewire\EditorDemo;

Route::get('/editor-demo', EditorDemo::class)->middleware('auth');
```

## Output

The editor stores HTML:

```html
<h2>Welcome</h2>
<p>A paragraph with <strong>bold</strong> text.</p>
<img src="/storage/photos/1/office.jpg" class="tiptap-image align-center" width="75%" style="width: 75%; margin-left: auto; margin-right: auto; display: block;" alt="Our office" data-align="center">
<p>Download the <a href="/storage/files/1/brochure.pdf" target="_blank" rel="noopener noreferrer nofollow">brochure (PDF)</a>.</p>
```

Render it with unescaped output in a `prose` container, for trusted editors only. See the [documentation](https://arviddejong.github.io/livewire-flux-editor-filemanager/) for everything else.
