<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SiteDonation;
use App\Rules\PhoneNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless((bool) Setting::get('donation.enabled', true), 404);

        $verified = SiteDonation::query()->verified();

        $methods = collect([
            'bkash' => Setting::get('donation.bkash_number'),
            'nagad' => Setting::get('donation.nagad_number'),
            'rocket' => Setting::get('donation.rocket_number'),
            'bank' => null,
        ])->filter(fn ($number, $method) => $method === 'bank'
            ? filled(Setting::get('donation.bank_details'))
            : filled($number));

        return view('donate.index', [
            'currency' => (string) Setting::get('donation.currency', 'BDT'),
            'minimum' => max(1, (int) Setting::get('donation.min_amount', 50)),
            'goal' => (int) Setting::get('donation.goal_amount', 0),
            'showGoal' => (bool) Setting::get('donation.show_goal', true),
            'raised' => (float) (clone $verified)->sum('amount'),
            'donorsCount' => (clone $verified)->count(),
            'recentDonors' => (clone $verified)->latest('verified_at')->take(12)->get(['name', 'amount', 'message', 'verified_at']),
            'methods' => $methods,
            'methodLabels' => SiteDonation::methods(),
            'bankDetails' => (string) Setting::get('donation.bank_details', ''),
            'instructions' => (string) Setting::get('donation.instructions', ''),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless((bool) Setting::get('donation.enabled', true), 404);

        if (filled($request->input('phone'))) {
            $request->merge(['phone' => PhoneNumber::normalize($request->input('phone'))]);
        }

        $minimum = max(1, (int) Setting::get('donation.min_amount', 50));

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['nullable', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30', new PhoneNumber],
            'amount' => ['required', 'numeric', 'min:'.$minimum, 'max:10000000'],
            'method' => ['required', 'in:bkash,nagad,rocket,bank,other'],
            'transaction_id' => ['required', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.min' => 'সর্বনিম্ন '.$minimum.' '.Setting::get('donation.currency', 'BDT').' পরিমাণ দিন।',
        ]);

        SiteDonation::query()->create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'amount' => $data['amount'],
            'currency' => (string) Setting::get('donation.currency', 'BDT'),
            'method' => $data['method'],
            'transaction_id' => $data['transaction_id'],
            'status' => 'pending',
            'message' => $data['message'] ?? null,
        ]);

        return redirect()->route('donate.index')
            ->with('success', (string) Setting::get('donation.thank_you', 'আপনার সহযোগিতার জন্য অসংখ্য ধন্যবাদ!'))
            ->with('status', 'আপনার তথ্য জমা হয়েছে। অ্যাডমিন যাচাই করার পর আপনার নাম ধন্যবাদ তালিকায় যুক্ত হবে।');
    }
}
