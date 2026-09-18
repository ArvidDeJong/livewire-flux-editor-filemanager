<?php

namespace Darvis\FluxFilemanager\Console;

use Darvis\FluxFilemanager\Support\InstallationCheck;
use Illuminate\Console\Command;

class CheckCommand extends Command
{
    protected $signature = 'flux-filemanager:check';

    protected $description = 'Check the Flux Filemanager installation and say what to do about every problem';

    public function handle(InstallationCheck $check): int
    {
        $this->newLine();

        $failed = 0;
        $warnings = 0;

        foreach ($check->results() as $result) {
            match ($result['status']) {
                InstallationCheck::OK => $this->line("  <fg=green>✓</> {$result['label']}"),
                InstallationCheck::WARNING => $this->line("  <fg=yellow>!</> {$result['label']}"),
                default => $this->line("  <fg=red>✗</> {$result['label']}"),
            };

            if ($result['status'] === InstallationCheck::OK) {
                continue;
            }

            $result['status'] === InstallationCheck::FAILED ? $failed++ : $warnings++;

            $this->line("    <fg=gray>{$result['hint']}</>");
        }

        $this->newLine();

        if ($failed > 0) {
            $this->error("{$failed} problem(s) to fix. Run php artisan flux-filemanager:install to do most of it for you.");

            return self::FAILURE;
        }

        if ($warnings > 0) {
            $this->warn("Nothing is broken, {$warnings} thing(s) to be aware of.");

            return self::SUCCESS;
        }

        $this->info('The installation is complete.');

        $this->line('<fg=gray>Buttons still doing nothing? Open the browser console: one failing import in app.js stops all of it.</>');

        return self::SUCCESS;
    }
}
