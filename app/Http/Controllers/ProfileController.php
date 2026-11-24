<?php

namespace App\Http\Controllers;

use App\Enums\VerificationPurpose;
use App\Models\Setting;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Services\MailService;
use App\Services\OtpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, MailService $mailer, OtpService $otp): RedirectResponse
    {
        $request->merge(['phone' => PhoneNumber::normalize($request->input('phone'))]);

        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', new PhoneNumber, Rule::unique('users', 'phone')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $emailChanged = strtolower($data['email']) !== strtolower((string) $user->email);
        $phoneChanged = $data['phone'] !== (string) $user->phone;

        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        if ($emailChanged) {
            $attributes['email_verified_at'] = null;
        }

        if ($phoneChanged) {
            $attributes['phone_verified_at'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $attributes['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $attributes['avatar_path'] = null;
        }

        $user->forceFill($attributes)->save();

        if ($emailChanged && Setting::emailVerificationRequired()) {
            $this->sendVerificationLink($user, $mailer);
        }

        if ($phoneChanged && Setting::phoneVerificationRequired()) {
            $code = $otp->send($user->phone, VerificationPurpose::Register);

            return redirect()->route('verification.notice')
                ->with('status', 'মোবাইল নম্বর পরিবর্তন হয়েছে। নতুন নম্বরটি যাচাই করতে OTP পাঠানো হয়েছে।')
                ->with('dev_otp', app()->isLocal() ? $code : null);
        }

        return redirect()->route('profile.edit')->with('success', 'প্রোফাইল আপডেট হয়েছে।');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $request->user()->forceFill(['password' => $data['password']])->save();

        return redirect()->route('profile.edit')->with('success', 'পাসওয়ার্ড পরিবর্তন হয়েছে।');
    }

    private function sendVerificationLink(User $user, MailService $mailer): void
    {
        $url = URL::temporarySignedRoute(
            'verification.verify.email',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())],
        );

        $mailer->send(
            $user,
            $user->email,
            'verification',
            'আপনার ইমেইল যাচাই করুন',
            'ইমেইল ঠিকানা যাচাই করুন',
            [
                'আপনার অ্যাকাউন্টের ইমেইল ঠিকানা পরিবর্তন করা হয়েছে।',
                'নিচের বাটনে ক্লিক করে নতুন ইমেইল ঠিকানাটি যাচাই করুন।',
            ],
            $url,
            'ইমেইল যাচাই করুন',
        );
    }
}
