<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppearanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_brand_and_layout_settings_save_and_render_on_the_public_site(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site' => [
                    'name' => 'Appearance Test',
                    'tagline' => 'Test tagline',
                    'contact_email' => 'hello@example.test',
                    'contact_phone' => '01700000000',
                    'address' => 'Dhaka',
                    'country' => 'Bangladesh',
                    'currency' => 'BDT',
                ],
                'appearance' => [
                    'primary_color' => '#123456',
                    'paper_color' => '#faf7f1',
                    'surface_color' => '#ffffff',
                    'raised_color' => '#eee8dc',
                    'line_color' => '#ddd4c4',
                    'ink_color' => '#111111',
                    'font_sans' => 'inter',
                    'font_display' => 'georgia',
                    'font_size' => '17',
                    'logo_height' => '42',
                    'layout' => 'minimal',
                    'container_width' => 'wide',
                    'radius' => 'round',
                    'section_spacing' => 'compact',
                    'button_style' => 'brand',
                    'card_style' => 'bordered',
                    'sticky_header' => '0',
                    'show_header_tagline' => '0',
                    'show_header_cta' => '0',
                    'show_contact_bar' => '1',
                    'announcement_enabled' => '1',
                    'announcement_text' => 'আজ রক্তদান শিবির',
                    'announcement_url' => '/requests',
                    'hero_style' => 'centered',
                    'show_hero_search' => '0',
                    'show_hero_points' => '0',
                    'show_stats' => '0',
                    'show_urgent_requests' => '0',
                    'show_steps' => '0',
                    'show_community' => '0',
                    'show_donor_card' => '0',
                    'show_cta' => '0',
                    'footer_style' => 'minimal',
                    'show_footer_contact' => '0',
                    'show_footer_districts' => '0',
                    'show_footer_credit' => '0',
                    'custom_css' => '.appearance-test { color: #123456; }',
                ],
                'donors' => [
                    'daily_reveal_limit' => '15',
                ],
            ])
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHasNoErrors();

        $this->assertSame('#123456', Setting::get('appearance.primary_color'));
        $this->assertSame('inter', Setting::get('appearance.font_sans'));
        $this->assertSame(17, Setting::get('appearance.font_size'));
        $this->assertSame('wide', Setting::get('appearance.container_width'));
        $this->assertSame('round', Setting::get('appearance.radius'));
        $this->assertSame('brand', Setting::get('appearance.button_style'));
        $this->assertSame('minimal', Setting::get('appearance.footer_style'));
        $this->assertFalse(Setting::get('appearance.show_stats'));
        $this->assertTrue(Setting::get('appearance.announcement_enabled'));

        auth()->logout();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('--color-brand-600: #123456', false)
            ->assertSee('radius-round', false)
            ->assertSee('spacing-compact', false)
            ->assertSee('btn-style-brand', false)
            ->assertSee('card-style-bordered', false)
            ->assertSee('layout-minimal', false)
            ->assertSee('Inter', false)
            ->assertSee('.appearance-test { color: #123456; }', false)
            ->assertSee('আজ রক্তদান শিবির')
            ->assertSee('href="/requests"', false)
            ->assertSee('hello@example.test')
            ->assertSee('01700000000')
            ->assertDontSee('রেজিস্টার')
            ->assertDontSee('ডোনারের নম্বর শুধু লগইন করা')
            ->assertDontSee('নম্বর ভেরিফাইড')
            ->assertDontSee('জেলা কভার')
            ->assertDontSee('জরুরি রক্তের অনুরোধ')
            ->assertDontSee('তিন ধাপে রক্তদাতার সাথে যুক্ত হোন')
            ->assertDontSee('ডোনার কমিউনিটি')
            ->assertDontSee('ডিজিটাল ডোনার আইডি কার্ড')
            ->assertDontSee('এক ব্যাগ রক্ত বাঁচাতে পারে একটি জীবন')
            ->assertDontSee('জনপ্রিয় জেলা')
            ->assertDontSee('ডেভেলপার ও রক্ষণাবেক্ষণ');
    }

    public function test_brand_and_layout_panel_shows_all_customization_groups(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit', ['tab' => 'appearance']))
            ->assertOk()
            ->assertSee('ব্র্যান্ড ও লেআউট')
            ->assertSee('টাইপোগ্রাফি')
            ->assertSee('লেআউট ও স্টাইল')
            ->assertSee('হেডার ও ঘোষণা')
            ->assertSee('হোমপেজ সেকশন')
            ->assertSee('ফুটার')
            ->assertSee('কাস্টম CSS')
            ->assertSee('name="appearance[paper_color]"', false)
            ->assertSee('name="appearance[font_sans]"', false)
            ->assertSee('name="appearance[announcement_text]"', false)
            ->assertSee('name="appearance[custom_css]"', false);
    }

    public function test_brand_and_layout_settings_reject_invalid_values(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site' => [
                    'name' => 'Invalid Appearance',
                ],
                'appearance' => [
                    'primary_color' => '#123456',
                    'paper_color' => 'not-a-color',
                    'font_sans' => 'comic-sans',
                    'font_size' => '99',
                    'layout' => 'classic',
                    'radius' => 'pill',
                ],
                'donors' => [
                    'daily_reveal_limit' => '15',
                ],
            ])
            ->assertRedirect()
            ->assertSessionHasErrors([
                'appearance.paper_color',
                'appearance.font_sans',
                'appearance.font_size',
                'appearance.radius',
            ]);
    }
}
