<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DonorStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Donor;
use App\Services\MailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected,suspended'],
            'q' => ['nullable', 'string', 'max:60'],
        ]);

        $donors = Donor::query()
            ->with(['user', 'district', 'upazila', 'verifiedBy'])
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when(filled($filters['q'] ?? null), fn ($query) => $query->search($filters['q']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.donors.index', compact('donors', 'filters'));
    }

    public function show(Donor $donor): View
    {
        $donor->load([
            'user', 'division', 'district', 'upazila', 'union', 'verifiedBy', 'contacts',
            'donations' => fn ($query) => $query->latest('donated_at'),
            'ratings' => fn ($query) => $query->with('user')->latest(),
            'phoneReveals' => fn ($query) => $query->with('user')->latest('created_at')->limit(10),
        ]);

        return view('admin.donors.show', compact('donor'));
    }

    public function approve(Request $request, Donor $donor, MailService $mailer): RedirectResponse
    {
        $donor->approve($request->user());

        ActivityLog::record('donor.approved', $donor, 'ডোনার অনুমোদন করা হয়েছে: @'.$donor->user->username);

        $mailer->send(
            $donor->user,
            $donor->user->email,
            'status',
            'আপনি এখন ভেরিফাইড ডোনার',
            'অভিনন্দন! আপনার ডোনার আবেদন অনুমোদিত হয়েছে',
            [
                'এখন থেকে রক্তের প্রয়োজনে অন্য ইউজাররা আপনাকে খুঁজে পাবেন।',
                'আপনার ডিজিটাল ডোনার কার্ড ডাউনলোড করতে ড্যাশবোর্ডে যান।',
                'রক্তদানের পর তারিখ আপডেট করতে ভুলবেন না — এতে ৪ মাস আপনি অনুপলব্ধ থাকবেন।',
            ],
            route('dashboard'),
            'ড্যাশবোর্ডে যান',
        );

        return back()->with('success', 'ডোনার অনুমোদন করা হয়েছে।');
    }

    public function reject(Request $request, Donor $donor, MailService $mailer): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $donor->reject($request->user(), $data['reason']);

        ActivityLog::record('donor.rejected', $donor, 'ডোনার বাতিল: '.$data['reason']);

        $mailer->send(
            $donor->user,
            $donor->user->email,
            'status',
            'আপনার ডোনার আবেদন বাতিল হয়েছে',
            'আবেদনটি অনুমোদন করা যায়নি',
            [
                'কারণ: '.$data['reason'],
                'তথ্য ঠিক করে আবার আবেদন করতে পারেন।',
            ],
            route('donor.apply'),
            'আবার আবেদন করুন',
        );

        return back()->with('success', 'আবেদন বাতিল করা হয়েছে।');
    }

    public function suspend(Donor $donor): RedirectResponse
    {
        $donor->forceFill(['status' => DonorStatus::Suspended, 'is_public' => false])->save();

        ActivityLog::record('donor.suspended', $donor, 'ডোনার স্থগিত করা হয়েছে: @'.$donor->user->username);

        return back()->with('success', 'ডোনার অ্যাকাউন্ট স্থগিত করা হয়েছে।');
    }

    public function destroy(Donor $donor): RedirectResponse
    {
        ActivityLog::record('donor.deleted', $donor, 'ডোনার প্রোফাইল মুছে ফেলা হয়েছে: @'.$donor->user->username);

        $donor->delete();

        return redirect()->route('admin.donors.index')->with('success', 'ডোনার প্রোফাইল মুছে ফেলা হয়েছে।');
    }
}
