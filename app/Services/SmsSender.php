<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsSender
{
    /**
     * Deliver an SMS through the configured gateway.
     *
     * Supported drivers:
     *  - log: writes the message to storage/logs/laravel.log (local testing)
     *  - http: calls sms.gateway_url with {phone}, {message}, {api_key} and
     *          {sender_id} placeholders replaced. This works with most
     *          country-specific SMS providers without code changes.
     */
    public function send(string $phone, string $message): void
    {
        $driver = (string) Setting::get('sms.driver', 'log');
        $enabled = (bool) Setting::get('sms.enabled', true);

        if (! $enabled) {
            Log::info("SMS disabled. Message to {$phone}: {$message}");

            return;
        }

        if ($driver === 'log' || $driver === '') {
            Log::info("SMS to {$phone}: {$message}");

            return;
        }

        $url = (string) Setting::get('sms.gateway_url', '');

        if (blank($url)) {
            Log::warning("SMS gateway URL is empty. Message to {$phone}: {$message}");

            return;
        }

        $url = str_replace(
            ['{phone}', '{message}', '{api_key}', '{sender_id}'],
            [urlencode($phone), urlencode($message), urlencode((string) Setting::get('sms.api_key', '')), urlencode((string) Setting::get('sms.sender_id', ''))],
            $url,
        );

        try {
            $response = Http::timeout(15)->get($url);

            if ($response->failed()) {
                Log::warning('SMS gateway rejected message', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (Throwable $exception) {
            report($exception);
            Log::warning("SMS delivery failed for {$phone}: {$exception->getMessage()}");
        }
    }
}
