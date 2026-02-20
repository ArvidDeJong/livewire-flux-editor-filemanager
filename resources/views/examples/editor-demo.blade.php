<div>
    @php
        $appUrl = config('app.url');
        $appUrlHost = $appUrl ? parse_url($appUrl, PHP_URL_HOST) : null;
        $appUrlHostText = $appUrlHost ?? __('flux-filemanager::filemanager.demo_not_set');
        $currentHost = request()->getHost();
        $appUrlMatchesHost = $appUrlHost && strcasecmp($appUrlHost, $currentHost) === 0;
    @endphp

    <flux:callout variant="warning" icon="exclamation-circle">
        <flux:callout.heading>{{ __('flux-filemanager::filemanager.demo_login_required_heading') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('flux-filemanager::filemanager.demo_login_required_text') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout class="mt-4" :variant="$appUrlMatchesHost ? 'success' : 'danger'"
        :icon="$appUrlMatchesHost ? 'check-circle' : 'x-circle'">
        <flux:callout.heading>
            {{ __('flux-filemanager::filemanager.demo_app_url_heading') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('flux-filemanager::filemanager.demo_app_url_status', ['appUrlHost' => $appUrlHostText, 'currentHost' => $currentHost]) }}

            @unless ($appUrlMatchesHost)
                <br>
                {{ __('flux-filemanager::filemanager.demo_app_url_fix') }}
                <br>
                {{ __('flux-filemanager::filemanager.demo_app_url_command') }}
            @endunless
        </flux:callout.text>
    </flux:callout>

    <div class="mb-8 mt-8">
        <flux:heading level="1" size="xl">{{ __('flux-filemanager::filemanager.demo_title') }}</flux:heading>

        <flux:button wire:click="save">
            {{ __('flux-filemanager::filemanager.demo_save') }}
        </flux:button>
    </div>

    @if (session()->has('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 mt-8">
        <label>
            {{ __('flux-filemanager::filemanager.demo_content_label') }}
        </label>

        <x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" />

        <flux:text>
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
        <flux:heading level="2" size="lg">{{ __('flux-filemanager::filemanager.demo_preview') }}
        </flux:heading>

        <div>
            {!! $content !!}
        </div>
    </div>
</div>
