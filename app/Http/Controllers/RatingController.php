<?php

namespace App\Http\Controllers;

use App\Enums\ReviewStatus;
use App\Models\Donor;
use App\Models\Rating;
use App\Services\MailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, string $username, MailService $mailer): RedirectResponse
    {
        $donor = Donor::query()
            ->approved()
            ->with('user')
            ->whereHas('user', fn ($query) => $query->where('username', $username))
            ->firstOrFail();

        abort_if($donor->user_id === $request->user()->id, 403, 'নিজের প্রোফাইলে রেটিং দেওয়া যাবে না।');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:600'],
        ]);

        $rating = Rating::query()->updateOrCreate(
            ['donor_id' => $donor->id, 'user_id' => $request->user()->id],
            $data + ['status' => ReviewStatus::Published],
        );

        $donor->refreshRatingSummary();

        $mailer->send(
            $donor->user,
            $donor->user->email,
            'rating',
            'আপনার প্রোফাইলে নতুন রেটিং এসেছে',
            'নতুন রেটিং: '.$rating->rating.' / ৫',
            [
                ($request->user()->name ?? 'একজন ইউজার').' আপনার ডোনার প্রোফাইলে রেটিং দিয়েছেন।',
                $rating->comment ? 'মন্তব্য: '.$rating->comment : 'মন্তব্য ছাড়া রেটিং।',
            ],
            $donor->user->profileUrl(),
            'প্রোফাইল দেখুন',
        );

        return back()->with('success', 'ধন্যবাদ! আপনার রেটিং যোগ হয়েছে।');
    }
}
