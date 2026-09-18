<?php

it('registers the editor demo route', function () {
    $route = app('router')->getRoutes()->getByName('flux-filemanager.editor-demo');

    expect($route)->not->toBeNull();
    expect($route->uri())->toBe('darvis/editor-demo');
});

it('renders the editor demo page', function () {
    $this->get('/darvis/editor-demo')
        ->assertOk()
        ->assertSee('ui-editor', false);
});

it('renders the installation checklist page', function () {
    $this->get('/darvis/filemanager-checklist')
        ->assertOk();
});
