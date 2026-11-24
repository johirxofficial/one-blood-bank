<?php

namespace Tests\Feature;

use App\Enums\BloodGroup;
use App\Enums\DonorStatus;
use App\Models\Donor;
use App\Models\DonorContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonorContactTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_phone_reveal_returns_primary_and_additional_contacts(): void
    {
        $donor = Donor::query()
            ->where('status', DonorStatus::Approved)
            ->with('user')
            ->firstOrFail();

        DonorContact::query()->create([
            'donor_id' => $donor->id,
            'label' => 'Emergency',
            'phone' => '+8801999999999',
        ]);

        $response = $this->actingAs($donor->user)
            ->postJson(route('donors.reveal', $donor->user->username));

        $response->assertOk()
            ->assertJsonPath('phones.0.label', 'Primary')
            ->assertJsonPath('phones.0.phone', $donor->user->phone)
            ->assertJsonPath('phones.1.label', 'Emergency')
            ->assertJsonPath('phones.1.phone', '+8801999999999');
    }

    public function test_donor_application_stores_multiple_contacts_and_skips_primary_duplicate(): void
    {
        $user = User::factory()->create([
            'name' => 'Multi Contact Tester',
            'username' => 'multi-contact-tester',
            'email' => 'multi-contact-tester@example.test',
            'phone' => '01711111111',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('donor.apply.store'), [
            'blood_group' => BloodGroup::OPositive->value,
            'country' => 'Bangladesh',
            'state' => '',
            'city' => 'Dhaka',
            'village' => 'Test village',
            'address_details' => 'Test address',
            'date_of_birth' => now()->subYears(30)->toDateString(),
            'gender' => 'male',
            'weight_kg' => 68,
            'last_donation_date' => null,
            'contacts' => [
                ['label' => 'Primary duplicate', 'phone' => '01711111111'],
                ['label' => 'Office', 'phone' => '01722222222'],
                ['label' => 'Emergency', 'phone' => '+8801733333333'],
            ],
        ]);

        $response->assertRedirect();

        $donor = $user->fresh()->donor;

        $this->assertNotNull($donor);
        $this->assertSame(DonorStatus::Pending, $donor->status);
        $this->assertCount(2, $donor->contacts);
        $this->assertSame(['Office', 'Emergency'], $donor->contacts->pluck('label')->all());
        $this->assertSame(['01722222222', '+8801733333333'], $donor->contacts->pluck('phone')->all());
    }

    public function test_duplicate_contact_numbers_are_rejected(): void
    {
        $user = User::factory()->create([
            'name' => 'Duplicate Contact Tester',
            'username' => 'duplicate-contact-tester',
            'email' => 'duplicate-contact-tester@example.test',
            'phone' => '01711111111',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->from(route('donor.apply'))->post(route('donor.apply.store'), [
            'blood_group' => BloodGroup::OPositive->value,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
            'date_of_birth' => now()->subYears(30)->toDateString(),
            'gender' => 'male',
            'weight_kg' => 68,
            'contacts' => [
                ['label' => 'One', 'phone' => '01722222222'],
                ['label' => 'Two', 'phone' => '01722222222'],
            ],
        ]);

        $response->assertRedirect(route('donor.apply'));
        $response->assertSessionHasErrors('contacts');
        $this->assertNull($user->fresh()->donor);
    }
}
