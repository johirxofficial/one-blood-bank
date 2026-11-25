<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_dashboard_shows_a_tasteful_donation_link(): void
    {
        $user = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('সাইটটি ফ্রি রাখতে সাহায্য করুন')
            ->assertSee(route('donate.index'), false);
    }

    public function test_dashboard_hides_the_donation_link_when_donations_are_disabled(): void
    {
        Setting::put('donation.enabled', false, 'donation', 'boolean');

        $user = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('সাইটটি ফ্রি রাখতে সাহায্য করুন');
    }

    public function test_dashboard_donation_link_is_translated_in_english_mode(): void
    {
        app()->setLocale('en');
        config(['app.locale' => 'en']);

        $user = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Help keep this site free');
    }
}
