<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\PhoneNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['login']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'login' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($throttleKey)]),
            ]);
        }

        $login = trim($credentials['login']);
        $field = match (true) {
            filter_var($login, FILTER_VALIDATE_EMAIL) !== false => 'email',
            preg_match('/^\+?[0-9][0-9\s\-()]{6,19}$/', $login) === 1 => 'phone',
            default => 'username',
        };

        if ($field === 'phone') {
            $login = PhoneNumber::normalize($login);
        }

        if (! Auth::attempt([$field => $login, 'password' => $credentials['password']], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->is_active || $user->banned_at !== null) {
            Auth::logout();

            throw ValidationException::withMessages([
                'login' => 'আপনার অ্যাকাউন্টটি বন্ধ রয়েছে। সাপোর্টে যোগাযোগ করুন।',
            ]);
        }

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        if ($user->needsPhoneVerification()) {
            return redirect()->route('verification.notice');
        }

        if (! $user->isStaff() && $user->needsEmailVerification()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'লগইন সফল হয়েছে। এখন আপনার ইমেইল ঠিকানা ভেরিফাই করুন।');
        }

        return redirect()->intended($user->isStaff() ? route('admin.dashboard') : route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
