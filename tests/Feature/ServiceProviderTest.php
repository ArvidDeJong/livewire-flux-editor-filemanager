<?php

use Darvis\FluxFilemanager\FluxFilemanagerServiceProvider;

it('registers the service provider', function () {
    expect($this->app->getLoadedProviders())->toHaveKey(FluxFilemanagerServiceProvider::class);
});

it('publishes the config', function () {
    $this->artisan('vendor:publish', [
        '--tag' => 'flux-filemanager-config',
        '--force' => true,
    ])->assertSuccessful();
});

it('loads the views', function () {
    expect(view()->exists('flux-filemanager::components.editor'))->toBeTrue();
});

it('registers the blade component', function () {
    $aliases = $this->app->make('blade.compiler')->getClassComponentAliases();

    expect($aliases)->toHaveKey('flux-filemanager-editor');
});

it('does not register the demo routes by default', function () {
    $routes = app('router')->getRoutes();

    expect($routes->getByName('flux-filemanager.editor-demo'))->toBeNull();
    expect($routes->getByName('flux-filemanager.filemanager-checklist'))->toBeNull();
});
