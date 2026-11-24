<?php

namespace App\Services;

use App\Mail\SiteMail;
use App\Models\MailLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailService
{
    /**
     * Send a templated notification and keep a delivery log for the admin portal.
     *
     * @param  array<int, string>  $lines
     */
    public function send(
        ?User $user,
        string $toEmail,
        string $template,
        string $subject,
        string $heading,
        array $lines = [],
        ?string $actionUrl = null,
        ?string $actionLabel = null,
    ): bool {
        if (! $this->templateEnabled($template)) {
            return false;
        }

        if (blank($toEmail)) {
            return false;
        }

        $log = MailLog::query()->create([
            'user_id' => $user?->id,
            'to_email' => $toEmail,
            'subject' => $subject,
            'template' => $template,
            'status' => 'queued',
        ]);

        try {
            Setting::applyMailConfig();

            Mail::to($toEmail)->send(new SiteMail(
                subjectLine: $subject,
                heading: $heading,
                lines: $lines,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                footerNote: Setting::get('site.name'),
            ));

            $log->forceFill(['status' => 'sent', 'sent_at' => now()])->save();

            return true;
        } catch (Throwable $exception) {
            report($exception);

            $log->forceFill(['status' => 'failed', 'error' => $exception->getMessage()])->save();

            return false;
        }
    }

    private function templateEnabled(string $template): bool
    {
        $key = match ($template) {
            'welcome' => 'mail.welcome_enabled',
            'application' => 'mail.application_enabled',
            'status' => 'mail.status_enabled',
            'rating' => 'mail.rating_enabled',
            default => null,
        };

        return $key === null || (bool) Setting::get($key, true);
    }
}
