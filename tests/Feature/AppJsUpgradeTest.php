<?php

use Darvis\FluxFilemanager\Console\InstallCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\PendingCommand;

/**
 * Run one of the installer's app.js rewrite helpers.
 */
function rewriteAppJs(string $method, string $content): string
{
    $command = new InstallCommand;

    return (string) Closure::bind(
        fn (): string => $command->{$method}($content),
        null,
        InstallCommand::class
    )();
}

/**
 * The setup block of 1.9: the current stub without the lines that enable the table extensions.
 */
function setupBlockOf19(): string
{
    $stub = (string) file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js'));

    return (string) preg_replace('/^[ \t]*\/\/ Tables are bundled.*?\R\R/ms', '', $stub, 1);
}

/**
 * Run the installer with only the app.js step accepted, so the run touches nothing else in the Testbench app.
 */
function acceptOnlyTheAppJsStep(PendingCommand $command): PendingCommand
{
    foreach ([
        'Publish the Laravel Filemanager configuration and assets?' => 'no',
        'Enable the Laravel Filemanager routes at /filemanager?' => 'no',
        'Create the storage link and upload directories?' => 'no',
        'Install the npm packages (TipTap)?' => 'no',
        'Publish config/flux-filemanager.php?' => 'no',
        'Add the editor setup to resources/js/app.js?' => 'yes',
        'Build the assets with npm?' => 'no',
        'Buy me a beer? 🍺 Sponsoring on GitHub keeps darvis/livewire-flux-editor-filemanager maintained.' => 'no',
    ] as $question => $answer) {
        $command = $command->expectsConfirmation($question, $answer);
    }

    return $command;
}

afterEach(fn () => File::delete(resource_path('js/app.js')));

$module = '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js';

it('rewrites the named import 1.1.x wrote', function () use ($module) {
    $result = rewriteAppJs('namespaceImport', "import { initLaravelFilemanager } from '{$module}'\n");

    expect($result)->toBe("import * as fluxFilemanager from '{$module}'\n");
});

it('rewrites a named import whatever its names, order and quotes are', function () use ($module) {
    $result = rewriteAppJs('namespaceImport', "import {createImageDropPastePlugin,  initLaravelFilemanager} from \"{$module}\"\n");

    expect($result)->toBe("import * as fluxFilemanager from '{$module}'\n");
});

it('leaves the namespace import and other modules alone', function () use ($module) {
    $content = "import Image from '@tiptap/extension-image'\nimport * as fluxFilemanager from '{$module}'\n";

    expect(rewriteAppJs('namespaceImport', $content))->toBe($content);
});

it('points bare calls at the namespace and makes the plugin call optional', function () {
    $content = "addProseMirrorPlugins() { return [createImageDropPastePlugin()] }\ninitLaravelFilemanager()\n";

    expect(rewriteAppJs('namespaceCalls', $content))
        ->toBe("addProseMirrorPlugins() { return [fluxFilemanager.createImageDropPastePlugin?.()] }\nfluxFilemanager.initLaravelFilemanager()\n");
});

it('does not prefix calls twice or touch the deprecated alias', function () {
    $content = "fluxFilemanager.createImageDropPastePlugin?.()\nfluxFilemanager.initLaravelFilemanager()\ninitLaravelFilemanagerForAllEditors()\n";

    expect(rewriteAppJs('namespaceCalls', $content))->toBe($content);
});

it('leaves the example app.js unchanged, so installing twice is safe', function () {
    $example = (string) file_get_contents(packagePath('examples/app.js'));

    expect(rewriteAppJs('enableTableExtensions', rewriteAppJs('namespaceCalls', rewriteAppJs('namespaceImport', $example))))->toBe($example);
});

it('adds the table lines under the guard line of a 1.9 block, with the indentation of the block', function () {
    $before = "// flux-filemanager:start\ndocument.addEventListener('flux:editor', (e) => {\n  e.detail.__fluxFilemanagerExtensionsRegistered = true\n\n  e.detail.registerExtension(Link)\n})\n// flux-filemanager:end\n";

    expect(rewriteAppJs('enableTableExtensions', $before))->toBe(
        "// flux-filemanager:start\ndocument.addEventListener('flux:editor', (e) => {\n  e.detail.__fluxFilemanagerExtensionsRegistered = true\n\n"
        ."  // Tables are bundled with Flux Pro 2.15.1+ but off by default. Enable them here and never install the\n"
        ."  // table extensions through npm: a second copy of ProseMirror breaks the editor.\n"
        ."  const tableExtensions = ['table', 'tableRow', 'tableHeader', 'tableCell']\n"
        ."  tableExtensions.forEach((name) => e.detail.enableExtension?.(name))\n\n"
        ."  e.detail.registerExtension(Link)\n})\n// flux-filemanager:end\n"
    );
});

it('writes the lines exactly as the stub has them, so an upgraded 1.9 block equals a fresh one', function () {
    $stub = (string) file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js'));
    $before = setupBlockOf19();

    expect($before)->not->toContain('enableExtension')
        ->and(rewriteAppJs('enableTableExtensions', $before))->toBe($stub);
});

it('leaves a block that already enables the table extensions unchanged', function () {
    $stub = (string) file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js'));

    expect(rewriteAppJs('enableTableExtensions', $stub))->toBe($stub);
});

it('leaves content without the setup block alone', function () {
    $content = "document.addEventListener('flux:editor', (e) => {\n    e.detail.__fluxFilemanagerExtensionsRegistered = true\n})\n";

    expect(rewriteAppJs('enableTableExtensions', $content))->toBe($content);
});

it('leaves a block without the guard line alone, because there is no place for the lines', function () {
    $content = "// flux-filemanager:start\ndocument.addEventListener('flux:editor', (e) => {\n    e.detail.registerExtension(Link)\n})\n// flux-filemanager:end\n";

    expect(rewriteAppJs('enableTableExtensions', $content))->toBe($content);
});

it('adds the table lines to app.js when the installer runs again on an installation from before 1.10', function () {
    $appJs = resource_path('js/app.js');
    File::ensureDirectoryExists(dirname($appJs));
    File::put($appJs, setupBlockOf19());

    expect(File::get($appJs))->not->toContain('enableExtension');

    acceptOnlyTheAppJsStep($this->artisan('flux-filemanager:install'))->assertSuccessful();

    expect(File::get($appJs))->toContain('tableExtensions.forEach((name) => e.detail.enableExtension?.(name))');
});

it('warns about a setup block it finds no place for the table lines in', function () {
    $appJs = resource_path('js/app.js');
    File::ensureDirectoryExists(dirname($appJs));
    File::put($appJs, "// flux-filemanager:start\ndocument.addEventListener('flux:editor', (e) => {\n    e.detail.registerExtension(Link)\n})\n// flux-filemanager:end\n");

    acceptOnlyTheAppJsStep($this->artisan('flux-filemanager:install'))
        ->expectsOutputToContain('app.js has a setup block without the table extensions. Compare it with examples/app.js in the package.')
        ->assertSuccessful();

    expect(File::get($appJs))->not->toContain('enableExtension');
});
