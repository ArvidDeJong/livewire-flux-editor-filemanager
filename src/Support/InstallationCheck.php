<?php

namespace Darvis\FluxFilemanager\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use UniSharp\LaravelFilemanager\Lfm;

/**
 * The state of the installation in a host app, for `flux-filemanager:check` and the checklist page.
 *
 * One list, two readers: a second list would drift from this one. Labels and hints are English,
 * like the rest of the console output; the checklist page translates a label when a
 * `checklist_<key>` translation exists.
 */
class InstallationCheck
{
    public const OK = 'ok';

    public const WARNING = 'warning';

    public const FAILED = 'failed';

    /**
     * Every check, in the order a developer should fix them.
     *
     * @return array<int, array{key: string, status: string, label: string, hint: string}>
     */
    public function results(): array
    {
        return [
            $this->laravelFilemanager(),
            $this->lfmConfig(),
            $this->routes(),
            $this->protection(),
            $this->storageLink(),
            $this->npmPackages(),
            $this->appJs(),
            $this->build(),
            $this->appUrl(),
        ];
    }

    public function failed(): bool
    {
        return collect($this->results())->contains('status', self::FAILED);
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function result(string $key, string $status, string $label, string $hint = ''): array
    {
        return ['key' => $key, 'status' => $status, 'label' => $label, 'hint' => $hint];
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function laravelFilemanager(): array
    {
        return $this->result(
            'package_available',
            class_exists(Lfm::class) ? self::OK : self::FAILED,
            'Laravel Filemanager is installed',
            'Run composer require unisharp/laravel-filemanager. It is a dependency of this package, so this should not happen.',
        );
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function lfmConfig(): array
    {
        return $this->result(
            'lfm_config_available',
            File::exists(config_path('lfm.php')) ? self::OK : self::FAILED,
            'config/lfm.php is published',
            'Run php artisan vendor:publish --tag=lfm_config. Without it you cannot set the routes or the middleware.',
        );
    }

    /**
     * The editor opens the configured URL, so that URL has to be a route, however it was registered.
     *
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function routes(): array
    {
        $url = '/'.ltrim((string) config('flux-filemanager.url', '/filemanager'), '/');

        $registered = false;

        foreach (Route::getRoutes()->getRoutes() as $route) {
            if ('/'.ltrim($route->uri(), '/') === $url && in_array('GET', $route->methods(), true)) {
                $registered = true;

                break;
            }
        }

        return $this->result(
            'routes_enabled',
            $registered ? self::OK : self::FAILED,
            "The file manager answers at {$url}",
            'Set use_package_routes to true and url_prefix to filemanager in config/lfm.php, or register the routes yourself and point url in config/flux-filemanager.php at them.',
        );
    }

    /**
     * An unprotected file manager is a public upload endpoint, so this is a failure, not a note.
     *
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function protection(): array
    {
        /** @var array<int, string> $middlewares */
        $middlewares = (array) config('lfm.middlewares', []);

        $guarded = collect($middlewares)->contains(
            fn (string $middleware): bool => $middleware === 'auth' || Str::startsWith($middleware, 'auth:')
        );

        return $this->result(
            'protected',
            $guarded ? self::OK : self::FAILED,
            'The file manager is behind authentication',
            "Add 'auth' to middlewares in config/lfm.php. Without it anyone can browse and upload files. Using your own guard middleware instead? Then this check is a false alarm.",
        );
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function storageLink(): array
    {
        $disk = (string) config('lfm.disk', 'public');

        return $this->result(
            'storage_link',
            $disk !== 'public' || File::exists(public_path('storage')) ? self::OK : self::FAILED,
            'public/storage is linked',
            'Run php artisan storage:link. Uploads land outside the web root without it, so images 404.',
        );
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function npmPackages(): array
    {
        $required = ['@tiptap/core', '@tiptap/pm', '@tiptap/extension-image', '@tiptap/extension-link'];

        $missing = collect($required)
            ->reject(fn (string $package): bool => File::isDirectory(base_path('node_modules/'.$package)))
            ->values();

        return $this->result(
            'npm_packages',
            $missing->isEmpty() ? self::OK : self::FAILED,
            'The TipTap packages are installed',
            'Run npm install '.$missing->implode(' ').'. The editor JavaScript imports them, and one missing import stops all of app.js.',
        );
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function appJs(): array
    {
        $content = $this->appJsContent();

        if (! Str::contains($content, 'initLaravelFilemanager()')) {
            return $this->result(
                'js_init_available',
                self::FAILED,
                'resources/js/app.js calls initLaravelFilemanager()',
                'Run php artisan flux-filemanager:install, or copy examples/app.js from this package into resources/js/app.js.',
            );
        }

        if (Str::contains($content, 'import {') && Str::contains($content, 'laravel-filemanager.js')) {
            return $this->result(
                'js_init_available',
                self::WARNING,
                'resources/js/app.js still uses a named import',
                'Run php artisan flux-filemanager:install to switch to the namespace import. A named import of an export an older vendor copy lacks is a module error, and that stops every editor button at once.',
            );
        }

        return $this->result(
            'js_init_available',
            self::OK,
            'resources/js/app.js calls initLaravelFilemanager()',
        );
    }

    /**
     * A build older than the package JavaScript is the usual reason the buttons do nothing.
     *
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function build(): array
    {
        if (File::exists(public_path('hot'))) {
            return $this->result(
                'build_current',
                self::WARNING,
                'The Vite dev server is running',
                'It does not watch vendor/, so restart npm run dev after updating this package. Until you do, it serves the JavaScript it read at startup.',
            );
        }

        $manifest = public_path('build/manifest.json');

        if (! File::exists($manifest)) {
            return $this->result(
                'build_current',
                self::FAILED,
                'The assets are built',
                'Run npm run build, or npm run dev while developing.',
            );
        }

        $packageJs = __DIR__.'/../../resources/js/laravel-filemanager.js';

        if (File::exists($packageJs) && File::lastModified($manifest) < File::lastModified($packageJs)) {
            return $this->result(
                'build_current',
                self::FAILED,
                'The build is older than this package',
                'Run npm run build. The bundle still holds the previous version of the editor JavaScript.',
            );
        }

        return $this->result('build_current', self::OK, 'The assets are built');
    }

    /**
     * @return array{key: string, status: string, label: string, hint: string}
     */
    protected function appUrl(): array
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $this->result(
            'app_url',
            filled($host) ? self::OK : self::WARNING,
            'APP_URL is set',
            'Laravel Filemanager builds image URLs from APP_URL. When it differs from the host in the browser, the images point at the wrong one.',
        );
    }

    protected function appJsContent(): string
    {
        $path = resource_path('js/app.js');

        return File::exists($path) ? (string) File::get($path) : '';
    }
}
