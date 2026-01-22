<?php

namespace Darvis\FluxFilemanager\Tests\Unit;

use Darvis\FluxFilemanager\Tests\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_has_default_filemanager_url()
    {
        $url = config('flux-filemanager.url');
        
        $this->assertEquals('/cms/laravel-filemanager', $url);
    }

    /** @test */
    public function it_has_popup_configuration()
    {
        $popup = config('flux-filemanager.popup');
        
        $this->assertIsArray($popup);
        $this->assertArrayHasKey('width', $popup);
        $this->assertArrayHasKey('height', $popup);
        $this->assertEquals(900, $popup['width']);
        $this->assertEquals(600, $popup['height']);
    }

    /** @test */
    public function it_has_resize_presets()
    {
        $presets = config('flux-filemanager.resize_presets');
        
        $this->assertIsArray($presets);
        $this->assertContains('25%', $presets);
        $this->assertContains('50%', $presets);
        $this->assertContains('75%', $presets);
        $this->assertContains('100%', $presets);
    }

    /** @test */
    public function it_has_custom_width_range()
    {
        $customWidth = config('flux-filemanager.custom_width');
        
        $this->assertIsArray($customWidth);
        $this->assertEquals(1, $customWidth['min']);
        $this->assertEquals(100, $customWidth['max']);
    }

    /** @test */
    public function it_has_error_messages()
    {
        $messages = config('flux-filemanager.messages');
        
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('popup_blocked', $messages);
        $this->assertArrayHasKey('no_images_selected', $messages);
        $this->assertArrayHasKey('filemanager_not_found', $messages);
    }

    /** @test */
    public function filemanager_url_can_be_overridden_via_env()
    {
        config(['flux-filemanager.url' => '/admin/filemanager']);
        
        $url = config('flux-filemanager.url');
        
        $this->assertEquals('/admin/filemanager', $url);
    }
}
