<?php

namespace Darvis\FluxFilemanager;

use Darvis\FluxFilemanager\Console\CheckCommand;
use Darvis\FluxFilemanager\Console\InstallCommand;
use Darvis\FluxFilemanager\Support\FluxFilemanagerConfig;
use Darvis\FluxFilemanager\View\Components\Editor;
use Illuminate\Support\ServiceProvider;

class FluxFilemanagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/flux-filemanager.php', 'flux-filemanager');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckCommand::class,
                InstallCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flux-filemanager');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'flux-filemanager');

        $this->loadViewComponentsAs('flux-filemanager', [
            'editor' => Editor::class,
        ]);

        // The demo and checklist pages are unauthenticated, so they are opt-in.
        if (FluxFilemanagerConfig::demoRoutes()) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        $this->publishes([
            __DIR__.'/../config/flux-filemanager.php' => config_path('flux-filemanager.php'),
        ], 'flux-filemanager-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/flux-filemanager'),
        ], 'flux-filemanager-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/flux-filemanager'),
        ], 'flux-filemanager-lang');
    }
}
