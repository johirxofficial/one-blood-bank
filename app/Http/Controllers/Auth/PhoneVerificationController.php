<?php

namespace App\Http\Controllers\Auth;

use App\Enums\VerificationPurpose;
use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->phone_verified_at !== null) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-phone');
    }

    public function verify(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $otp->verify($user->phone, (string) $request->input('code'), VerificationPurpose::Register)) {
            return back()->withErrors(['code' => 'কোডটি সঠিক নয় অথবা মেয়াদ শেষ হয়ে গেছে।']);
        }

        $user->forceFill(['phone_verified_at' => now()])->save();

        return redirect()->route('dashboard')
            ->with('success', 'মোবাইল নম্বর সফলভাবে ভেরিফাই হয়েছে। এখন ডোনার খুঁজে পাবেন।');
    }

    public function resend(Request $request, OtpService $otp): RedirectResponse
    {
        $user = $request->user();

        if ($user->phone_verified_at !== null) {
            return redirect()->route('dashboard');
        }

        $code = $otp->send($user->phone, VerificationPurpose::Register);

        return back()
            ->with('status', 'নতুন OTP পাঠানো হয়েছে।')
            ->with('dev_otp', app()->isLocal() ? $code : null);
    }
}
