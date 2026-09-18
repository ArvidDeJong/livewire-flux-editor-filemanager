<?php

namespace Darvis\FluxFilemanager\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Editor extends Component
{
    public function __construct(
        public ?string $id = null,
        public int $rows = 12,
        public string|bool $toolbar = 'default',
    ) {}

    /**
     * Settings and translations the JavaScript reads from the page.
     *
     * @return array<string, mixed>
     */
    public function jsConfig(): array
    {
        return [
            'filemanager_url' => config('flux-filemanager.url', '/filemanager'),
            'popup_width' => config('flux-filemanager.popup.width', 900),
            'popup_height' => config('flux-filemanager.popup.height', 600),
            'resize_presets' => config('flux-filemanager.resize_presets', ['25%', '50%', '75%', '100%']),
            'custom_width_min' => config('flux-filemanager.custom_width.min', 1),
            'custom_width_max' => config('flux-filemanager.custom_width.max', 100),
            'popup_blocked_message' => config('flux-filemanager.messages.popup_blocked'),
            'filemanager_error_message' => config('flux-filemanager.messages.filemanager_not_found'),
            'i18n' => trans('flux-filemanager::filemanager'),
        ];
    }

    public function render(): View
    {
        /** @var view-string $view Registered under the package namespace, which Larastan can't resolve. */
        $view = 'flux-filemanager::components.editor';

        return view($view);
    }
}
