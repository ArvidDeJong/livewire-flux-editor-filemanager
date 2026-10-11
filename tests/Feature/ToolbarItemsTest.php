<?php

use Darvis\FluxFilemanager\View\Components\Editor;

it('renders the buttons named in the toolbar string', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="bold | image" />');

    $view->assertSee('data-editor="bold"', false)
        ->assertSee('data-editor="image"', false)
        ->assertDontSee('data-editor="file-link"', false);
});

it('expands a preset name inside the toolbar string', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="default | html-source" />');

    $view->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="file-link"', false)
        ->assertSee('data-editor="html-source"', false);
});

it('expands presets and collapses whitespace in the toolbar string', function () {
    $editor = new Editor(toolbar: "  default  |\n html-source ");

    expect($editor->toolbarItems())->toBe(Editor::PRESETS['default'].' | html-source');
});

it('returns no toolbar string when the slot renders the toolbar', function () {
    $editor = new Editor(toolbar: false);

    expect($editor->toolbarItems())->toBeNull();
});

it('tolerates whitespace runs in the toolbar string', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="bold   italic" />');

    $view->assertSee('data-editor="bold"', false)
        ->assertSee('data-editor="italic"', false);
});

it('renders the default preset for an empty toolbar string', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="" />');

    $view->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="file-link"', false)
        ->assertDontSee('data-editor="html-source"', false);
});

it('renders the default preset when toolbar is true', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" :toolbar="true" />');

    $view->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="file-link"', false)
        ->assertDontSee('data-editor="html-source"', false);
});

it('renders every button of the full preset', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="full" />');

    foreach (array_diff(explode(' ', Editor::PRESETS['full']), ['|', '~']) as $name) {
        $view->assertSee('data-editor="'.$name.'"', false);
    }
});

it('renders the table menu in the full preset', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="full" />');

    $view->assertSee('data-editor="table"', false)
        ->assertSee('data-editor="table-delete"', false);
});

it('renders the table menu with every table command', function () {
    $view = $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="table" />');

    $commands = [
        'table-menu',
        'table',
        'table-add-row-before',
        'table-add-row-after',
        'table-delete-row',
        'table-add-column-before',
        'table-add-column-after',
        'table-delete-column',
        'table-toggle-header-row',
        'table-toggle-header-column',
        'table-merge-or-split',
        'table-delete',
    ];

    preg_match_all('/data-editor="(table[^"]*)"/', (string) $view, $matches);

    expect(array_values(array_unique($matches[1])))->toEqualCanonicalizing($commands);

    foreach ($commands as $command) {
        $view->assertSee('data-editor="'.$command.'"', false);
    }
});

it('leaves the checklist button out of every preset', function () {
    foreach (array_keys(Editor::PRESETS) as $preset) {
        $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="'.$preset.'" />')
            ->assertDontSee('data-filemanager-checklist', false);
    }
});

it('renders the checklist button when the toolbar string names it', function () {
    $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="full | checklist" />')
        ->assertSee('data-filemanager-checklist', false);
});

it('renders the package buttons as items of a Flux toolbar string', function () {
    $view = $this->blade('<flux:editor.toolbar items="bold | image file-link html-source" />');

    $view->assertSee('data-editor="bold"', false)
        ->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="file-link"', false)
        ->assertSee('data-editor="html-source"', false);
});

it('renders the package buttons as Flux components in a Blade toolbar', function () {
    $view = $this->blade('<flux:editor.toolbar><flux:editor.image /><flux:editor.html-source /></flux:editor.toolbar>');

    $view->assertSee('data-editor="image"', false)
        ->assertSee('data-editor="html-source"', false);
});

it('has the presets minimal, default and full', function () {
    expect(array_keys(Editor::PRESETS))->toBe(['minimal', 'default', 'full']);
});

it('renders the tooltip of each new button', function (string $name, string $tooltip) {
    $this->blade('<flux:editor.'.$name.' />')->assertSee($tooltip);
})->with([
    ['hr', 'Horizontal rule'],
    ['code-block', 'Code block'],
    ['clear-format', 'Clear formatting'],
    ['table', 'Table'],
]);
