<?php

namespace Darvis\FluxFilemanager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'flux-filemanager:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install Flux Filemanager package with all dependencies';

    public function handle(): int
    {
        $this->info('🚀 Installing Flux Filemanager...');
        $this->newLine();

        // Step 1: Install Laravel Filemanager
        if ($this->confirm('Install Laravel Filemanager?', true)) {
            $this->step1InstallLaravelFilemanager();
        }

        // Step 2: Publish Laravel Filemanager config
        if ($this->confirm('Publish Laravel Filemanager configuration?', true)) {
            $this->step2PublishLfmConfig();
        }

        // Step 3: Configure standard LFM routes
        if ($this->confirm('Configure standard Laravel Filemanager routes?', true)) {
            $this->step3ConfigureRoutes();
        }

        // Step 4: Create storage directories
        if ($this->confirm('Create storage directories?', true)) {
            $this->step3CreateStorageDirectories();
        }

        // Step 5: Install NPM dependencies
        if ($this->confirm('Install NPM dependencies (@tiptap/extension-image)?', true)) {
            $this->step4InstallNpmDependencies();
        }

        // Step 6: Publish package assets
        if ($this->confirm('Publish Flux Filemanager assets?', true)) {
            $this->step5PublishAssets();
        }

        // Step 7: Auto-configure app.js resources
        if ($this->confirm('Automatically configure resources/js/app.js?', true)) {
            $this->step6ConfigureAppJs();
        }

        // Step 8: Build assets
        if ($this->confirm('Build assets with npm?', true)) {
            $this->step6BuildAssets();
        }

        $this->newLine();
        $this->info('✅ Installation complete!');
        $this->newLine();

        $this->displayNextSteps();

        return self::SUCCESS;
    }

    protected function runTask(string $description, callable $callback): void
    {
        $this->line("- {$description}...");

        try {
            $success = (bool) $callback();
        } catch (\Throwable $exception) {
            $this->error("  ✗ {$description} failed: {$exception->getMessage()}");
            return;
        }

        if ($success) {
            $this->info("  ✓ {$description} completed");
        } else {
            $this->warn("  ⚠ {$description} not fully successful");
        }
    }

    protected function step1InstallLaravelFilemanager(): void
    {
        $this->runTask('Installing Laravel Filemanager', function () {
            exec('composer require unisharp/laravel-filemanager 2>&1', $output, $exitCode);
            return $exitCode === 0;
        });
    }

    protected function step2PublishLfmConfig(): void
    {
        $this->runTask('Publishing Laravel Filemanager configuration', function () {
            $this->call('vendor:publish', ['--tag' => 'lfm_config']);
            $this->call('vendor:publish', ['--tag' => 'lfm_public']);
            return true;
        });
    }

    protected function step3ConfigureRoutes(): void
    {
        $this->runTask('Configuring standard Laravel Filemanager routes', function () {
            $lfmConfigPath = config_path('lfm.php');

            if (! File::exists($lfmConfigPath)) {
                $this->warn('  ⚠ config/lfm.php not found, skipping route configuration.');
                return false;
            }

            $content = File::get($lfmConfigPath);

            $content = preg_replace(
                "/'use_package_routes'\\s*=>\\s*(true|false),/",
                "'use_package_routes' => true,",
                $content
            );

            $content = preg_replace(
                "/'url_prefix'\\s*=>\\s*'[^']*',/",
                "'url_prefix' => 'filemanager',",
                $content
            );

            File::put($lfmConfigPath, $content);

            return true;
        });
    }

    protected function step3CreateStorageDirectories(): void
    {
        $this->runTask('Creating storage directories', function () {
            $this->call('storage:link');
            
            $directories = [
                public_path('storage/photos'),
                public_path('storage/files'),
            ];

            foreach ($directories as $directory) {
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
            }

            return true;
        });
    }

    protected function step4InstallNpmDependencies(): void
    {
        $this->runTask('Installing NPM dependencies', function () {
            exec('npm install @tiptap/core @tiptap/extension-image @tiptap/extension-link 2>&1', $output, $exitCode);
            return $exitCode === 0;
        });
    }

    protected function step5PublishAssets(): void
    {
        $this->runTask('Publishing Flux Filemanager assets', function () {
            $this->call('vendor:publish', [
                '--tag' => 'flux-filemanager-config',
                '--force' => $this->option('force'),
            ]);
            $this->call('vendor:publish', [
                '--tag' => 'flux-filemanager-assets',
                '--force' => $this->option('force'),
            ]);
            $this->call('vendor:publish', [
                '--tag' => 'flux-filemanager-views',
                '--force' => $this->option('force'),
            ]);
            return true;
        });
    }

    protected function step6BuildAssets(): void
    {
        $this->runTask('Building assets', function () {
            exec('npm run build 2>&1', $output, $exitCode);
            return $exitCode === 0;
        });
    }

    protected function step6ConfigureAppJs(): void
    {
        $this->runTask('Configuring resources/js/app.js', function () {
            $appJsPath = resource_path('js/app.js');

            if (! File::exists($appJsPath)) {
                $this->warn('  ⚠ resources/js/app.js not found, skipping automatic JS configuration.');
                return false;
            }

            $content = File::get($appJsPath);

            $importLines = [
                "import Link from '@tiptap/extension-link'",
                "import Image from '@tiptap/extension-image'",
                "import { initLaravelFilemanager } from '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js'",
                "import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'",
                "import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'",
            ];

            $missingImports = array_filter($importLines, fn ($line) => ! str_contains($content, $line));

            if (! empty($missingImports)) {
                $content = implode(PHP_EOL, $missingImports).PHP_EOL.$content;
            }

            $setupMarkerStart = '// flux-filemanager:start';
            $setupMarkerEnd = '// flux-filemanager:end';

            if (! str_contains($content, $setupMarkerStart)) {
                $setupBlock = <<<JS

{$setupMarkerStart}
const FluxSafeImage = Image.extend({
    addNodeView() {
        return () => null
    },
    addAttributes() {
        return {
            ...this.parent?.(),
            class: {
                default: 'tiptap-image',
                parseHTML: element => element.getAttribute('class'),
                renderHTML: attributes => {
                    if (!attributes.class) return {}
                    return { class: attributes.class }
                },
            },
            style: {
                default: null,
                parseHTML: element => element.getAttribute('style'),
                renderHTML: attributes => {
                    if (!attributes.style) return {}
                    return { style: attributes.style }
                },
            },
            'data-align': {
                default: null,
                parseHTML: element => element.getAttribute('data-align'),
                renderHTML: attributes => {
                    if (!attributes['data-align']) return {}
                    return { 'data-align': attributes['data-align'] }
                },
            },
        }
    },
})

document.addEventListener('flux:editor', (e) => {
    if (!e.detail?.registerExtension || e.detail.__fluxFilemanagerExtensionsRegistered) return

    e.detail.__fluxFilemanagerExtensionsRegistered = true

    e.detail.registerExtension(Link.configure({
        openOnClick: false,
        HTMLAttributes: {
            rel: 'noopener noreferrer nofollow',
        },
    }).extend({
        addAttributes() {
            return {
                ...this.parent?.(),
                target: {
                    default: '_blank',
                    parseHTML: element => element.getAttribute('target'),
                    renderHTML: attributes => {
                        if (!attributes.target) return {}
                        return { target: attributes.target }
                    },
                },
                class: {
                    default: null,
                    parseHTML: element => element.getAttribute('class'),
                    renderHTML: attributes => {
                        if (!attributes.class) return {}
                        return { class: attributes.class }
                    },
                },
                style: {
                    default: null,
                    parseHTML: element => element.getAttribute('style'),
                    renderHTML: attributes => {
                        if (!attributes.style) return {}
                        return { style: attributes.style }
                    },
                },
            }
        },
    }))

    e.detail.registerExtension(FluxSafeImage.configure({
        inline: true,
        allowBase64: true,
        resize: false,
        HTMLAttributes: {
            class: 'tiptap-image',
        },
    }))
})
{$setupMarkerEnd}
JS;

                $content = rtrim($content).PHP_EOL.$setupBlock.PHP_EOL;
            }

            if (! str_contains($content, 'initLaravelFilemanager()')) {
                $content = rtrim($content).PHP_EOL.PHP_EOL.'initLaravelFilemanager()'.PHP_EOL;
            }

            File::put($appJsPath, $content);

            return true;
        });
    }

    protected function displayNextSteps(): void
    {
        $this->warn('⚠️  IMPORTANT: Next steps:');
        $this->newLine();

        $this->line('1. Standard Laravel Filemanager routes are configured by setting `use_package_routes = true` in config/lfm.php.');
        $this->line('   <fg=gray>// Default route prefix: /filemanager</>');
        $this->newLine();

        $this->line('2. resources/js/app.js is auto-configured by this installer (when enabled).');
        $this->line('   <fg=gray>// Includes Flux-safe Link + Image extension setup and initLaravelFilemanager()</>');
        $this->newLine();

        $this->line('3. Use the component in your Blade files:');
        $this->line('   <fg=gray><x-flux-filemanager-editor wire:model="content" /></>');
        $this->newLine();

        $this->info('📚 For more information, see the README.md file.');
    }
}
