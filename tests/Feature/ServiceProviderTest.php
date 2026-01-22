<?php

namespace Darvis\FluxFilemanager\Tests\Feature;

use Darvis\FluxFilemanager\FluxFilemanagerServiceProvider;
use Darvis\FluxFilemanager\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function service_provider_is_registered()
    {
        $providers = $this->app->getLoadedProviders();
        
        $this->assertArrayHasKey(FluxFilemanagerServiceProvider::class, $providers);
    }

    /** @test */
    public function config_is_published()
    {
        $this->artisan('vendor:publish', [
            '--tag' => 'flux-filemanager-config',
            '--force' => true,
        ])->assertSuccessful();
    }

    /** @test */
    public function views_are_loaded()
    {
        $this->assertTrue(
            view()->exists('flux-filemanager::components.editor')
        );
    }

    /** @test */
    public function blade_component_is_registered()
    {
        $component = $this->app->make('blade.compiler')
            ->getClassComponentAliases();
        
        $this->assertArrayHasKey('flux-filemanager-editor', $component);
    }
}
