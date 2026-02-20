@php
    $jsAppPath = resource_path('js/app.js');
    $jsAppContent = file_exists($jsAppPath) ? file_get_contents($jsAppPath) : '';
    $appUrlHost = parse_url((string) config('app.url'), PHP_URL_HOST);
    $currentHost = request()->getHost();

    $checks = [
        [
            'label' => __('flux-filemanager::filemanager.checklist_installed'),
            'ok' => class_exists(\Darvis\FluxFilemanager\FluxFilemanagerServiceProvider::class),
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_package_available'),
            'ok' => class_exists(\UniSharp\LaravelFilemanager\Lfm::class),
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_flux_config_available'),
            'ok' => file_exists(config_path('flux-filemanager.php')),
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_lfm_config_available'),
            'ok' => file_exists(config_path('lfm.php')),
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_routes_enabled'),
            'ok' => (bool) config('lfm.use_package_routes', false),
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_prefix_set'),
            'ok' => config('lfm.url_prefix') === 'filemanager',
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_js_init_available'),
            'ok' => \Illuminate\Support\Str::contains($jsAppContent, 'initLaravelFilemanager()'),
        ],
        [
            'label' => __('flux-filemanager::filemanager.checklist_app_url_matches_host'),
            'ok' => filled($appUrlHost) && strcasecmp((string) $appUrlHost, (string) $currentHost) === 0,
        ],
    ];

    $okCount = collect($checks)->where('ok', true)->count();
    $totalCount = count($checks);
@endphp

@component('flux-filemanager::examples.layout', ['title' => 'Darvis Filemanager Checklist'])
    <div class="space-y-6">
        <div>
            <flux:heading level="1" size="xl">{{ __('flux-filemanager::filemanager.checklist_title') }}
            </flux:heading>
            <flux:text class="mt-1">
                {{ __('flux-filemanager::filemanager.checklist_summary', ['okCount' => $okCount, 'totalCount' => $totalCount]) }}
            </flux:text>
        </div>

        <flux:card>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($checks as $check)
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $check['label'] }}</span>

                        @if ($check['ok'])
                            <span
                                class="inline-flex rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">{{ __('flux-filemanager::filemanager.checklist_status_ok') }}</span>
                        @else
                            <span
                                class="inline-flex rounded-md bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">{{ __('flux-filemanager::filemanager.checklist_status_missing') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </flux:card>

        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('flux-filemanager::filemanager.url') }}:
            {{ url()->current() }}</p>
    </div>
@endcomponent
