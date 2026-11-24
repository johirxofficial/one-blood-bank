<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->getKey() === (int) $id, 403);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403, 'ভেরিফিকেশন লিংকটি সঠিক নয়।');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('status', 'ইমেইল আগেই ভেরিফাই করা হয়েছে।');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('dashboard')->with('success', 'ইমেইল সফলভাবে ভেরিফাই হয়েছে।');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('status', 'আপনার ইমেইল আগেই ভেরিফাই করা হয়েছে।');
        }

        if (! Setting::emailVerificationRequired()) {
            return back()->with('status', 'ইমেইল ভেরিফিকেশন বর্তমানে ঐচ্ছিক।');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'নতুন ভেরিফিকেশন লিংক ইমেইলে পাঠানো হয়েছে।');
    }
}
