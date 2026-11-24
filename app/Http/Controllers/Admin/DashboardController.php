<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DonorStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\MailLog;
use App\Models\Post;
use App\Models\Rating;
use App\Models\SiteDonation;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'donors' => Donor::query()->approved()->count(),
                'pending' => Donor::query()->where('status', DonorStatus::Pending)->count(),
                'requests' => BloodRequest::query()->open()->count(),
                'ratings' => Rating::query()->count(),
                'mails' => MailLog::query()->whereDate('created_at', today())->count(),
                'posts' => Post::query()->count(),
                'pending_donations' => SiteDonation::query()->pending()->count(),
                'donation_amount' => (float) SiteDonation::query()->verified()->sum('amount'),
            ],
            'pendingDonors' => Donor::query()
                ->where('status', DonorStatus::Pending)
                ->with(['user', 'district', 'upazila'])
                ->oldest()
                ->take(8)
                ->get(),
            'latestDonors' => Donor::query()
                ->approved()
                ->with(['user', 'district'])
                ->latest('verified_at')
                ->take(6)
                ->get(),
            'recentActivity' => ActivityLog::query()->with('user')->latest('created_at')->take(10)->get(),
            'latestRatings' => Rating::query()->with(['donor.user', 'user'])->latest()->take(5)->get(),
            'latestSiteDonations' => SiteDonation::query()->latest()->take(5)->get(),
        ]);
    }
}
