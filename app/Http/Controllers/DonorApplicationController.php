<?php

namespace App\Http\Controllers;

use App\Enums\DonorStatus;
use App\Enums\UserRole;
use App\Http\Requests\DonorApplicationRequest;
use App\Models\ActivityLog;
use App\Models\District;
use App\Models\Division;
use App\Models\Donor;
use App\Models\Union;
use App\Models\Upazila;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Services\MailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonorApplicationController extends Controller
{
    public function create(Request $request): View
    {
        $donor = $request->user()->donor;
        $donor?->load('contacts');

        return view('donor.apply', [
            'donor' => $donor,
            'divisions' => Division::query()->forCountry()->orderBy('name')->get(),
            'districts' => $donor?->division_id
                ? District::query()->forCountry()->where('division_id', $donor->division_id)->orderBy('name')->get()
                : collect(),
            'upazilas' => $donor?->district_id
                ? Upazila::query()->forCountry()->where('district_id', $donor->district_id)->orderBy('name')->get()
                : collect(),
            'unions' => $donor?->upazila_id
                ? Union::query()->forCountry()->where('upazila_id', $donor->upazila_id)->orderBy('name')->get()
                : collect(),
            'countries' => config('countries', []),
        ]);
    }

    public function store(DonorApplicationRequest $request, MailService $mailer): RedirectResponse
    {
        $user = $request->user();
        $donor = $user->donor;

        // A soft-deleted profile still owns the unique user_id, so restore it
        // before the updateOrCreate call when the user applies again.
        if ($donor === null) {
            Donor::withTrashed()->where('user_id', $user->id)->first()?->restore();
            $donor = $user->refresh()->donor;
        }

        $wasApproved = $donor?->isApproved() ?? false;

        if ($request->hasFile('photo')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->forceFill(['avatar_path' => $request->file('photo')->store('avatars', 'public')])->save();
        }

        $validated = $request->validated();
        $attributes = collect($validated)->except(['photo', 'contacts'])->all();
        $attributes['status'] = $wasApproved ? DonorStatus::Approved : DonorStatus::Pending;

        if (! $wasApproved) {
            $attributes['verified_at'] = null;
            $attributes['verified_by'] = null;
            $attributes['rejection_reason'] = null;
        }

        $donor = Donor::query()->updateOrCreate(['user_id' => $user->id], $attributes);

        $primaryPhone = PhoneNumber::normalize($user->phone);
        $contacts = collect($validated['contacts'] ?? [])
            ->filter(fn ($contact): bool => is_array($contact) && filled($contact['phone'] ?? null))
            ->map(fn (array $contact): array => [
                'label' => filled($contact['label'] ?? null) ? trim((string) $contact['label']) : 'Other',
                'phone' => trim((string) $contact['phone']),
            ])
            ->reject(fn (array $contact): bool => PhoneNumber::normalize($contact['phone']) === $primaryPhone)
            ->unique(fn (array $contact): string => PhoneNumber::normalize($contact['phone']))
            ->take(5)
            ->values();

        $donor->contacts()->delete();
        $donor->contacts()->createMany($contacts->all());
        $donor->load('contacts');

        ActivityLog::record(
            $wasApproved ? 'donor.updated' : 'donor.applied',
            $donor,
            $wasApproved ? 'ডোনার নিজের তথ্য আপডেট করেছেন।' : 'নতুন ডোনার আবেদন জমা হয়েছে।',
            $user,
        );

        if (! $wasApproved) {
            $mailer->send(
                $user,
                $user->email,
                'application',
                'ডোনার আবেদন গ্রহণ করা হয়েছে',
                'আপনার আবেদন আমরা পেয়েছি',
                [
                    'আমাদের টিম আপনার তথ্য যাচাই করছে। সাধারণত ২৪–৪৮ ঘণ্টার মধ্যে সিদ্ধান্ত জানানো হয়।',
                    'যাচাইয়ের জন্য আমরা আপনার নম্বরে কল করতে পারি, তাই ফোন খোলা রাখুন।',
                ],
                route('dashboard'),
                'আবেদনের অবস্থা দেখুন',
            );

            User::query()
                ->whereIn('role', [UserRole::Admin, UserRole::Manager])
                ->where('is_active', true)
                ->take(10)
                ->get()
                ->each(fn (User $staff) => $mailer->send(
                    $staff,
                    $staff->email,
                    'application',
                    'নতুন ডোনার আবেদন',
                    'নতুন ডোনার আবেদন পর্যালোচনা প্রয়োজন',
                    [
                        'আবেদনকারী: '.$user->name.' (@'.$user->username.')',
                        'রক্তের গ্রুপ: '.$donor->blood_group,
                        'এলাকা: '.$donor->locationLabel(),
                    ],
                    route('admin.donors.show', $donor),
                    'আবেদন দেখুন',
                ));
        }

        return redirect()->route('dashboard')
            ->with('success', $wasApproved
                ? 'আপনার ডোনার তথ্য আপডেট হয়েছে।'
                : 'আপনার ডোনার আবেদন জমা হয়েছে। যাচাই শেষে আপনার প্রোফাইল লিস্টে দেখা যাবে।');
    }
}
