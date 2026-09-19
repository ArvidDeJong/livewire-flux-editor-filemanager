<?php

declare(strict_types=1);

namespace Darvis\FluxFilemanager\Support;

/**
 * The one place that reads the package config. Callers ask this class, so a default is written
 * once and a caller cannot quietly disagree with the config file about what it is.
 *
 * Settings of the file manager itself (the lfm.* keys) belong to laravel-filemanager and are
 * read where they are needed, not here.
 */
final class FluxFilemanagerConfig
{
    /**
     * URL the editor opens the file manager at.
     */
    public static function url(): string
    {
        return (string) config('flux-filemanager.url', '/filemanager');
    }

    /**
     * Link to the installation checklist, shown in the editor toolbar.
     */
    public static function checklistUrl(): string
    {
        return (string) config('flux-filemanager.checklist_url', '/darvis/filemanager-checklist');
    }

    /**
     * Whether the unauthenticated demo and checklist pages are registered.
     */
    public static function demoRoutes(): bool
    {
        return (bool) config('flux-filemanager.demo_routes', false);
    }

    public static function popupWidth(): int
    {
        return (int) config('flux-filemanager.popup.width', 900);
    }

    public static function popupHeight(): int
    {
        return (int) config('flux-filemanager.popup.height', 600);
    }

    /**
     * Width percentages offered in the resize menu.
     *
     * @return array<int, string>
     */
    public static function resizePresets(): array
    {
        /** @var array<int, string> $presets */
        $presets = (array) config('flux-filemanager.resize_presets', ['25%', '50%', '75%', '100%']);

        return $presets;
    }

    public static function customWidthMin(): int
    {
        return (int) config('flux-filemanager.custom_width.min', 1);
    }

    public static function customWidthMax(): int
    {
        return (int) config('flux-filemanager.custom_width.max', 100);
    }

    public static function popupBlockedMessage(): string
    {
        return (string) config(
            'flux-filemanager.messages.popup_blocked',
            'Popup was blocked by your browser. Please allow popups for this site.'
        );
    }

    public static function noImagesSelectedMessage(): string
    {
        return (string) config('flux-filemanager.messages.no_images_selected', 'No images were selected.');
    }

    public static function filemanagerNotFoundMessage(): string
    {
        return (string) config(
            'flux-filemanager.messages.filemanager_not_found',
            'Laravel Filemanager could not be loaded. Please check your installation.'
        );
    }

    /**
     * How a dragged or pasted image is handled: 'base64' embeds it, 'upload' posts it.
     */
    public static function dragDropMethod(): string
    {
        return (string) config('flux-filemanager.drag_drop.method', 'base64');
    }

    public static function dragDropUploadUrl(): string
    {
        return (string) config('flux-filemanager.drag_drop.upload_url', '/filemanager/upload');
    }

    public static function dragDropMaxFileSize(): int
    {
        return (int) config('flux-filemanager.drag_drop.max_file_size', 5242880);
    }

    /**
     * @return array<int, string>
     */
    public static function dragDropAllowedTypes(): array
    {
        /** @var array<int, string> $types */
        $types = (array) config('flux-filemanager.drag_drop.allowed_types', [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        ]);

        return $types;
    }
}
