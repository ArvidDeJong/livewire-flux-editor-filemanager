<?php

namespace Darvis\FluxFilemanager\Livewire;

use Livewire\Component;

class EditorDemo extends Component
{
    public string $content = '';

    public function mount(): void
    {
        $this->content = '<h2>' . \e(\__('flux-filemanager::filemanager.demo_welcome_heading')) . '</h2><p>' . \e(\__('flux-filemanager::filemanager.demo_welcome_text')) . '</p>';
    }

    public function save(): void
    {
        \session()->flash('success', \__('flux-filemanager::filemanager.demo_saved'));
    }

    public function render()
    {
        return \view('flux-filemanager::examples.editor-demo')
            ->layout('flux-filemanager::examples.layout', [
                'title' => \__('flux-filemanager::filemanager.demo_page_title'),
            ]);
    }
}