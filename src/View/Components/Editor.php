<?php

namespace Darvis\FluxFilemanager\View\Components;

use Darvis\FluxFilemanager\Support\FluxFilemanagerConfig;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Editor extends Component
{
    /**
     * The toolbar presets, as Flux toolbar strings. A preset name inside a toolbar string expands to its items.
     *
     * @var array<string, string>
     */
    public const PRESETS = [
        'minimal' => 'bold italic | link',
        'default' => 'heading | bold italic strike | bullet ordered blockquote | image link file-link | align',
        'full' => 'heading | bold italic strike underline highlight subscript superscript | bullet ordered blockquote | image link file-link | align | code code-block hr clear-format | html-source ~ undo redo',
    ];

    public function __construct(
        public ?string $id = null,
        public int $rows = 12,
        public string|bool $toolbar = 'default',
    ) {}

    /**
     * The toolbar as a Flux toolbar string, or null when the slot renders the toolbar.
     *
     * A preset name inside the string expands to its items. Whitespace runs collapse to single
     * spaces, because Flux splits the string on single spaces and an empty item throws.
     */
    public function toolbarItems(): ?string
    {
        if ($this->toolbar === false) {
            return null;
        }

        $items = is_string($this->toolbar) ? Str::squish($this->toolbar) : '';

        if ($items === '') {
            return self::PRESETS['default'];
        }

        $expanded = array_map(
            fn (string $item): string => self::PRESETS[$item] ?? $item,
            explode(' ', $items),
        );

        return implode(' ', $expanded);
    }

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
