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

        // Step 3: Create storage directories
        if ($this->confirm('Create storage directories?', true)) {
            $this->step3CreateStorageDirectories();
        }

        // Step 4: Install NPM dependencies
        if ($this->confirm('Install NPM dependencies (@tiptap/extension-image)?', true)) {
            $this->step4InstallNpmDependencies();
        }

        // Step 5: Publish package assets
        if ($this->confirm('Publish Flux Filemanager assets?', true)) {
            $this->step5PublishAssets();
        }

        // Step 6: Build assets
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
            $this->error("  ✗ {$description} mislukt: {$exception->getMessage()}");
            return;
        }

        if ($success) {
            $this->info("  ✓ {$description} voltooid");
        } else {
            $this->warn("  ⚠ {$description} niet volledig geslaagd");
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
            exec('npm install @tiptap/core @tiptap/extension-image @tiptap/extension-link prosemirror-state 2>&1', $output, $exitCode);
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

    protected function displayNextSteps(): void
    {
        $this->warn('⚠️  IMPORTANT: Next steps:');
        $this->newLine();

        $this->line('1. Add Laravel Filemanager routes to your routes/web.php:');
        $this->line('   <fg=gray>Route::prefix(\'cms/laravel-filemanager\')->middleware([\'auth\'])->group(function () {</>');
        $this->line('   <fg=gray>    \\UniSharp\\LaravelFilemanager\\Lfm::routes();</>');
        $this->line('   <fg=gray>});</>');
        $this->newLine();
        $this->line('   <fg=gray>// For staff guard projects:</>');
        $this->line('   <fg=gray>// ->middleware([\'auth:staff\', \\App\\Http\\Middleware\\EnsureStaffEmailIsVerified::class])</>');
        $this->newLine();

        $this->line('2. Add the following to your resources/js/app.js:');
        $this->line('   <fg=gray>import { initLaravelFilemanager } from "../../vendor/darvis/livewire-flux-editor-filemanager/resources/js/laravel-filemanager.js"</>');
        $this->line('   <fg=gray>import Link from "@tiptap/extension-link"</>');
        $this->line('   <fg=gray>import Image from "@tiptap/extension-image"</>');
        $this->line('   <fg=gray>import "../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/tiptap-image.css"</>');
        $this->line('   <fg=gray>import "../../vendor/darvis/livewire-flux-editor-filemanager/resources/css/file-link-modal.css"</>');
        $this->newLine();
        $this->line('   <fg=gray>initLaravelFilemanager();</>');
        $this->newLine();

        $this->line('3. Use the component in your Blade files:');
        $this->line('   <fg=gray><x-flux-filemanager-editor wire:model="content" /></>');
        $this->newLine();

        $this->info('📚 For more information, see the README.md file.');
    }
}
