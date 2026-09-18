<?php

/**
 * @return array<int, string>
 */
function translationKeys(string $locale): array
{
    return array_keys(require packagePath("resources/lang/{$locale}/filemanager.php"));
}

test('every translation key exists in every language', function () {
    $locales = ['en', 'nl', 'de'];
    $all = [];

    foreach ($locales as $locale) {
        $all = array_unique([...$all, ...translationKeys($locale)]);
    }

    foreach ($locales as $locale) {
        expect(array_values(array_diff($all, translationKeys($locale))))->toBe([], "keys missing in {$locale}");
    }

    expect(count($all))->toBeGreaterThan(50);
});

test('the JavaScript only uses translation keys that exist', function () {
    preg_match_all("/\\bt\\('([a-z_]+)'/", (string) file_get_contents(packagePath('resources/js/laravel-filemanager.js')), $matches);

    $used = array_unique($matches[1]);

    expect($used)->not->toBeEmpty();
    expect(array_values(array_diff($used, translationKeys('en'))))->toBe([]);
});
