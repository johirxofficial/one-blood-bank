<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify.email',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );
    }

    public function toMail($notifiable): MailMessage
    {
        $siteName = (string) Setting::get('site.name', config('app.name'));

        return (new MailMessage)
            ->subject($siteName.' — ইমেইল যাচাই করুন')
            ->greeting('স্বাগতম, '.$notifiable->name.'!')
            ->line('আপনার অ্যাকাউন্টের ইমেইল ঠিকানা যাচাই করতে নিচের বাটনে ক্লিক করুন।')
            ->action('ইমেইল যাচাই করুন', $this->verificationUrl($notifiable))
            ->line('লিংকটির মেয়াদ ৬০ মিনিট। আপনি এই অনুরোধ না করে থাকলে এটি উপেক্ষা করুন।')
            ->salutation($siteName);
    }
}
