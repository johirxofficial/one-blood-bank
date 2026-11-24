<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteDonation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteDonationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'in:pending,verified,rejected'],
        ]);

        $donations = SiteDonation::query()
            ->when(filled($filters['q'] ?? null), function ($query) use ($filters): void {
                $term = $filters['q'];
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('transaction_id', 'like', "%{$term}%");
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.site-donations.index', [
            'donations' => $donations,
            'filters' => $filters,
            'stats' => [
                'verified_amount' => (float) SiteDonation::query()->verified()->sum('amount'),
                'verified_count' => SiteDonation::query()->verified()->count(),
                'pending_count' => SiteDonation::query()->pending()->count(),
                'month_amount' => (float) SiteDonation::query()->verified()->where('verified_at', '>=', now()->startOfMonth())->sum('amount'),
            ],
        ]);
    }

    public function update(Request $request, SiteDonation $siteDonation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,verified,rejected'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $siteDonation->forceFill([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
            'verified_by' => $data['status'] === 'verified' ? $request->user()->id : null,
            'verified_at' => $data['status'] === 'verified' ? ($siteDonation->verified_at ?? now()) : null,
        ])->save();

        ActivityLog::record('donation.status', $siteDonation, 'সাইট ডোনেশন '.$data['status'].': '.$siteDonation->name);

        return back()->with('success', 'ডোনেশন স্ট্যাটাস আপডেট হয়েছে।');
    }

    public function destroy(SiteDonation $siteDonation): RedirectResponse
    {
        ActivityLog::record('donation.deleted', null, 'সাইট ডোনেশন মুছে ফেলা: '.$siteDonation->name);

        $siteDonation->delete();

        return back()->with('success', 'ডোনেশন রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
