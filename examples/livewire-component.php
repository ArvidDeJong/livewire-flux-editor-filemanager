<?php

namespace App\Livewire\Examples;

use Livewire\Component;

/**
 * Example Livewire component using Flux Filemanager Editor
 */
class BlogPostEditor extends Component
{
    public string $title = '';
    public string $excerpt = '';
    public string $content = '';

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
        ]);

        // Save your blog post
        // BlogPost::create([...]);

        session()->flash('message', 'Blog post saved successfully!');
    }

    public function render()
    {
        return view('livewire.examples.blog-post-editor');
    }
}
