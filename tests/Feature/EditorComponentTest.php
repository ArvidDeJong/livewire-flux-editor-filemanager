<?php

it('can render the editor component', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" />');

    $view->assertSee('ui-editor');
});

it('can render with custom rows', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" :rows="20" />');

    $view->assertSee('ui-editor');
});

it('can render with minimal toolbar', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="minimal" />');

    $view->assertSee('ui-editor');
});

it('can render with full toolbar', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="full" />');

    $view->assertSee('ui-editor');
});

it('can render with custom toolbar', function () {
    $view = $this->blade(
        '<x-flux-filemanager-editor wire:model="content" :toolbar="false">
            <flux:editor.toolbar>
                <flux:editor.bold />
            </flux:editor.toolbar>
        </x-flux-filemanager-editor>'
    );

    $view->assertSee('ui-editor');
});
