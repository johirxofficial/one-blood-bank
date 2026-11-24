<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Rating;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(ReviewStatus::class)],
        ]);

        $ratings = Rating::query()
            ->with(['donor.user', 'user'])
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ratings.index', compact('ratings', 'filters'));
    }

    public function update(Request $request, Rating $rating): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(ReviewStatus::class)],
        ]);

        $rating->forceFill(['status' => $data['status']])->save();
        $rating->donor?->refreshRatingSummary();

        ActivityLog::record('rating.moderated', $rating, 'রেটিং স্ট্যাটাস: '.$data['status']);

        return back()->with('success', 'রেটিং আপডেট হয়েছে।');
    }

    public function destroy(Rating $rating): RedirectResponse
    {
        $donor = $rating->donor;
        $rating->delete();
        $donor?->refreshRatingSummary();

        ActivityLog::record('rating.deleted', $donor, 'একটি রেটিং মুছে ফেলা হয়েছে।');

        return back()->with('success', 'রেটিং মুছে ফেলা হয়েছে।');
    }
}
