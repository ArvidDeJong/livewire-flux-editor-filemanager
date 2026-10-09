<?php

it('can render the editor component', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" />');

    $view->assertSee('ui-editor', false);
});

it('can render with custom rows', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" :rows="20" />');

    $view->assertSee('ui-editor', false);
});

it('renders the image and file link buttons in the default toolbar', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" />');

    $view->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="file-link"', false);
});

it('leaves the filemanager buttons out of the minimal toolbar', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="minimal" />');

    $view->assertSee('ui-editor', false)
        ->assertDontSee('data-editor="image"', false);
});

it('can render with full toolbar', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="full" />');

    $view->assertSee('data-editor="file-link"', false);
});

it('renders the slot instead of a preset when toolbar is false', function () {
    $view = $this->blade(
        '<x-flux-filemanager-editor wire:model="content" :toolbar="false">
            <flux:editor.toolbar>
                <flux:editor.bold />
            </flux:editor.toolbar>
        </x-flux-filemanager-editor>'
    );

    $view->assertSee('ui-editor', false)
        ->assertDontSee('data-editor="file-link"', false);
});

it('renders the settings and translations for the JavaScript', function () {
    config(['flux-filemanager.url' => '/admin/filemanager']);

    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" />');

    $view->assertSee('data-flux-filemanager-config', false)
        ->assertSee('\/admin\/filemanager', false)
        ->assertSee('Insert File Link', false);
});

it('renders the drag and drop settings as data attributes', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" />');

    $view->assertSee('data-drag-drop-method="base64"', false)
        ->assertSee('data-max-file-size="5242880"', false);
});

it('renders the HTML source button in the full toolbar only', function () {
    $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="full" />')
        ->assertSee('data-editor="html-source"', false)
        ->assertSee('View HTML', false);

    $this->blade('<x-flux-filemanager-editor wire:model="content" />')
        ->assertDontSee('data-editor="html-source"', false);

    $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="minimal" />')
        ->assertDontSee('data-editor="html-source"', false);
});

it('renders the HTML source button on its own in a custom toolbar', function () {
    $this->blade(
        '<x-flux-filemanager-editor wire:model="content" :toolbar="false">
            <flux:editor.toolbar>
                @include(\'flux-filemanager::flux.editor.html-source\')
            </flux:editor.toolbar>
        </x-flux-filemanager-editor>'
    )->assertSee('data-editor="html-source"', false);
});
