<?php

namespace App\Http\Requests;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Models\District;
use App\Models\Union;
use App\Models\Upazila;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class DonorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'blood_group' => ['required', Rule::enum(BloodGroup::class)],
            'country' => ['required', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:80'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
            'union_id' => ['nullable', 'integer', 'exists:unions,id'],
            'village' => ['nullable', 'string', 'max:120'],
            'address_details' => ['nullable', 'string', 'max:1000'],
            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:'.now()->subYears((int) config('bloodbank.minimum_donor_age'))->toDateString(),
                'after_or_equal:'.now()->subYears((int) config('bloodbank.maximum_donor_age'))->toDateString(),
            ],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'weight_kg' => ['nullable', 'integer', 'min:30', 'max:250'],
            'last_donation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'contacts' => ['nullable', 'array', 'max:5'],
            'contacts.*.label' => ['nullable', 'string', 'max:40'],
            'contacts.*.phone' => ['nullable', 'string', 'max:30', new PhoneNumber],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if ($this->filled('district_id')) {
                    $district = District::query()->find($this->integer('district_id'));

                    if ($district && $this->filled('division_id') && $district->division_id !== $this->integer('division_id')) {
                        $validator->errors()->add('district_id', 'নির্বাচিত জেলা এই বিভাগের নয়।');
                    }
                }

                if ($this->filled('upazila_id')) {
                    $upazila = Upazila::query()->find($this->integer('upazila_id'));

                    if ($upazila && $this->filled('district_id') && $upazila->district_id !== $this->integer('district_id')) {
                        $validator->errors()->add('upazila_id', 'নির্বাচিত উপজেলা এই জেলার নয়।');
                    }
                }

                if ($this->filled('union_id')) {
                    $union = Union::query()->find($this->integer('union_id'));

                    if ($union && $this->filled('upazila_id') && $union->upazila_id !== $this->integer('upazila_id')) {
                        $validator->errors()->add('union_id', 'নির্বাচিত ইউনিয়ন এই উপজেলার নয়।');
                    }
                }

                $contactPhones = collect($this->input('contacts', []))
                    ->filter(fn ($contact): bool => is_array($contact) && filled($contact['phone'] ?? null))
                    ->map(fn (array $contact): string => PhoneNumber::normalize($contact['phone']))
                    ->filter();

                if ($contactPhones->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('contacts', 'একই নম্বর একাধিকবার যোগ করা যাবে না।');
                }

                $minimumWeight = (int) config('bloodbank.minimum_donor_weight_kg');

                if ($this->filled('weight_kg') && $this->integer('weight_kg') < $minimumWeight) {
                    $validator->errors()->add('weight_kg', "রক্তদানের জন্য কমপক্ষে {$minimumWeight} কেজি ওজন প্রয়োজন।");
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'ডোনার হতে কমপক্ষে '.config('bloodbank.minimum_donor_age').' বছর বয়স হতে হবে।',
            'date_of_birth.after_or_equal' => 'সর্বোচ্চ '.config('bloodbank.maximum_donor_age').' বছর বয়স পর্যন্ত ডোনার হওয়া যাবে।',
            'last_donation_date.before_or_equal' => 'শেষ রক্তদানের তারিখ আজকের পরে হতে পারবে না।',
            'country.required' => 'আপনার দেশ নির্বাচন করুন বা লিখুন।',
        ];
    }
}
