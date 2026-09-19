<?php

namespace Darvis\FluxFilemanager\View\Components;

use Darvis\FluxFilemanager\Support\FluxFilemanagerConfig;
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
            'filemanager_url' => FluxFilemanagerConfig::url(),
            'popup_width' => FluxFilemanagerConfig::popupWidth(),
            'popup_height' => FluxFilemanagerConfig::popupHeight(),
            'resize_presets' => FluxFilemanagerConfig::resizePresets(),
            'custom_width_min' => FluxFilemanagerConfig::customWidthMin(),
            'custom_width_max' => FluxFilemanagerConfig::customWidthMax(),
            'popup_blocked_message' => FluxFilemanagerConfig::popupBlockedMessage(),
            'filemanager_error_message' => FluxFilemanagerConfig::filemanagerNotFoundMessage(),
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
