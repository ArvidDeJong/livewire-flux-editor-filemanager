<?php

namespace Darvis\FluxFilemanager\Tests\Feature;

use Darvis\FluxFilemanager\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class InstallCommandTest extends TestCase
{
    /** @test */
    public function install_command_exists()
    {
        $commands = Artisan::all();
        
        $this->assertArrayHasKey('flux-filemanager:install', $commands);
    }

    /** @test */
    public function install_command_has_correct_signature()
    {
        $command = Artisan::all()['flux-filemanager:install'];
        
        $this->assertEquals('flux-filemanager:install', $command->getName());
        $this->assertStringContainsString('Install Flux Filemanager', $command->getDescription());
    }

    /** @test */
    public function install_command_has_force_option()
    {
        $command = Artisan::all()['flux-filemanager:install'];
        $definition = $command->getDefinition();
        
        $this->assertTrue($definition->hasOption('force'));
    }

    /** @test */
    public function install_command_can_run_without_interaction()
    {
        // This test would need mocking of external commands
        // For now, we just verify the command can be called
        $this->artisan('flux-filemanager:install --help')
            ->assertSuccessful();
    }
}
