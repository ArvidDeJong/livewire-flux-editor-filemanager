<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Testing\PendingCommand;

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

const STAR_QUESTION = 'Star darvis/livewire-flux-editor-filemanager on GitHub? A star helps other developers find the package.';

/**
 * Decline every install step, so the run touches nothing in the Testbench app.
 */
function declineEveryStep(PendingCommand $command): PendingCommand
{
    foreach ([
        'Publish the Laravel Filemanager configuration and assets?',
        'Enable the Laravel Filemanager routes at /filemanager?',
        'Create the storage link and upload directories?',
        'Install the npm packages (TipTap)?',
        'Publish config/flux-filemanager.php?',
        'Add the editor setup to resources/js/app.js?',
        'Build the assets with npm?',
    ] as $question) {
        $command = $command->expectsConfirmation($question, 'no');
    }

    return $command;
}

it('opens the repository in the browser when the user wants to star it', function () {
    Process::fake();

    declineEveryStep($this->artisan('flux-filemanager:install'))
        ->expectsConfirmation(STAR_QUESTION, 'yes')
        ->expectsOutputToContain('https://github.com/ArvidDeJong/livewire-flux-editor-filemanager')
        ->assertSuccessful();

    Process::assertRan(fn (PendingProcess $process): bool => in_array('https://github.com/ArvidDeJong/livewire-flux-editor-filemanager', (array) $process->command, true));
});

it('does not open a browser when the user declines the star', function () {
    Process::fake();

    declineEveryStep($this->artisan('flux-filemanager:install'))
        ->expectsConfirmation(STAR_QUESTION, 'no')
        ->assertSuccessful();

    Process::assertNothingRan();
});
