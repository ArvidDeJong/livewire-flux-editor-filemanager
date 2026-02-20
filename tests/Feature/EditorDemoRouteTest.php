<?php

it('editor demo route is registered with expected uri', function () {
    $route = app('router')->getRoutes()->getByName('flux-filemanager.editor-demo');

    expect($route)->not->toBeNull();
    expect($route->uri())->toBe('darvis/editor-demo');
});

it('editor demo route can be accessed', function () {
    $this->get('/darvis/editor-demo')
        ->assertOk()
        ->assertSee('Editor Demo');
});
