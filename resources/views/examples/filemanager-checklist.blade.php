@php
    use Darvis\FluxFilemanager\Support\InstallationCheck;

    // The same list the flux-filemanager:check command prints, so the two can't drift apart.
    $checks = app(InstallationCheck::class)->results();

    $okCount = collect($checks)->where('status', InstallationCheck::OK)->count();
    $totalCount = count($checks);

    $appUrlHost = parse_url((string) config('app.url'), PHP_URL_HOST);
    $hostMatches = filled($appUrlHost) && strcasecmp((string) $appUrlHost, request()->getHost()) === 0;
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
                    @php
                        $key = 'flux-filemanager::filemanager.checklist_'.$check['key'];
                        $label = \Illuminate\Support\Facades\Lang::has($key) ? __($key) : $check['label'];
                    @endphp

                    <div class="flex items-start justify-between gap-4 px-4 py-3">
                        <div>
                            <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $label }}</span>

                            @if ($check['status'] !== InstallationCheck::OK && $check['hint'] !== '')
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $check['hint'] }}</p>
                            @endif
                        </div>

                        @if ($check['status'] === InstallationCheck::OK)
                            <span
                                class="inline-flex shrink-0 rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">{{ __('flux-filemanager::filemanager.checklist_status_ok') }}</span>
                        @else
                            <span
                                class="inline-flex shrink-0 rounded-md bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">{{ __('flux-filemanager::filemanager.checklist_status_missing') }}</span>
                        @endif
                    </div>
                @endforeach

                {{-- Only a request knows the host being used, so this one lives here and not in the command. --}}
                <div class="flex items-start justify-between gap-4 px-4 py-3">
                    <span
                        class="text-sm text-zinc-800 dark:text-zinc-200">{{ __('flux-filemanager::filemanager.checklist_app_url_matches_host') }}</span>

                    @if ($hostMatches)
                        <span
                            class="inline-flex shrink-0 rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">{{ __('flux-filemanager::filemanager.checklist_status_ok') }}</span>
                    @else
                        <span
                            class="inline-flex shrink-0 rounded-md bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">{{ __('flux-filemanager::filemanager.checklist_status_missing') }}</span>
                    @endif
                </div>
            </div>
        </flux:card>

        <flux:text size="sm">{{ __('flux-filemanager::filemanager.checklist_cli_hint') }}</flux:text>
    </div>
@endcomponent
