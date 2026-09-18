<?php

namespace Darvis\FluxFilemanager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'flux-filemanager:install
                            {--force : Overwrite the published config file}';

    protected $description = 'Install Flux Filemanager: Laravel Filemanager config and routes, storage, npm packages and the app.js setup';

    private const NPM_PACKAGES = [
        '@tiptap/core',
        '@tiptap/pm',
        '@tiptap/extension-image',
        '@tiptap/extension-link',
    ];

    private const JS_MODULE = '../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js';

    private const IMPORTS = [
        "import Link from '@tiptap/extension-link'",
        "import Image from '@tiptap/extension-image'",
        "import { initLaravelFilemanager, createImageDropPastePlugin } from '".self::JS_MODULE."'",
        "import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css'",
        "import '../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css'",
    ];

    public function handle(): int
    {
        $this->info('Installing Flux Filemanager...');
        $this->newLine();

        if ($this->confirm('Publish the Laravel Filemanager configuration and assets?', true)) {
            $this->publishLfmConfig();
        }

        if ($this->confirm('Enable the Laravel Filemanager routes at /filemanager?', true)) {
            $this->configureLfmRoutes();
        }

        if ($this->confirm('Create the storage link and upload directories?', true)) {
            $this->createStorageDirectories();
        }

        if ($this->confirm('Install the npm packages (TipTap)?', true)) {
            $this->installNpmDependencies();
        }

        if ($this->confirm('Publish config/flux-filemanager.php?', true)) {
            $this->publishConfig();
        }

        if ($this->confirm('Add the editor setup to resources/js/app.js?', true)) {
            $this->configureAppJs();
        }

        if ($this->confirm('Build the assets with npm?', true)) {
            $this->buildAssets();
        }

        $this->newLine();
        $this->info('Installation complete.');
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

    protected function publishLfmConfig(): void
    {
        $this->runTask('Publishing Laravel Filemanager configuration and assets', function () {
            $this->call('vendor:publish', ['--tag' => 'lfm_config']);
            $this->call('vendor:publish', ['--tag' => 'lfm_public']);

            return true;
        });
    }

    protected function configureLfmRoutes(): void
    {
        $this->runTask('Enabling the Laravel Filemanager routes', function () {
            $path = config_path('lfm.php');

            if (! File::exists($path)) {
                $this->warn('  ⚠ config/lfm.php not found, skipping route configuration.');

                return false;
            }

            $content = File::get($path);

            $content = (string) preg_replace(
                "/'use_package_routes'\\s*=>\\s*(true|false),/",
                "'use_package_routes' => true,",
                $content
            );

            $content = (string) preg_replace(
                "/'url_prefix'\\s*=>\\s*'[^']*',/",
                "'url_prefix' => 'filemanager',",
                $content
            );

            File::put($path, $content);

            return true;
        });
    }

    protected function createStorageDirectories(): void
    {
        $this->runTask('Creating the storage link and upload directories', function () {
            $this->call('storage:link');

            foreach ([public_path('storage/photos'), public_path('storage/files')] as $directory) {
                if (! File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
            }

            return true;
        });
    }

    protected function installNpmDependencies(): void
    {
        $this->runTask('Installing npm packages', function () {
            exec('npm install '.implode(' ', self::NPM_PACKAGES).' 2>&1', $output, $exitCode);

            return $exitCode === 0;
        });
    }

    protected function publishConfig(): void
    {
        $this->runTask('Publishing config/flux-filemanager.php', function () {
            $this->call('vendor:publish', [
                '--tag' => 'flux-filemanager-config',
                '--force' => (bool) $this->option('force'),
            ]);

            return true;
        });
    }

    protected function buildAssets(): void
    {
        $this->runTask('Building assets', function () {
            exec('npm run build 2>&1', $output, $exitCode);

            return $exitCode === 0;
        });
    }

    /**
     * Adds the imports, the extension setup block from resources/stubs and the init call.
     * Runs again without duplicating anything, and upgrades the import line from 1.1.x.
     */
    protected function configureAppJs(): void
    {
        $this->runTask('Configuring resources/js/app.js', function () {
            $path = resource_path('js/app.js');

            if (! File::exists($path)) {
                $this->warn('  ⚠ resources/js/app.js not found, skipping.');

                return false;
            }

            $content = File::get($path);

            // 1.1.x imported initLaravelFilemanager alone; a second import of the same name would be a syntax error.
            $content = str_replace(
                "import { initLaravelFilemanager } from '".self::JS_MODULE."'",
                "import { initLaravelFilemanager, createImageDropPastePlugin } from '".self::JS_MODULE."'",
                $content
            );

            $missingImports = array_filter(self::IMPORTS, fn (string $line) => ! str_contains($content, $line));

            if ($missingImports !== []) {
                $content = implode(PHP_EOL, $missingImports).PHP_EOL.$content;
            }

            if (! str_contains($content, '// flux-filemanager:start')) {
                $stub = File::get(__DIR__.'/../../resources/stubs/flux-filemanager-setup.js');
                $content = rtrim($content).PHP_EOL.PHP_EOL.rtrim($stub).PHP_EOL;
            } elseif (! str_contains($content, 'addProseMirrorPlugins')) {
                $this->warn('  ⚠ app.js has a setup block from an older version without drag and drop. Compare it with examples/app.js in the package.');
            }

            if (! str_contains($content, 'initLaravelFilemanager()')) {
                $content = rtrim($content).PHP_EOL.PHP_EOL.'initLaravelFilemanager()'.PHP_EOL;
            }

            File::put($path, $content);

            return true;
        });
    }

    protected function displayNextSteps(): void
    {
        $this->warn('Next steps:');
        $this->newLine();

        $this->line('1. Protect the file manager: set `middlewares` to [\'web\', \'auth\'] in config/lfm.php.');
        $this->newLine();

        $this->line('2. Use the component in a Livewire view:');
        $this->line('   <fg=gray><x-flux-filemanager-editor wire:model="content" /></>');
        $this->newLine();

        $this->line('3. To try it, set FLUX_FILEMANAGER_DEMO_ROUTES=true and open /darvis/editor-demo (local only, no auth).');
        $this->newLine();

        $this->info('Documentation: https://arviddejong.github.io/livewire-flux-editor-filemanager/');
    }
}
