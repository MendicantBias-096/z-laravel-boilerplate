<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_stored_value(): void
    {
        Setting::create(['key' => 'app_name', 'value' => 'Mi App']);

        $this->assertEquals('Mi App', Setting::get('app_name'));
    }

    public function test_get_returns_default_when_key_does_not_exist(): void
    {
        $this->assertEquals('Default', Setting::get('nonexistent_key', 'Default'));
    }

    public function test_get_returns_null_when_no_default_provided(): void
    {
        $this->assertNull(Setting::get('nonexistent_key'));
    }

    public function test_set_creates_new_setting(): void
    {
        Setting::set('site_url', 'https://example.com');

        $this->assertDatabaseHas('settings', [
            'key'   => 'site_url',
            'value' => 'https://example.com',
        ]);
    }

    public function test_set_updates_existing_setting(): void
    {
        Setting::create(['key' => 'app_name', 'value' => 'Antiguo']);
        Setting::set('app_name', 'Nuevo');

        $this->assertDatabaseHas('settings', ['key' => 'app_name', 'value' => 'Nuevo']);
        $this->assertDatabaseMissing('settings', ['key' => 'app_name', 'value' => 'Antiguo']);
    }

    public function test_cache_is_invalidated_after_set(): void
    {
        Setting::set('app_name', 'Primera vez');
        Setting::set('app_name', 'Segunda vez');

        $this->assertEquals('Segunda vez', Setting::get('app_name'));
    }

    public function test_get_uses_cache_on_repeated_calls(): void
    {
        Setting::set('cached_key', 'valor');

        Cache::flush();
        Setting::get('cached_key');

        $this->assertTrue(Cache::has('setting:cached_key'));
    }
}
