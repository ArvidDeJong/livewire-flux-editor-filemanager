<?php

it('uses a published copy of a button in the presets', function () {
    $this->blade('<x-flux-filemanager-editor wire:model="content" />')
        ->assertSee('data-published-copy', false);
});

it('uses a published copy of a button in the toolbar string', function () {
    $this->blade('<x-flux-filemanager-editor wire:model="content" toolbar="bold | image" />')
        ->assertSee('data-published-copy', false);
});

it('uses a published copy of a button as a Flux component', function () {
    $this->blade('<flux:editor.image />')
        ->assertSee('data-published-copy', false);
});

it('uses a published copy of a button in an include', function () {
    $this->blade("@include('flux-filemanager::flux.editor.image')")
        ->assertSee('data-published-copy', false);
});

it('keeps the package view for a button that is not published', function () {
    $this->blade('<flux:editor.file-link />')
        ->assertSee('data-editor="file-link"', false)
        ->assertDontSee('data-published-copy', false);
});
