<?php

namespace Tests\Feature;

use App\Models\Donor;
use App\Models\PhoneVerification;
use App\Models\Post;
use App\Models\Setting;
use App\Models\SiteDonation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_admin_can_open_every_admin_screen(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();
        $donor = Donor::query()->firstOrFail();

        $this->actingAs($admin);

        $routes = [
            route('admin.dashboard'),
            route('admin.donors.index'),
            route('admin.donors.index', ['status' => 'pending']),
            route('admin.donors.show', $donor),
            route('admin.users.index'),
            route('admin.users.edit', $admin),
            route('admin.ratings.index'),
            route('admin.requests.index'),
            route('admin.messages.index'),
            route('admin.messages.create'),
            route('admin.ads.index'),
            route('admin.ads.create'),
            route('admin.pages.index'),
            route('admin.pages.create'),
            route('admin.blog.index'),
            route('admin.blog.create'),
            route('admin.site-donations.index'),
            route('admin.logs.activity'),
            route('admin.logs.mail'),
            route('admin.settings.edit'),
            route('admin.locations.index'),
            route('admin.staff.index'),
            route('admin.staff.create'),
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertOk();
        }

        $this->get(route('admin.locations.sample'))->assertOk();
    }

    public function test_localisation_admin_screens_render_in_english(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();
        app()->setLocale('en');

        $this->actingAs($admin)
            ->get(route('admin.locations.index'))
            ->assertOk()
            ->assertSee('Location import')
            ->assertSee('Upload CSV');

        $this->actingAs($admin)
            ->get(route('admin.settings.edit', ['tab' => 'others']))
            ->assertOk()
            ->assertSee('Location country code')
            ->assertSee('Import a new language');
    }

    public function test_settings_form_saves_dotted_keys(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site' => [
                'name' => 'Test Blood Bank',
                'tagline' => 'Test tagline',
                'contact_email' => 'hello@example.test',
                'contact_phone' => '+8801711111111',
                'address' => 'Dhaka',
                'country' => 'Bangladesh',
                'currency' => 'BDT',
            ],
            'seo' => [
                'title' => 'Test SEO title',
                'description' => 'Test SEO description',
                'keywords' => 'blood, donor',
            ],
            'appearance' => [
                'primary_color' => '#112233',
                'layout' => 'minimal',
                'show_stats' => '0',
                'show_urgent_requests' => '1',
            ],
            'ads' => [
                'enabled' => '1',
                'popunder_enabled' => '0',
                'popunder_code' => '<script>void 0</script>',
            ],
            'donors' => [
                'require_login' => '1',
                'auto_approve' => '0',
                'daily_reveal_limit' => '9',
            ],
            'security' => [
                'require_phone_verification' => '0',
                'require_email_verification' => '1',
                'allow_registration' => '1',
            ],
            'sms' => [
                'enabled' => '0',
                'driver' => 'log',
                'otp_template' => 'Test OTP {code}',
            ],
            'donation' => [
                'enabled' => '1',
                'currency' => 'BDT',
                'min_amount' => '50',
                'goal_amount' => '0',
                'show_goal' => '1',
            ],
            'others' => [
                'blog_enabled' => '1',
                'locale' => 'bn',
                'timezone' => 'Asia/Dhaka',
            ],
            'mail' => [
                'driver' => 'log',
                'welcome_enabled' => '1',
                'application_enabled' => '1',
                'status_enabled' => '0',
                'rating_enabled' => '1',
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertSame('Test Blood Bank', Setting::get('site.name'));
        $this->assertSame('#112233', Setting::get('appearance.primary_color'));
        $this->assertSame('minimal', Setting::get('appearance.layout'));
        $this->assertTrue(Setting::get('ads.enabled'));
        $this->assertFalse(Setting::get('ads.popunder_enabled'));
        $this->assertSame(9, Setting::get('donors.daily_reveal_limit'));
        $this->assertFalse(Setting::get('mail.status_enabled'));
        $this->assertFalse(Setting::get('security.require_phone_verification'));
        $this->assertTrue(Setting::get('security.require_email_verification'));
        $this->assertFalse(Setting::get('sms.enabled'));
        $this->assertSame('Bangladesh', Setting::get('site.country'));
    }

    public function test_manager_can_moderate_but_not_manage_settings(): void
    {
        $manager = User::query()->where('email', 'manager@thebloodbank.test')->firstOrFail();

        $this->actingAs($manager);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.donors.index'))->assertOk();
        $this->get(route('admin.settings.edit'))->assertForbidden();
        $this->get(route('admin.staff.index'))->assertForbidden();
    }

    public function test_inactive_staff_cannot_access_the_admin_portal(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();
        $admin->forceFill(['is_active' => false, 'banned_at' => now()])->save();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_user_management_is_restricted_to_managers_and_admins(): void
    {
        $staff = User::query()->where('email', 'staff@thebloodbank.test')->firstOrFail();
        $manager = User::query()->where('email', 'manager@thebloodbank.test')->firstOrFail();
        $donorUser = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();

        $this->actingAs($staff)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('admin.users.edit', $donorUser))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('admin.users.edit', $staff))
            ->assertForbidden();
    }

    public function test_a_user_can_reapply_after_their_donor_profile_was_deleted(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();
        $donor = Donor::query()->with('user')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.donors.destroy', $donor))
            ->assertRedirect();

        $this->assertSoftDeleted($donor);

        $this->actingAs($donor->user)
            ->post(route('donor.apply.store'), [
                'blood_group' => $donor->blood_group,
                'country' => 'Bangladesh',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('donors', [
            'id' => $donor->id,
            'user_id' => $donor->user_id,
            'deleted_at' => null,
        ]);
    }

    public function test_deleting_a_donor_user_also_removes_the_donor_profile(): void
    {
        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();
        $donorUser = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();
        $donor = $donorUser->donor()->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $donorUser))
            ->assertRedirect();

        $this->assertSoftDeleted($donorUser);
        $this->assertSoftDeleted($donor);
        $this->assertFalse(Donor::query()->approved()->whereKey($donor->id)->exists());
    }

    public function test_donor_directory_requires_verified_phone(): void
    {
        $unverified = User::query()->where('username', 'donor')->firstOrFail();
        $unverified->forceFill(['phone_verified_at' => null])->save();

        $this->actingAs($unverified)
            ->get(route('donors.index'))
            ->assertRedirect(route('verification.notice'));

        $verified = User::query()->where('email', 'staff@thebloodbank.test')->firstOrFail();

        $this->actingAs($verified)
            ->get(route('donors.index'))
            ->assertOk();
    }

    public function test_public_pages_render(): void
    {
        $post = Post::query()->firstOrFail();

        $this->get(route('home'))->assertOk();
        $this->get(route('requests.index'))->assertOk();
        $this->get(route('pages.show', 'about'))->assertOk();
        $this->get(route('blog.index'))->assertOk();
        $this->get(route('blog.show', $post->slug))->assertOk();
        $this->get(route('donate.index'))->assertOk();
    }

    public function test_site_donation_with_local_phone_can_be_submitted_and_moderated(): void
    {
        $response = $this->post(route('donate.store'), [
            'name' => 'Smoke Test Donor',
            'email' => 'smoke-donor@example.test',
            'phone' => '01712345678',
            'amount' => '50',
            'method' => 'bkash',
            'transaction_id' => 'SMOKE-123456',
            'message' => 'Automated smoke test record',
        ]);

        $response->assertRedirect(route('donate.index'));
        $response->assertSessionHasNoErrors();

        $donation = SiteDonation::query()->firstOrFail();
        $this->assertSame('01712345678', $donation->phone);
        $this->assertSame('pending', $donation->status);

        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.site-donations.update', $donation), [
                'status' => 'verified',
                'admin_note' => 'Smoke test verified',
            ])
            ->assertRedirect();

        $this->assertSame('verified', $donation->fresh()->status);

        $this->actingAs($admin)
            ->delete(route('admin.site-donations.destroy', $donation))
            ->assertRedirect();

        $this->assertNull($donation->fresh());
    }

    public function test_guest_can_view_donor_profiles_when_login_is_optional(): void
    {
        Setting::put('donors.require_login', false, 'donors', 'boolean');

        $donor = Donor::query()->with('user')->firstOrFail();

        $this->get(route('donors.index'))->assertOk();
        $this->get(route('donors.show', $donor->user->username))->assertOk();
        $this->get(route('donors.show.username', $donor->user->username))->assertOk();
        $this->assertStringEndsWith('/@'.$donor->user->username, $donor->user->profileUrl());
    }

    public function test_changing_profile_phone_requires_fresh_verification(): void
    {
        $donorUser = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();
        $newPhone = '+8801999999999';

        $this->actingAs($donorUser)
            ->put(route('profile.update'), [
                'name' => $donorUser->name,
                'email' => $donorUser->email,
                'phone' => $newPhone,
            ])
            ->assertRedirect(route('verification.notice'));

        $donorUser->refresh();

        $this->assertSame($newPhone, $donorUser->phone);
        $this->assertNull($donorUser->phone_verified_at);
        $this->assertTrue(
            PhoneVerification::query()
                ->where('phone', $newPhone)
                ->where('purpose', 'register')
                ->exists()
        );
    }

    public function test_required_email_verification_blocks_the_donor_directory(): void
    {
        Setting::put('security.require_phone_verification', false, 'security', 'boolean');
        Setting::put('security.require_email_verification', true, 'security', 'boolean');

        $user = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();
        $user->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($user)
            ->get(route('donors.index'))
            ->assertRedirect(route('profile.edit'));

        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user)
            ->get(route('donors.index'))
            ->assertOk();
    }

    public function test_signed_email_verification_link_marks_the_address_verified(): void
    {
        $user = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();
        $user->forceFill(['email_verified_at' => null])->save();

        $url = URL::temporarySignedRoute(
            'verification.verify.email',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_admin_can_upload_logo_and_favicon(): void
    {
        Storage::fake('public');

        $admin = User::query()->where('email', 'admin@thebloodbank.test')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site' => [
                    'name' => 'Upload Test',
                    'tagline' => 'Test tagline',
                    'contact_email' => 'hello@example.test',
                    'contact_phone' => '01712345678',
                    'address' => 'Dhaka',
                    'country' => 'Bangladesh',
                    'currency' => 'BDT',
                ],
                'appearance' => [
                    'primary_color' => '#D32F2F',
                    'layout' => 'classic',
                ],
                'donors' => [
                    'daily_reveal_limit' => '15',
                ],
                'logo' => UploadedFile::fake()->image('logo.png', 128, 128),
                'favicon' => UploadedFile::fake()->image('favicon.png', 32, 32),
            ])
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHasNoErrors();

        $logoPath = Setting::get('appearance.logo_path');
        $faviconPath = Setting::get('appearance.favicon_path');

        $this->assertNotEmpty($logoPath);
        $this->assertNotEmpty($faviconPath);
        Storage::disk('public')->assertExists($logoPath);
        Storage::disk('public')->assertExists($faviconPath);
    }

    public function test_profile_avatar_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');

        $donorUser = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();

        $this->actingAs($donorUser)
            ->put(route('profile.update'), [
                'name' => $donorUser->name,
                'email' => $donorUser->email,
                'phone' => $donorUser->phone,
                'avatar' => UploadedFile::fake()->image('avatar.png', 200, 200),
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasNoErrors();

        $donorUser->refresh();
        $this->assertNotEmpty($donorUser->avatar_path);
        Storage::disk('public')->assertExists($donorUser->avatar_path);

        $this->actingAs($donorUser)
            ->put(route('profile.update'), [
                'name' => $donorUser->name,
                'email' => $donorUser->email,
                'phone' => $donorUser->phone,
                'remove_avatar' => '1',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasNoErrors();

        $donorUser->refresh();
        $this->assertNull($donorUser->avatar_path);
    }

    public function test_profile_edit_and_donor_card_download_work(): void
    {
        $donorUser = User::query()->where('email', 'donor@thebloodbank.test')->firstOrFail();

        $this->actingAs($donorUser)
            ->get(route('profile.edit'))
            ->assertOk();

        $this->actingAs($donorUser)
            ->get(route('donor.card.download'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
