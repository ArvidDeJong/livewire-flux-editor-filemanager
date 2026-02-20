{{--
    Simple demo view for Flux Filemanager Editor
    
    This is a minimal example showing just the editor
    without complex layout or additional fields.
--}}

<div class="mx-auto max-w-4xl space-y-6 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <flux:heading level="1" size="xl">{{ __('flux-filemanager::filemanager.demo_title') }}</flux:heading>

        <flux:button wire:click="save">
            {{ __('flux-filemanager::filemanager.demo_save') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Editor --}}
    <div class="rounded-lg bg-white p-6 shadow">
        <flux:text class="mb-2 block">{{ __('flux-filemanager::filemanager.demo_content_label') }}</flux:text>

        <x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" />

        <flux:text class="mt-2">
            {{ __('flux-filemanager::filemanager.demo_features_intro') }}
            • {{ __('flux-filemanager::filemanager.demo_feature_upload_images') }}
            • {{ __('flux-filemanager::filemanager.demo_feature_add_file_links') }}
            • {{ __('flux-filemanager::filemanager.demo_feature_drag_drop') }}
            • {{ __('flux-filemanager::filemanager.demo_feature_paste') }}
            • {{ __('flux-filemanager::filemanager.demo_feature_single_click_resize') }}
            • {{ __('flux-filemanager::filemanager.demo_feature_double_click_edit') }}
        </flux:text>
    </div>

    {{-- Preview --}}
    <div class="rounded-lg bg-white p-6 shadow">
        <flux:heading level="2" size="lg" class="mb-4">
            {{ __('flux-filemanager::filemanager.demo_preview') }}</flux:heading>

        <div class="prose max-w-none">
            {!! $content !!}
        </div>
    </div>
</div>
