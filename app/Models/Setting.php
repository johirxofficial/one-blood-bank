<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value', 'group', 'type'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /**
     * Canonical setting keys and their fallbacks. Keeping defaults here means
     * the application works on a fresh database before the settings form is
     * opened for the first time.
     *
     * @return array<string, array{0: mixed, 1: string, 2: string}>
     */
    public static function defaults(): array
    {
        return [
            // Site information
            'site.name' => ['One Blood Bank', 'general', 'string'],
            'site.tagline' => ['রক্ত লাগবে? কাছের ভেরিফাইড ডোনার খুঁজুন।', 'general', 'string'],
            'site.contact_email' => ['support@thebloodbank.test', 'general', 'string'],
            'site.contact_phone' => ['01XXXXXXXXX', 'general', 'string'],
            'site.address' => ['ঢাকা, বাংলাদেশ', 'general', 'string'],
            'site.country' => ['Bangladesh', 'general', 'string'],
            'site.currency' => ['BDT', 'general', 'string'],

            // SEO
            'seo.title' => ['One Blood Bank — বাংলাদেশের ভেরিফাইড ব্লাড ডোনার ডাটাবেজ', 'seo', 'string'],
            'seo.description' => ['জেলা, উপজেলা ও ইউনিয়ন ভিত্তিক ভেরিফাইড ব্লাড ডোনার খুঁজুন। ফ্রি, দ্রুত ও নিরাপদ।', 'seo', 'string'],
            'seo.keywords' => ['blood donor bangladesh, রক্তদাতা, ব্লাড ডোনার, রক্ত লাগবে', 'seo', 'string'],
            'seo.og_image' => ['', 'seo', 'string'],

            // Appearance
            'appearance.primary_color' => ['#D32F2F', 'appearance', 'string'],
            'appearance.paper_color' => ['#f7f4ee', 'appearance', 'string'],
            'appearance.surface_color' => ['#fdfbf7', 'appearance', 'string'],
            'appearance.raised_color' => ['#efe9df', 'appearance', 'string'],
            'appearance.line_color' => ['#e4ddd0', 'appearance', 'string'],
            'appearance.ink_color' => ['#1f1b16', 'appearance', 'string'],
            'appearance.font_sans' => ['hind', 'appearance', 'string'],
            'appearance.font_display' => ['tiro', 'appearance', 'string'],
            'appearance.font_size' => ['16', 'appearance', 'integer'],
            'appearance.logo_height' => ['36', 'appearance', 'integer'],
            'appearance.layout' => ['classic', 'appearance', 'string'],
            'appearance.container_width' => ['normal', 'appearance', 'string'],
            'appearance.radius' => ['soft', 'appearance', 'string'],
            'appearance.section_spacing' => ['normal', 'appearance', 'string'],
            'appearance.button_style' => ['ink', 'appearance', 'string'],
            'appearance.card_style' => ['flat', 'appearance', 'string'],
            'appearance.sticky_header' => ['1', 'appearance', 'boolean'],
            'appearance.show_header_tagline' => ['1', 'appearance', 'boolean'],
            'appearance.show_header_cta' => ['1', 'appearance', 'boolean'],
            'appearance.show_contact_bar' => ['0', 'appearance', 'boolean'],
            'appearance.announcement_enabled' => ['0', 'appearance', 'boolean'],
            'appearance.announcement_text' => ['', 'appearance', 'string'],
            'appearance.announcement_url' => ['', 'appearance', 'string'],
            'appearance.hero_style' => ['split', 'appearance', 'string'],
            'appearance.show_hero_search' => ['1', 'appearance', 'boolean'],
            'appearance.show_hero_points' => ['1', 'appearance', 'boolean'],
            'appearance.show_stats' => ['1', 'appearance', 'boolean'],
            'appearance.show_urgent_requests' => ['1', 'appearance', 'boolean'],
            'appearance.show_steps' => ['1', 'appearance', 'boolean'],
            'appearance.show_community' => ['1', 'appearance', 'boolean'],
            'appearance.show_donor_card' => ['1', 'appearance', 'boolean'],
            'appearance.show_cta' => ['1', 'appearance', 'boolean'],
            'appearance.footer_style' => ['columns', 'appearance', 'string'],
            'appearance.show_footer_contact' => ['1', 'appearance', 'boolean'],
            'appearance.show_footer_districts' => ['1', 'appearance', 'boolean'],
            'appearance.show_footer_credit' => ['1', 'appearance', 'boolean'],
            'appearance.custom_css' => ['', 'appearance', 'text'],
            'appearance.logo_path' => ['', 'appearance', 'string'],
            'appearance.favicon_path' => ['', 'appearance', 'string'],

            // Security, registration and verification
            'security.require_phone_verification' => ['1', 'security', 'boolean'],
            'security.require_email_verification' => ['0', 'security', 'boolean'],
            'security.allow_registration' => ['1', 'security', 'boolean'],
            'security.show_social_login' => ['0', 'security', 'boolean'],

            // Donors
            'donors.require_login' => ['1', 'donors', 'boolean'],
            'donors.daily_reveal_limit' => ['15', 'donors', 'integer'],
            'donors.auto_approve' => ['0', 'donors', 'boolean'],

            // Ads
            'ads.enabled' => ['0', 'ads', 'boolean'],
            'ads.popunder_enabled' => ['0', 'ads', 'boolean'],
            'ads.popunder_code' => ['', 'ads', 'text'],

            // Mail notifications and SMTP
            'mail.driver' => ['log', 'mail', 'string'],
            'mail.host' => ['', 'mail', 'string'],
            'mail.port' => ['587', 'mail', 'integer'],
            'mail.username' => ['', 'mail', 'string'],
            'mail.password' => ['', 'mail', 'string'],
            'mail.encryption' => ['tls', 'mail', 'string'],
            'mail.from_address' => ['', 'mail', 'string'],
            'mail.from_name' => ['', 'mail', 'string'],
            'mail.welcome_enabled' => ['1', 'mail', 'boolean'],
            'mail.application_enabled' => ['1', 'mail', 'boolean'],
            'mail.status_enabled' => ['1', 'mail', 'boolean'],
            'mail.rating_enabled' => ['1', 'mail', 'boolean'],

            // SMS / OTP gateway
            'sms.enabled' => ['1', 'sms', 'boolean'],
            'sms.driver' => ['log', 'sms', 'string'],
            'sms.gateway_url' => ['', 'sms', 'string'],
            'sms.api_key' => ['', 'sms', 'string'],
            'sms.sender_id' => ['', 'sms', 'string'],
            'sms.otp_template' => ['{site}: আপনার যাচাইকরণ কোড {code}। {minutes} মিনিটের মধ্যে ব্যবহার করুন।', 'sms', 'text'],

            // Optional site donation
            'donation.enabled' => ['1', 'donation', 'boolean'],
            'donation.currency' => ['BDT', 'donation', 'string'],
            'donation.min_amount' => ['50', 'donation', 'integer'],
            'donation.goal_amount' => ['0', 'donation', 'integer'],
            'donation.show_goal' => ['1', 'donation', 'boolean'],
            'donation.bkash_number' => ['', 'donation', 'string'],
            'donation.nagad_number' => ['', 'donation', 'string'],
            'donation.rocket_number' => ['', 'donation', 'string'],
            'donation.bank_details' => ['', 'donation', 'text'],
            'donation.instructions' => ['টাকা পাঠানোর পর ট্রানজেকশন আইডি দিয়ে ফর্মটি পূরণ করুন। যাচাইয়ের পর আপনার নাম ধন্যবাদ তালিকায় যুক্ত হবে।', 'donation', 'text'],
            'donation.thank_you' => ['আপনার সহযোগিতার জন্য অসংখ্য ধন্যবাদ!', 'donation', 'text'],

            // Blog and miscellaneous
            'others.blog_enabled' => ['1', 'others', 'boolean'],
            'others.footer_text' => ['', 'others', 'text'],
            'others.analytics_code' => ['', 'others', 'text'],
            'others.timezone' => ['Asia/Dhaka', 'others', 'string'],
            'others.locale' => ['bn', 'others', 'string'],
            'others.location_country_code' => ['BD', 'others', 'string'],
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = Cache::rememberForever(
            "settings.{$key}",
            fn () => static::query()->where('key', $key)->first(['value', 'type'])?->only(['value', 'type']),
        );

        if ($row === null || $row['value'] === null) {
            $fallback = static::defaults()[$key][0] ?? null;

            return $default ?? $fallback;
        }

        return match ($row['type']) {
            'boolean' => filter_var($row['value'], FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $row['value'],
            'json' => json_decode($row['value'], true),
            default => $row['value'],
        };
    }

    public static function put(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type],
        );

        Cache::forget("settings.{$key}");
    }

    /**
     * Apply administrator supplied SMTP credentials to Laravel's mail config.
     * This allows changing mail settings without editing .env on cPanel.
     */
    public static function applyMailConfig(): void
    {
        if (static::get('mail.driver', 'log') !== 'smtp') {
            return;
        }

        $host = (string) static::get('mail.host', '');

        if (blank($host)) {
            return;
        }

        $fromAddress = (string) static::get('mail.from_address', '');
        $encryption = (string) static::get('mail.encryption', 'tls');

        // Laravel's SMTP transport understands tls/ssl; STARTTLS is negotiated via tls.
        if ($encryption === 'starttls') {
            $encryption = 'tls';
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) static::get('mail.port', 587),
            'mail.mailers.smtp.username' => (string) static::get('mail.username', ''),
            'mail.mailers.smtp.password' => (string) static::get('mail.password', ''),
            'mail.mailers.smtp.encryption' => $encryption ?: null,
            'mail.from.address' => $fromAddress ?: config('mail.from.address'),
            'mail.from.name' => (string) static::get('mail.from_name', '') ?: config('mail.from.name'),
        ]);
    }

    public static function phoneVerificationRequired(): bool
    {
        return (bool) static::get('security.require_phone_verification', true);
    }

    public static function emailVerificationRequired(): bool
    {
        return (bool) static::get('security.require_email_verification', false);
    }

    /**
     * A zero limit means unlimited phone reveals.
     */
    public static function dailyRevealLimit(): int
    {
        return max(0, (int) static::get('donors.daily_reveal_limit', 15));
    }
}
