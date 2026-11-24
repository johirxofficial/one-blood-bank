<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && Setting::phoneVerificationRequired() && $user->phone_verified_at === null) {
            return redirect()->route('verification.notice')
                ->with('warning', 'ডোনার লিস্ট দেখার আগে আপনার মোবাইল নম্বর ভেরিফাই করুন।');
        }

        if ($user && ! $user->isStaff() && Setting::emailVerificationRequired() && ! $user->hasVerifiedEmail()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'ডোনার লিস্ট দেখার আগে আপনার ইমেইল ঠিকানা ভেরিফাই করুন।');
        }

        return $next($request);
    }
}
