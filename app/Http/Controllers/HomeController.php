<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Division;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $stats = [
            'donors' => Donor::query()->approved()->count(),
            'districts' => Donor::query()->approved()->whereNotNull('district_id')->distinct()->count('district_id'),
            'donations' => (int) Donor::query()->approved()->sum('total_donations'),
            'members' => User::query()->count(),
        ];

        $requiresLogin = (bool) Setting::get('donors.require_login', true);
        $user = $request->user();
        $canViewDonors = ! $requiresLogin || $user !== null;

        if ($canViewDonors && $user !== null && Setting::phoneVerificationRequired()) {
            $canViewDonors = $user->phone_verified_at !== null;
        }

        $donors = $canViewDonors
            ? Donor::query()
                ->approved()
                ->with(['user', 'district', 'upazila'])
                ->orderForDirectory('rating')
                ->take(6)
                ->get()
            : collect();

        $urgentRequests = BloodRequest::query()
            ->open()
            ->with(['district', 'upazila'])
            ->orderBy('needed_at')
            ->take(4)
            ->get();

        return view('home', [
            'stats' => $stats,
            'donors' => $donors,
            'canViewDonors' => $canViewDonors,
            'divisions' => Division::query()->forCountry()->orderBy('name')->get(),
            'urgentRequests' => $urgentRequests,
            'donationsCount' => Donation::query()->count(),
        ]);
    }
}
