<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use App\Models\Donation;
use App\Models\PhoneReveal;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load([
            'donor.district',
            'donor.upazila',
            'donor.donations' => fn ($query) => $query->latest('donated_at')->limit(10),
        ]);

        $donor = $user->donor;

        return view('dashboard.index', [
            'user' => $user,
            'donor' => $donor,
            'revealedToday' => PhoneReveal::query()
                ->where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfDay())
                ->count(),
            'revealLimit' => Setting::dailyRevealLimit(),
            'messages' => DirectMessage::query()
                ->where('recipient_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
            'ratings' => $donor
                ? $donor->ratings()->published()->with('user')->latest()->take(5)->get()
                : collect(),
        ]);
    }

    public function storeDonation(Request $request): RedirectResponse
    {
        $donor = $request->user()->donor;

        abort_unless($donor?->isApproved(), 403, 'শুধুমাত্র ভেরিফাইড ডোনার রক্তদানের তথ্য যোগ করতে পারবেন।');

        $data = $request->validate([
            'donated_at' => ['required', 'date', 'before_or_equal:today'],
            'hospital' => ['nullable', 'string', 'max:160'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $donatedAt = Carbon::parse($data['donated_at']);

        if ($donor->last_donation_date && $donatedAt->lt($donor->last_donation_date)) {
            return back()->withErrors([
                'donated_at' => 'নতুন রক্তদানের তারিখ সর্বশেষ রক্তদানের ('.$donor->last_donation_date->format('d M Y').') চেয়ে আগের হতে পারবে না।',
            ]);
        }

        Donation::query()->create([
            'donor_id' => $donor->id,
            'donated_at' => $donatedAt,
            'hospital' => $data['hospital'] ?? null,
            'note' => $data['note'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $donor->forceFill([
            'last_donation_date' => $donatedAt,
            'total_donations' => $donor->donations()->count(),
        ])->save();

        return back()->with('success', 'রক্তদানের তথ্য যোগ হয়েছে। আগামী ৪ মাস আপনি "অনুপলব্ধ" হিসেবে দেখাবেন।');
    }
}
