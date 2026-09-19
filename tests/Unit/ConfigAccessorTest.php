<?php

declare(strict_types=1);

use Darvis\FluxFilemanager\Support\FluxFilemanagerConfig;

/**
 * FluxFilemanagerConfig is the one place that reads the package config. These tests guard the two
 * things that go wrong once a default is written down twice: an accessor that disagrees with the
 * config file, and a caller that reaches past the accessor and keeps its own stale fallback.
 */
it('returns the values the config file ships', function () {
    $config = require packagePath('config/flux-filemanager.php');

    expect(FluxFilemanagerConfig::url())->toBe($config['url'])
        ->and(FluxFilemanagerConfig::checklistUrl())->toBe($config['checklist_url'])
        ->and(FluxFilemanagerConfig::demoRoutes())->toBe($config['demo_routes'])
        ->and(FluxFilemanagerConfig::popupWidth())->toBe($config['popup']['width'])
        ->and(FluxFilemanagerConfig::popupHeight())->toBe($config['popup']['height'])
        ->and(FluxFilemanagerConfig::resizePresets())->toBe($config['resize_presets'])
        ->and(FluxFilemanagerConfig::customWidthMin())->toBe($config['custom_width']['min'])
        ->and(FluxFilemanagerConfig::customWidthMax())->toBe($config['custom_width']['max'])
        ->and(FluxFilemanagerConfig::popupBlockedMessage())->toBe($config['messages']['popup_blocked'])
        ->and(FluxFilemanagerConfig::noImagesSelectedMessage())->toBe($config['messages']['no_images_selected'])
        ->and(FluxFilemanagerConfig::filemanagerNotFoundMessage())->toBe($config['messages']['filemanager_not_found'])
        ->and(FluxFilemanagerConfig::dragDropMethod())->toBe($config['drag_drop']['method'])
        ->and(FluxFilemanagerConfig::dragDropUploadUrl())->toBe($config['drag_drop']['upload_url'])
        ->and(FluxFilemanagerConfig::dragDropMaxFileSize())->toBe($config['drag_drop']['max_file_size'])
        ->and(FluxFilemanagerConfig::dragDropAllowedTypes())->toBe($config['drag_drop']['allowed_types']);
});

it('follows a changed setting', function () {
    config([
        'flux-filemanager.url' => '/admin/files',
        'flux-filemanager.popup.width' => 1200,
        'flux-filemanager.drag_drop.method' => 'upload',
    ]);

    expect(FluxFilemanagerConfig::url())->toBe('/admin/files')
        ->and(FluxFilemanagerConfig::popupWidth())->toBe(1200)
        ->and(FluxFilemanagerConfig::dragDropMethod())->toBe('upload');
});

it('is the only place in the package that reads the config', function () {
    $offenders = [];

    foreach (['src', 'resources', 'routes'] as $directory) {
        $path = packagePath($directory);

        if (! is_dir($path)) {
            continue;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        foreach ($files as $file) {
            if (! in_array($file->getExtension(), ['php'], true)) {
                continue;
            }

            $relative = str_replace(packagePath(''), '', $file->getPathname());

            if (str_contains($relative, 'FluxFilemanagerConfig.php')) {
                continue;
            }

            if (preg_match("/config\(['\"]flux-filemanager\./", (string) file_get_contents($file->getPathname()))) {
                $offenders[] = $relative;
            }
        }
    }

    expect($offenders)->toBe([], 'these read the config directly instead of through FluxFilemanagerConfig');
});

it('keeps the config keys in alphabetical order', function () {
    preg_match_all(
        "/^    '([a-z_0-9]+)' =>/m",
        (string) file_get_contents(packagePath('config/flux-filemanager.php')),
        $matches
    );

    $keys = $matches[1];
    $sorted = $keys;
    sort($sorted);

    expect($keys)->toBe($sorted);
});
