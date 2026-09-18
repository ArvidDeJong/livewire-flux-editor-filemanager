<?php

namespace Darvis\FluxFilemanager\Tests;

use Darvis\FluxFilemanager\FluxFilemanagerServiceProvider;
use Flux\FluxServiceProvider;
use FluxPro\FluxProServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\ViewErrorBag;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    // Testbench 9.0 does not include this trait, so $this->blade() would be missing on the lowest versions.
    use InteractsWithViews;

    protected function setUp(): void
    {
        parent::setUp();

        // Flux reads $errors in its views; outside the web middleware nothing shares it.
        $this->app['view']->share('errors', new ViewErrorBag);
    }

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FluxServiceProvider::class,
            FluxProServiceProvider::class,
            FluxFilemanagerServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
    }
}
