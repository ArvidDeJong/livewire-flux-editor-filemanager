<?php

namespace Darvis\FluxFilemanager;

use Illuminate\Support\ServiceProvider;

class FluxFilemanagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/flux-filemanager.php' => config_path('flux-filemanager.php'),
        ], 'flux-filemanager-config');

        // Publish assets
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor/flux-filemanager'),
            __DIR__.'/../resources/css' => resource_path('css/vendor/flux-filemanager'),
        ], 'flux-filemanager-assets');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views/components' => resource_path('views/components'),
        ], 'flux-filemanager-views');

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/flux-filemanager.php', 'flux-filemanager'
        );

        // Load views from package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flux-filemanager');

        // Register Blade components
        $this->loadViewComponentsAs('flux-filemanager', [
            'editor' => \Darvis\FluxFilemanager\View\Components\Editor::class,
        ]);
    }

    public function register(): void
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Darvis\FluxFilemanager\Console\InstallCommand::class,
            ]);
        }
    }
}
