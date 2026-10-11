<?php

namespace Darvis\FluxFilemanager;

use Darvis\FluxFilemanager\Console\CheckCommand;
use Darvis\FluxFilemanager\Console\InstallCommand;
use Darvis\FluxFilemanager\Support\FluxFilemanagerConfig;
use Darvis\FluxFilemanager\View\Components\Editor;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;

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

        // The toolbar buttons double as Flux toolbar items: <flux:editor.image /> and the
        // `toolbar="heading | image"` string syntax resolve them by file name. A copy published
        // with --tag=flux-filemanager-views is registered first, so it wins there as it does
        // in @include('flux-filemanager::flux.editor.image').
        $this->callAfterResolving('view', function (ViewFactory $view): void {
            foreach ($this->toolbarItemPaths($view) as $path) {
                Blade::anonymousComponentPath($path, 'flux');
            }
        });

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

    /**
     * The directories that hold the Flux toolbar items, in lookup order: the flux/ directory of
     * every path the flux-filemanager view namespace reads, published copies before the package's own.
     *
     * @return array<int, string>
     */
    protected function toolbarItemPaths(ViewFactory $view): array
    {
        $finder = $view->getFinder();
        $hints = $finder instanceof FileViewFinder ? $finder->getHints() : [];

        /** @var array<int, string> $viewPaths */
        $viewPaths = $hints['flux-filemanager'] ?? [__DIR__.'/../resources/views'];

        return array_values(array_filter(
            array_map(fn (string $viewPath): string => $viewPath.'/flux', $viewPaths),
            'is_dir',
        ));
    }
}
