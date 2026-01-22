<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Simple demo component for Flux Filemanager Editor
 * 
 * This is a minimal example showing just the editor without
 * any database persistence or complex logic.
 */
class EditorDemo extends Component
{
    public string $content = '';

    public function mount()
    {
        // Optional: Set some default content
        $this->content = '<h2>Welcome to the Editor Demo</h2><p>Start typing or use the toolbar to add images and links!</p>';
    }

    public function save()
    {
        // In a real application, you would save to database here
        session()->flash('success', 'Content saved!');
    }

    public function render()
    {
        return view('livewire.editor-demo');
    }
}
