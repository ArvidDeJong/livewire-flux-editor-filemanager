<?php

namespace Darvis\FluxFilemanager\Tests;

use Illuminate\Foundation\Application;

/**
 * Boots the package with the opt-in demo routes enabled.
 */
class DemoTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The demo layout loads app.js through Vite; there is no build in the test app.
        $this->withoutVite();
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('flux-filemanager.demo_routes', true);
    }
}
