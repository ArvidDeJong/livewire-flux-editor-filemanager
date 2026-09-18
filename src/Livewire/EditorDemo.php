<?php

namespace Darvis\FluxFilemanager\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditorDemo extends Component
{
    public string $content = '';

    public function mount(): void
    {
        $this->content = '<h2>'.\e(\__('flux-filemanager::filemanager.demo_welcome_heading')).'</h2><p>'.\e(\__('flux-filemanager::filemanager.demo_welcome_text')).'</p>';
    }

    public function save(): void
    {
        \session()->flash('success', \__('flux-filemanager::filemanager.demo_saved'));
    }

    public function render(): View
    {
        /** @var view-string $view Registered under the package namespace, which Larastan can't resolve. */
        $view = 'flux-filemanager::examples.editor-demo';

        return \view($view)
            ->layout('flux-filemanager::examples.layout', [
                'title' => \__('flux-filemanager::filemanager.demo_page_title'),
            ]);
    }
}
