<?php

use Darvis\FluxFilemanager\Console\InstallCommand;

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

    expect(rewriteAppJs('namespaceCalls', rewriteAppJs('namespaceImport', $example)))->toBe($example);
});
