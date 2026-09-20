<?php

use Darvis\FluxFilemanager\Support\InstallationCheck;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/**
 * @return array<string, array{key: string, status: string, label: string, hint: string}>
 */
function checkResults(): array
{
    return collect(app(InstallationCheck::class)->results())->keyBy('key')->all();
}

it('registers the check command', function () {
    expect(Artisan::all())->toHaveKey('flux-filemanager:check');
});

it('fails and names what to do when nothing is set up', function () {
    $this->artisan('flux-filemanager:check')
        ->expectsOutputToContain('php artisan vendor:publish --tag=lfm_config')
        ->assertFailed();
});

it('reports an unprotected file manager as a failure', function () {
    config()->set('lfm.middlewares', ['web']);

    expect(checkResults()['protected']['status'])->toBe(InstallationCheck::FAILED);

    config()->set('lfm.middlewares', ['web', 'auth']);

    expect(checkResults()['protected']['status'])->toBe(InstallationCheck::OK);
});

it('accepts a guard on the auth middleware', function () {
    config()->set('lfm.middlewares', ['web', 'auth:admin']);

    expect(checkResults()['protected']['status'])->toBe(InstallationCheck::OK);
});

it('looks for the configured url among the registered routes', function () {
    config()->set('flux-filemanager.url', '/media-library');

    expect(checkResults()['routes_enabled']['status'])->toBe(InstallationCheck::FAILED);

    Route::get('media-library', fn () => '')->name('test.media-library');

    expect(checkResults()['routes_enabled']['status'])->toBe(InstallationCheck::OK);
});

it('warns about a named import in app.js, because one missing export kills every button', function () {
    $appJs = resource_path('js/app.js');
    File::ensureDirectoryExists(dirname($appJs));
    File::put($appJs, "import { initLaravelFilemanager } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'\ninitLaravelFilemanager()\n");

    $result = checkResults()['js_init_available'];

    expect($result['status'])->toBe(InstallationCheck::WARNING)
        ->and($result['hint'])->toContain('flux-filemanager:install');

    File::put($appJs, "import * as fluxFilemanager from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'\nfluxFilemanager.initLaravelFilemanager()\n");

    expect(checkResults()['js_init_available']['status'])->toBe(InstallationCheck::OK);

    File::delete($appJs);
});

it('warns that a running dev server does not watch vendor', function () {
    File::put(public_path('hot'), 'http://localhost:5173');

    $result = checkResults()['build_current'];

    expect($result['status'])->toBe(InstallationCheck::WARNING)
        ->and($result['hint'])->toContain('restart npm run dev');

    File::delete(public_path('hot'));
});

it('fails when the build is older than the package javascript', function () {
    File::delete(public_path('hot'));
    $manifest = public_path('build/manifest.json');
    File::ensureDirectoryExists(dirname($manifest));
    File::put($manifest, '{}');

    // Both sides are set relative to the package file, never to "now". A fixed offset like
    // time() - 86400 only fails while the package JavaScript was touched today, so this test
    // passed in CI, where a checkout stamps every file, and started failing on a working copy
    // that was a day old.
    $packageJs = filemtime(packagePath('resources/js/laravel-filemanager.js'));

    touch($manifest, $packageJs - 60);

    expect(checkResults()['build_current']['status'])->toBe(InstallationCheck::FAILED);

    touch($manifest, $packageJs + 60);

    expect(checkResults()['build_current']['status'])->toBe(InstallationCheck::OK);

    File::deleteDirectory(dirname($manifest));
});
