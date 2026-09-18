<?php

use Darvis\FluxFilemanager\Tests\DemoTestCase;
use Darvis\FluxFilemanager\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');
uses(DemoTestCase::class)->in('Demo');

function packagePath(string $path = ''): string
{
    return dirname(__DIR__).($path === '' ? '' : '/'.$path);
}
