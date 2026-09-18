<div>
    @php
        $appUrl = config('app.url');
        $appUrlHost = $appUrl ? parse_url($appUrl, PHP_URL_HOST) : null;
        $appUrlHostText = $appUrlHost ?? __('flux-filemanager::filemanager.demo_not_set');
        $currentHost = request()->getHost();
        $appUrlMatchesHost = $appUrlHost && strcasecmp($appUrlHost, $currentHost) === 0;
    @endphp

    <flux:card class="space-y-1">
        <flux:heading>{{ __('flux-filemanager::filemanager.demo_login_required_heading') }}</flux:heading>
        <flux:text>{{ __('flux-filemanager::filemanager.demo_login_required_text') }}</flux:text>
    </flux:card>

    <flux:card class="mt-4 space-y-1">
        <div class="flex items-center gap-3">
            <flux:heading>{{ __('flux-filemanager::filemanager.demo_app_url_heading') }}</flux:heading>
            <flux:badge size="sm" :color="$appUrlMatchesHost ? 'green' : 'red'">
                {{ $appUrlMatchesHost ? __('flux-filemanager::filemanager.checklist_status_ok') : __('flux-filemanager::filemanager.checklist_status_missing') }}
            </flux:badge>
        </div>
        <flux:text>
            {{ __('flux-filemanager::filemanager.demo_app_url_status', ['appUrlHost' => $appUrlHostText, 'currentHost' => $currentHost]) }}

            @unless ($appUrlMatchesHost)
                <br>
                {{ __('flux-filemanager::filemanager.demo_app_url_fix') }}
                <br>
                {{ __('flux-filemanager::filemanager.demo_app_url_command') }}
            @endunless
        </flux:text>
    </flux:card>

    <div class="mb-8 mt-8 flex items-center justify-between">
        <flux:heading level="1" size="xl">{{ __('flux-filemanager::filemanager.demo_title') }}</flux:heading>

        <flux:button wire:click="save">
            {{ __('flux-filemanager::filemanager.demo_save') }}
        </flux:button>
    </div>

    @if (session()->has('success'))
        <flux:text class="mb-4">{{ session('success') }}</flux:text>
    @endif

    <div class="mb-8">
        <flux:field>
            <flux:label>{{ __('flux-filemanager::filemanager.demo_content_label') }}</flux:label>

            <x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" />
        </flux:field>

        <flux:text class="mt-4">
            {{ __('flux-filemanager::filemanager.demo_features_intro') }}<br>
            • {{ __('flux-filemanager::filemanager.demo_feature_upload_images') }}<br>
            • {{ __('flux-filemanager::filemanager.demo_feature_add_file_links') }}<br>
            • {{ __('flux-filemanager::filemanager.demo_feature_drag_drop') }}<br>
            • {{ __('flux-filemanager::filemanager.demo_feature_paste') }}<br>
            • {{ __('flux-filemanager::filemanager.demo_feature_single_click_resize') }}<br>
            • {{ __('flux-filemanager::filemanager.demo_feature_double_click_edit') }}<br>
        </flux:text>
    </div>

    <div>
        <flux:heading level="2" size="lg" class="mb-2">{{ __('flux-filemanager::filemanager.demo_preview') }}</flux:heading>

        <div class="prose max-w-none dark:prose-invert">
            {!! $content !!}
        </div>
    </div>
</div>
