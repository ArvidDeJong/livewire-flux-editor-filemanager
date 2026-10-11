<?php

namespace Darvis\FluxFilemanager\Tests;

use Illuminate\Foundation\Application;

/**
 * Boots the package with a published copy of the image button in place, the way
 * vendor:publish --tag=flux-filemanager-views leaves one, without writing into the Testbench app:
 * the fixture directory joins view.paths, where loadViewsFrom() looks for vendor/flux-filemanager.
 */
class PublishedViewsTestCase extends TestCase
{
    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $viewPaths = $app['config']->get('view.paths');

        $app['config']->set('view.paths', [
            ...(is_array($viewPaths) ? $viewPaths : []),
            __DIR__.'/fixtures/published-views',
        ]);
    }
}
