<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_installer_page_is_available_on_a_fresh_installation(): void
    {
        $this->assertSame(0, User::query()->count());
        $this->assertFalse(Storage::disk('local')->exists('installed.lock'));

        $this->get(route('install'))
            ->assertOk()
            ->assertSee('First-time installation wizard', false)
            ->assertSee('Install One Blood Bank', false)
            ->assertSee('Administrator account', false);
    }

    public function test_installer_creates_admin_settings_and_lock_file(): void
    {
        $response = $this->post(route('install.store'), [
            'step' => 5,
            'language' => 'en',
            'country' => 'United States',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'site_name' => 'Life Share',
            'site_tagline' => 'Find a donor, save a life.',
            'site_email' => 'hello@lifeshare.test',
            'site_phone' => '+15551234567',
            'site_address' => 'New York, NY',
            'admin_name' => 'Site Owner',
            'admin_username' => 'owner',
            'admin_email' => 'owner@lifeshare.test',
            'admin_phone' => '+15557654321',
            'admin_password' => 'secret123',
            'admin_password_confirmation' => 'secret123',
            'require_phone_verification' => '1',
            'mail_driver' => 'log',
            'mail_from_name' => 'Life Share',
            'mail_from_address' => 'noreply@lifeshare.test',
            'install_base_data' => '1',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $admin = User::query()->where('email', 'owner@lifeshare.test')->firstOrFail();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertNotNull($admin->phone_verified_at);
        $this->assertTrue((bool) $admin->is_active);
        $this->assertTrue(Storage::disk('local')->exists('installed.lock'));
        $this->assertSame('Life Share', Setting::get('site.name'));
        $this->assertSame('en', Setting::get('others.locale'));
        $this->assertSame('America/New_York', Setting::get('others.timezone'));
        $this->assertSame('USD', Setting::get('site.currency'));
        $this->assertTrue((bool) Setting::get('security.require_phone_verification'));
    }

    public function test_installer_redirects_after_installation(): void
    {
        User::factory()->create(['role' => UserRole::Admin]);

        $this->get(route('install'))->assertRedirect(route('home'));
    }
}
