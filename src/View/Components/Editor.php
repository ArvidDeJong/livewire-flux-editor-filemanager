<?php

namespace Darvis\FluxFilemanager\View\Components;

use Illuminate\View\Component;

class Editor extends Component
{
    public function __construct(
        public ?string $id = null,
        public int $rows = 12,
        public string $toolbar = 'default',
    ) {}

    public function render()
    {
        return view('flux-filemanager::components.editor');
    }
}
