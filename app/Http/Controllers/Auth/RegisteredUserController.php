<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\VerificationPurpose;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Rules\ReservedUsername;
use App\Services\MailService;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        abort_unless((bool) Setting::get('security.allow_registration', true), 403, 'নতুন রেজিস্ট্রেশন বর্তমানে বন্ধ রয়েছে।');

        return view('auth.register');
    }

    public function store(Request $request, OtpService $otp, MailService $mailer): RedirectResponse
    {
        abort_unless((bool) Setting::get('security.allow_registration', true), 403, 'নতুন রেজিস্ট্রেশন বর্তমানে বন্ধ রয়েছে।');

        $request->merge(['phone' => PhoneNumber::normalize($request->input('phone'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'lowercase', 'unique:users,username', new ReservedUsername],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', new PhoneNumber, 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms' => ['accepted'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'role' => UserRole::User,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $phoneRequired = Setting::phoneVerificationRequired();
        $code = $phoneRequired ? $otp->send($user->phone, VerificationPurpose::Register) : null;

        $mailer->send(
            $user,
            $user->email,
            'welcome',
            Setting::get('site.name', 'One Blood Bank').'-এ স্বাগতম',
            'স্বাগতম, '.$user->name.'!',
            [
                'আপনার অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে।',
                $phoneRequired
                    ? 'মোবাইল নম্বর ভেরিফাই করলেই আপনি ভেরিফাইড ডোনারদের খুঁজে পাবেন।'
                    : 'এখনই ডোনারদের তালিকা দেখতে পারবেন।',
                'ডোনার হতে চাইলে ড্যাশবোর্ড থেকে আবেদন করুন — যাচাইয়ের পর আপনার প্রোফাইল লিস্টে দেখা যাবে।',
            ],
            route('dashboard'),
            'ড্যাশবোর্ডে যান',
        );

        if ($phoneRequired) {
            return redirect()->route('verification.notice')
                ->with('status', 'আপনার মোবাইল নম্বরে একটি OTP পাঠানো হয়েছে।')
                ->with('dev_otp', app()->isLocal() ? $code : null);
        }

        return redirect()->route('dashboard')
            ->with('success', 'অ্যাকাউন্ট তৈরি হয়েছে। স্বাগতম!');
    }
}
