<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\DonorStatus;
use App\Enums\ReviewStatus;
use App\Enums\UserRole;
use App\Models\BloodRequest;
use App\Models\District;
use App\Models\Donor;
use App\Models\Rating;
use App\Models\Union;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Site Admin',
                'email' => 'admin@thebloodbank.test',
                'phone' => '01700000001',
                'password' => 'password',
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        );

        $manager = User::query()->firstOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Area Manager',
                'email' => 'manager@thebloodbank.test',
                'phone' => '01700000002',
                'password' => 'password',
                'role' => UserRole::Manager,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        );

        $staff = User::query()->firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Verification Staff',
                'email' => 'staff@thebloodbank.test',
                'phone' => '01700000003',
                'password' => 'password',
                'role' => UserRole::Staff,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        );

        $demoUser = User::query()->firstOrCreate(
            ['username' => 'donor'],
            [
                'name' => 'রাকিব হাসান',
                'email' => 'donor@thebloodbank.test',
                'phone' => '01700000004',
                'password' => 'password',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        );

        if (Donor::query()->exists()) {
            $this->command?->info('Demo donors already exist, skipping donor demo data.');

            return;
        }

        $districts = District::query()->with('upazilas')->get();

        if ($districts->isEmpty()) {
            $this->command?->warn('Geo data missing. Run GeoLocationSeeder first.');

            return;
        }

        $demoDonor = Donor::query()->create([
            'user_id' => $demoUser->id,
            'blood_group' => BloodGroup::OPositive,
            'division_id' => $districts->first()->division_id,
            'district_id' => $districts->first()->id,
            'upazila_id' => $districts->first()->upazilas->first()?->id,
            'village' => 'পূর্ব পাড়া',
            'address_details' => 'বাসা ১২, রোড ৩, শহর এলাকা',
            'date_of_birth' => now()->subYears(27),
            'gender' => 'male',
            'weight_kg' => 68,
            'last_donation_date' => now()->subDays(20),
            'status' => DonorStatus::Approved,
            'verified_at' => now(),
            'verified_by' => $admin->id,
            'total_donations' => 5,
        ]);

        /** @var Collection<int, User> $reviewerPool */
        $reviewerPool = collect();

        foreach (range(1, 40) as $index) {
            $district = $districts->random();
            $upazila = $district->upazilas->random();

            $user = User::factory()->create([
                'name' => fake('bn_BD')->name(),
                'username' => 'donor'.$index,
                'phone' => '018'.str_pad((string) $index, 8, '0', STR_PAD_LEFT),
            ]);

            Donor::factory()->create([
                'user_id' => $user->id,
                'blood_group' => fake()->randomElement(BloodGroup::values()),
                'division_id' => $district->division_id,
                'district_id' => $district->id,
                'upazila_id' => $upazila->id,
                'union_id' => Union::query()->where('upazila_id', $upazila->id)->inRandomOrder()->value('id'),
                'last_donation_date' => fake()->randomElement([
                    now()->subDays(fake()->numberBetween(5, 100)),
                    now()->subMonths(fake()->numberBetween(5, 18)),
                    null,
                ]),
                'status' => fake()->randomElement([DonorStatus::Approved, DonorStatus::Approved, DonorStatus::Approved, DonorStatus::Pending]),
            ]);

            $reviewerPool->push($user);
        }

        $demoDonor->refreshRatingSummary();

        Donor::query()->approved()->inRandomOrder()->take(20)->get()->each(function (Donor $donor) use ($reviewerPool): void {
            $reviewers = $reviewerPool
                ->reject(fn (User $user) => $user->id === $donor->user_id)
                ->shuffle()
                ->take(fake()->numberBetween(1, 5));

            foreach ($reviewers as $reviewer) {
                Rating::query()->create([
                    'donor_id' => $donor->id,
                    'user_id' => $reviewer->id,
                    'rating' => fake()->numberBetween(3, 5),
                    'comment' => fake()->randomElement([
                        'দ্রুত সাড়া দিয়েছেন, অনেক ধন্যবাদ।',
                        'খুব helpful একজন মানুষ।',
                        'রক্ত দিতে কোনো দ্বিধা করেননি।',
                        null,
                    ]),
                    'status' => ReviewStatus::Published,
                ]);
            }
        });

        foreach (range(1, 5) as $index) {
            $district = $districts->random();

            BloodRequest::query()->create([
                'user_id' => $reviewerPool->random()->id,
                'patient_name' => fake('bn_BD')->name(),
                'blood_group' => fake()->randomElement(['O+', 'A+', 'B+', 'AB+', 'O-']),
                'units' => fake()->numberBetween(1, 3),
                'hospital' => $district->name.' মেডিকেল কলেজ হাসপাতাল',
                'district_id' => $district->id,
                'upazila_id' => $district->upazilas->random()->id,
                'contact_phone' => '019'.str_pad((string) $index, 8, '0', STR_PAD_LEFT),
                'needed_at' => now()->addHours(fake()->numberBetween(6, 72)),
                'note' => 'জরুরি রক্ত প্রয়োজন।',
            ]);
        }

        $this->command?->info('Demo data ready. Admin: admin@thebloodbank.test / password — Staff: staff@thebloodbank.test / password');
    }
}
