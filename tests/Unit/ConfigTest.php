<?php

it('has default filemanager url', function () {
    expect(config('flux-filemanager.url'))->toBe('/filemanager');
});

it('has popup configuration', function () {
    $popup = config('flux-filemanager.popup');

    expect($popup)->toBeArray();
    expect($popup)->toHaveKeys(['width', 'height']);
    expect($popup['width'])->toBe(900);
    expect($popup['height'])->toBe(600);
});

it('has resize presets', function () {
    $presets = config('flux-filemanager.resize_presets');

    expect($presets)->toBeArray();
    expect($presets)->toContain('25%');
    expect($presets)->toContain('50%');
    expect($presets)->toContain('75%');
    expect($presets)->toContain('100%');
});

it('has custom width range', function () {
    $customWidth = config('flux-filemanager.custom_width');

    expect($customWidth)->toBeArray();
    expect($customWidth['min'])->toBe(1);
    expect($customWidth['max'])->toBe(100);
});

it('has error messages', function () {
    $messages = config('flux-filemanager.messages');

    expect($messages)->toBeArray();
    expect($messages)->toHaveKeys(['popup_blocked', 'no_images_selected', 'filemanager_not_found']);
});

it('filemanager url can be overridden via env', function () {
    config(['flux-filemanager.url' => '/admin/filemanager']);

    expect(config('flux-filemanager.url'))->toBe('/admin/filemanager');
});
