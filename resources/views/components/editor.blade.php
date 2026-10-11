@props([
    'id' => null,
    'rows' => 12,
    'toolbar' => 'default', // a preset name, a Flux toolbar string, or false for the slot
    'dragDropMethod' => \Darvis\FluxFilemanager\Support\FluxFilemanagerConfig::dragDropMethod(),
    'uploadUrl' => \Darvis\FluxFilemanager\Support\FluxFilemanagerConfig::dragDropUploadUrl(),
    'maxFileSize' => \Darvis\FluxFilemanager\Support\FluxFilemanagerConfig::dragDropMaxFileSize(),
    'allowedTypes' => implode(',', \Darvis\FluxFilemanager\Support\FluxFilemanagerConfig::dragDropAllowedTypes()),
])

{{-- Settings and translations for laravel-filemanager.js; a JSON script tag needs no CSP nonce. --}}
@once
    <script type="application/json" data-flux-filemanager-config>@json($jsConfig())</script>
@endonce

<flux:editor :id="$id" {{ $attributes }} :rows="$rows"
    data-drag-drop-method="{{ $dragDropMethod }}" data-upload-url="{{ $uploadUrl }}"
    data-max-file-size="{{ $maxFileSize }}" data-allowed-types="{{ $allowedTypes }}">
    @if ($toolbar !== false)
        <flux:editor.toolbar :items="$toolbarItems()" />
    @else
        {{-- Custom toolbar via slot --}}
        {{ $slot }}
    @endif

    <flux:editor.content />
</flux:editor>
