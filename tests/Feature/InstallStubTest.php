<?php

test('the example app.js contains the setup block the installer writes', function () {
    $stub = trim((string) file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js')));

    expect(file_get_contents(packagePath('examples/app.js')))
        ->toContain($stub)
        ->toContain('initLaravelFilemanager()');
});

test('the setup block has its markers and the drop and paste plugin', function () {
    expect(file_get_contents(packagePath('resources/stubs/flux-filemanager-setup.js')))
        ->toContain('// flux-filemanager:start')
        ->toContain('// flux-filemanager:end')
        ->toContain('createImageDropPastePlugin()')
        ->toContain('registerExtension');
});
