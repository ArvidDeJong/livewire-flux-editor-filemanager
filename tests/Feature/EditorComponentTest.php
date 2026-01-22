<?php

namespace Darvis\FluxFilemanager\Tests\Feature;

use Darvis\FluxFilemanager\Tests\TestCase;

class EditorComponentTest extends TestCase
{
    /** @test */
    public function it_can_render_the_editor_component()
    {
        $view = $this->blade(
            '<x-flux-filemanager-editor wire:model="content" />'
        );

        $view->assertSee('ui-editor');
    }

    /** @test */
    public function it_can_render_with_custom_rows()
    {
        $view = $this->blade(
            '<x-flux-filemanager-editor wire:model="content" :rows="20" />'
        );

        $view->assertSee('ui-editor');
    }

    /** @test */
    public function it_can_render_with_minimal_toolbar()
    {
        $view = $this->blade(
            '<x-flux-filemanager-editor wire:model="content" toolbar="minimal" />'
        );

        $view->assertSee('ui-editor');
    }

    /** @test */
    public function it_can_render_with_full_toolbar()
    {
        $view = $this->blade(
            '<x-flux-filemanager-editor wire:model="content" toolbar="full" />'
        );

        $view->assertSee('ui-editor');
    }

    /** @test */
    public function it_can_render_with_custom_toolbar()
    {
        $view = $this->blade(
            '<x-flux-filemanager-editor wire:model="content" :toolbar="false">
                <flux:editor.toolbar>
                    <flux:editor.bold />
                </flux:editor.toolbar>
            </x-flux-filemanager-editor>'
        );

        $view->assertSee('ui-editor');
    }
}
