<?php

test('the example app.js contains the setup block the installer writes', function () {
    $stub = trim((string) file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js')));

    expect(file_get_contents(packagePath('examples/app.js')))
        ->toContain($stub)
        ->toContain('fluxFilemanager.initLaravelFilemanager()')
        ->toContain('import * as fluxFilemanager from');
});

test('the setup block has its markers and the drop and paste plugin', function () {
    expect(file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js')))
        ->toContain('// flux-filemanager:start')
        ->toContain('// flux-filemanager:end')
        ->toContain('fluxFilemanager.createImageDropPastePlugin?.()')
        ->toContain('registerExtension');
});

test('the setup block reads the package through the namespace, so a stale vendor copy is survivable', function () {
    $stub = (string) file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js'));

    // A named import of a missing export is a fatal module error that kills every editor button.
    expect($stub)
        ->not->toContain('import {')
        ->toContain('console.warn');
});

test('the setup block enables the table extensions Flux Pro bundles instead of installing them', function () {
    // A second copy of ProseMirror from an npm table extension breaks the editor, so the block
    // turns on the bundled ones and the installer adds no table package.
    expect(file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js')))
        ->toContain('enableExtension')
        ->toContain("['table', 'tableRow', 'tableHeader', 'tableCell']")
        ->not->toContain('@tiptap/extension-table');
});
