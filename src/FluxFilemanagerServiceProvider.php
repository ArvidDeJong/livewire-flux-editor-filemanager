<?php

namespace Darvis\FluxFilemanager;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class FluxFilemanagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../config/flux-filemanager.php' => config_path('flux-filemanager.php'),
        ], 'flux-filemanager-config');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flux-filemanager');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/flux-filemanager'),
        ], 'flux-filemanager-views');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'flux-filemanager');

        // Publish translations
        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/flux-filemanager'),
        ], 'flux-filemanager-lang');

        // Register Livewire components
        Livewire::component('flux-filemanager-editor', \Darvis\FluxFilemanager\View\Components\Editor::class);

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/flux-filemanager.php', 'flux-filemanager'
        );

        // Register console commands

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
