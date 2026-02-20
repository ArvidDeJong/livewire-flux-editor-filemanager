<?php

use Illuminate\Support\Facades\Artisan;

it('install command exists', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('flux-filemanager:install');
});

it('install command has correct signature', function () {
    $command = Artisan::all()['flux-filemanager:install'];

    expect($command->getName())->toBe('flux-filemanager:install');
    expect($command->getDescription())->toContain('Install Flux Filemanager');
});

it('install command has force option', function () {
    $command = Artisan::all()['flux-filemanager:install'];
    $definition = $command->getDefinition();

    expect($definition->hasOption('force'))->toBeTrue();
});

it('install command can run help without interaction', function () {
    $this->artisan('flux-filemanager:install --help')
        ->assertSuccessful();
});
