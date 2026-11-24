<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\PhoneReveal;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneRevealController extends Controller
{
    public function __invoke(Request $request, string $username): JsonResponse
    {
        $donor = Donor::query()
            ->approved()
            ->with(['user', 'contacts'])
            ->whereHas('user', fn ($query) => $query->where('username', $username))
            ->firstOrFail();

        $user = $request->user();
        $limit = Setting::dailyRevealLimit();

        $revealedToday = PhoneReveal::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        $alreadyRevealed = PhoneReveal::query()
            ->where('user_id', $user->id)
            ->where('donor_id', $donor->id)
            ->where('created_at', '>=', now()->startOfDay())
            ->exists();

        // A limit of zero means the administrator allows unlimited reveals.
        if ($limit > 0 && ! $alreadyRevealed && $revealedToday >= $limit) {
            return response()->json([
                'message' => "আজকের জন্য {$limit} টি নম্বর দেখার সীমা শেষ হয়েছে। আগামীকাল আবার চেষ্টা করুন।",
            ], 429);
        }

        if (! $alreadyRevealed) {
            PhoneReveal::query()->create([
                'user_id' => $user->id,
                'donor_id' => $donor->id,
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        $remaining = $limit === 0
            ? null
            : max(0, $limit - $revealedToday - ($alreadyRevealed ? 0 : 1));

        $phones = collect([[
            'label' => 'Primary',
            'phone' => $donor->user->phone,
        ]])->merge($donor->contacts->map(fn ($contact): array => [
            'label' => $contact->displayLabel(),
            'phone' => $contact->phone,
        ]))
            ->filter(fn (array $phone): bool => filled($phone['phone']))
            ->unique(fn (array $phone): string => preg_replace('/\D+/', '', (string) $phone['phone']) ?? '')
            ->values();

        return response()->json([
            'name' => $donor->user->name,
            'phone' => $donor->user->phone,
            'phones' => $phones,
            'remaining' => $remaining,
        ]);
    }
}
