<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Support\LocationCountry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * Editable text values, grouped by the settings tab they belong to.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const TEXT_SETTINGS = [
        'site.name' => ['general', 'string'],
        'site.tagline' => ['general', 'string'],
        'site.contact_email' => ['general', 'string'],
        'site.contact_phone' => ['general', 'string'],
        'site.address' => ['general', 'string'],
        'site.country' => ['general', 'string'],
        'site.currency' => ['general', 'string'],
        'seo.title' => ['seo', 'string'],
        'seo.description' => ['seo', 'text'],
        'seo.keywords' => ['seo', 'string'],
        'appearance.primary_color' => ['appearance', 'string'],
        'appearance.paper_color' => ['appearance', 'string'],
        'appearance.surface_color' => ['appearance', 'string'],
        'appearance.raised_color' => ['appearance', 'string'],
        'appearance.line_color' => ['appearance', 'string'],
        'appearance.ink_color' => ['appearance', 'string'],
        'appearance.font_sans' => ['appearance', 'string'],
        'appearance.font_display' => ['appearance', 'string'],
        'appearance.font_size' => ['appearance', 'integer'],
        'appearance.logo_height' => ['appearance', 'integer'],
        'appearance.layout' => ['appearance', 'string'],
        'appearance.container_width' => ['appearance', 'string'],
        'appearance.radius' => ['appearance', 'string'],
        'appearance.section_spacing' => ['appearance', 'string'],
        'appearance.button_style' => ['appearance', 'string'],
        'appearance.card_style' => ['appearance', 'string'],
        'appearance.announcement_text' => ['appearance', 'string'],
        'appearance.announcement_url' => ['appearance', 'string'],
        'appearance.hero_style' => ['appearance', 'string'],
        'appearance.footer_style' => ['appearance', 'string'],
        'appearance.custom_css' => ['appearance', 'text'],
        'ads.popunder_code' => ['ads', 'text'],
        'donors.daily_reveal_limit' => ['donors', 'integer'],
        'mail.driver' => ['mail', 'string'],
        'mail.host' => ['mail', 'string'],
        'mail.port' => ['mail', 'integer'],
        'mail.username' => ['mail', 'string'],
        'mail.password' => ['mail', 'string'],
        'mail.encryption' => ['mail', 'string'],
        'mail.from_address' => ['mail', 'string'],
        'mail.from_name' => ['mail', 'string'],
        'sms.driver' => ['sms', 'string'],
        'sms.gateway_url' => ['sms', 'string'],
        'sms.api_key' => ['sms', 'string'],
        'sms.sender_id' => ['sms', 'string'],
        'sms.otp_template' => ['sms', 'text'],
        'donation.currency' => ['donation', 'string'],
        'donation.min_amount' => ['donation', 'integer'],
        'donation.goal_amount' => ['donation', 'integer'],
        'donation.bkash_number' => ['donation', 'string'],
        'donation.nagad_number' => ['donation', 'string'],
        'donation.rocket_number' => ['donation', 'string'],
        'donation.bank_details' => ['donation', 'text'],
        'donation.instructions' => ['donation', 'text'],
        'donation.thank_you' => ['donation', 'text'],
        'others.footer_text' => ['others', 'text'],
        'others.analytics_code' => ['others', 'text'],
        'others.timezone' => ['others', 'string'],
        'others.locale' => ['others', 'string'],
        'others.location_country_code' => ['others', 'string'],
    ];

    /**
     * @var array<int, array{key: string, group: string}>
     */
    private const BOOLEAN_SETTINGS = [
        ['key' => 'appearance.sticky_header', 'group' => 'appearance'],
        ['key' => 'appearance.show_header_tagline', 'group' => 'appearance'],
        ['key' => 'appearance.show_header_cta', 'group' => 'appearance'],
        ['key' => 'appearance.show_contact_bar', 'group' => 'appearance'],
        ['key' => 'appearance.announcement_enabled', 'group' => 'appearance'],
        ['key' => 'appearance.show_hero_search', 'group' => 'appearance'],
        ['key' => 'appearance.show_hero_points', 'group' => 'appearance'],
        ['key' => 'appearance.show_stats', 'group' => 'appearance'],
        ['key' => 'appearance.show_urgent_requests', 'group' => 'appearance'],
        ['key' => 'appearance.show_steps', 'group' => 'appearance'],
        ['key' => 'appearance.show_community', 'group' => 'appearance'],
        ['key' => 'appearance.show_donor_card', 'group' => 'appearance'],
        ['key' => 'appearance.show_cta', 'group' => 'appearance'],
        ['key' => 'appearance.show_footer_contact', 'group' => 'appearance'],
        ['key' => 'appearance.show_footer_districts', 'group' => 'appearance'],
        ['key' => 'appearance.show_footer_credit', 'group' => 'appearance'],
        ['key' => 'security.require_phone_verification', 'group' => 'security'],
        ['key' => 'security.require_email_verification', 'group' => 'security'],
        ['key' => 'security.allow_registration', 'group' => 'security'],
        ['key' => 'donors.require_login', 'group' => 'donors'],
        ['key' => 'donors.auto_approve', 'group' => 'donors'],
        ['key' => 'ads.enabled', 'group' => 'ads'],
        ['key' => 'ads.popunder_enabled', 'group' => 'ads'],
        ['key' => 'mail.welcome_enabled', 'group' => 'mail'],
        ['key' => 'mail.application_enabled', 'group' => 'mail'],
        ['key' => 'mail.status_enabled', 'group' => 'mail'],
        ['key' => 'mail.rating_enabled', 'group' => 'mail'],
        ['key' => 'sms.enabled', 'group' => 'sms'],
        ['key' => 'donation.enabled', 'group' => 'donation'],
        ['key' => 'donation.show_goal', 'group' => 'donation'],
        ['key' => 'others.blog_enabled', 'group' => 'others'],
    ];

    public function edit(): View
    {
        $values = [];

        foreach (static::allKeys() as $key) {
            $values[$key] = Setting::get($key);
        }

        return view('admin.settings.edit', [
            'values' => $values,
            'logo' => Setting::get('appearance.logo_path'),
            'favicon' => Setting::get('appearance.favicon_path'),
            'locales' => $this->availableLocales(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site.name' => ['required', 'string', 'max:80'],
            'site.tagline' => ['nullable', 'string', 'max:160'],
            'site.contact_email' => ['nullable', 'email', 'max:120'],
            'site.contact_phone' => ['nullable', 'string', 'max:30'],
            'site.address' => ['nullable', 'string', 'max:200'],
            'site.country' => ['nullable', 'string', 'max:80'],
            'site.currency' => ['nullable', 'string', 'max:10'],
            'seo.title' => ['nullable', 'string', 'max:180'],
            'seo.description' => ['nullable', 'string', 'max:300'],
            'seo.keywords' => ['nullable', 'string', 'max:300'],
            'appearance.primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'appearance.paper_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'appearance.surface_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'appearance.raised_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'appearance.line_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'appearance.ink_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'appearance.font_sans' => ['nullable', 'in:hind,inter,noto-sans,system'],
            'appearance.font_display' => ['nullable', 'in:tiro,noto-serif,georgia,system'],
            'appearance.font_size' => ['nullable', 'integer', 'between:14,18'],
            'appearance.logo_height' => ['nullable', 'integer', 'between:24,64'],
            'appearance.layout' => ['required', 'in:classic,minimal'],
            'appearance.container_width' => ['nullable', 'in:normal,wide,full'],
            'appearance.radius' => ['nullable', 'in:sharp,soft,round'],
            'appearance.section_spacing' => ['nullable', 'in:compact,normal,relaxed'],
            'appearance.button_style' => ['nullable', 'in:ink,brand,outline'],
            'appearance.card_style' => ['nullable', 'in:flat,bordered,raised'],
            'appearance.announcement_text' => ['nullable', 'string', 'max:200'],
            'appearance.announcement_url' => ['nullable', 'string', 'max:500'],
            'appearance.hero_style' => ['nullable', 'in:split,centered,minimal'],
            'appearance.footer_style' => ['nullable', 'in:columns,minimal'],
            'appearance.custom_css' => ['nullable', 'string', 'max:20000'],
            'donors.daily_reveal_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'ads.popunder_code' => ['nullable', 'string'],
            'mail.driver' => ['nullable', 'in:log,smtp'],
            'mail.host' => ['nullable', 'string', 'max:180'],
            'mail.port' => ['nullable', 'integer', 'between:1,65535'],
            'mail.username' => ['nullable', 'string', 'max:180'],
            'mail.password' => ['nullable', 'string', 'max:255'],
            'mail.encryption' => ['nullable', 'in:tls,ssl,starttls'],
            'mail.from_address' => ['nullable', 'email', 'max:180'],
            'mail.from_name' => ['nullable', 'string', 'max:120'],
            'sms.driver' => ['nullable', 'in:log,http'],
            'sms.gateway_url' => ['nullable', 'string', 'max:500'],
            'sms.api_key' => ['nullable', 'string', 'max:255'],
            'sms.sender_id' => ['nullable', 'string', 'max:60'],
            'sms.otp_template' => ['nullable', 'string', 'max:300'],
            'donation.currency' => ['nullable', 'string', 'max:10'],
            'donation.min_amount' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'donation.goal_amount' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'donation.bkash_number' => ['nullable', 'string', 'max:30'],
            'donation.nagad_number' => ['nullable', 'string', 'max:30'],
            'donation.rocket_number' => ['nullable', 'string', 'max:30'],
            'donation.bank_details' => ['nullable', 'string', 'max:1000'],
            'donation.instructions' => ['nullable', 'string', 'max:1000'],
            'donation.thank_you' => ['nullable', 'string', 'max:300'],
            'others.footer_text' => ['nullable', 'string', 'max:500'],
            'others.analytics_code' => ['nullable', 'string', 'max:5000'],
            'others.timezone' => ['nullable', 'timezone'],
            'others.locale' => ['nullable', 'string', 'max:12', 'regex:/^[a-z]{2,8}([_-][A-Za-z]{2,8})?$/'],
            'others.location_country_code' => ['nullable', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'language_code' => ['nullable', 'required_with:language_file', 'string', 'max:12', 'regex:/^[a-z]{2,8}([_-][A-Za-z]{2,8})?$/'],
            'language_file' => ['nullable', 'file', 'extensions:json', 'max:5120'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
        ]);

        $languageCount = null;

        foreach (self::TEXT_SETTINGS as $key => [$group, $type]) {
            if (! Arr::has($data, $key)) {
                continue;
            }

            $value = data_get($data, $key);

            // Blank passwords / keys keep the saved value instead of erasing it.
            if (in_array($key, ['mail.password', 'sms.api_key'], true) && blank($value)) {
                continue;
            }

            if ($key === 'others.location_country_code' && filled($value)) {
                $value = strtoupper((string) $value);
            }

            if ($key === 'others.locale' && filled($value)) {
                $value = strtolower(str_replace('-', '_', (string) $value));
            }

            Setting::put($key, $value, $group, $type);
        }

        foreach (self::BOOLEAN_SETTINGS as $setting) {
            if ($request->has($setting['key'])) {
                Setting::put($setting['key'], $request->boolean($setting['key']), $setting['group'], 'boolean');
            }
        }

        $this->storeUpload($request, 'logo', 'appearance.logo_path');
        $this->storeUpload($request, 'favicon', 'appearance.favicon_path');

        if ($request->hasFile('language_file')) {
            $languageCode = strtolower(str_replace('-', '_', (string) ($data['language_code'] ?? $data['others.locale'] ?? 'en')));
            $languageCount = $this->importLanguage($request->file('language_file')->getRealPath(), $languageCode);
            Setting::put('others.locale', $languageCode, 'others');
        }

        Cache::forget('footer.districts.'.LocationCountry::code());

        ActivityLog::record('settings.updated', null, 'সাইট সেটিংস আপডেট করা হয়েছে।');

        $message = $languageCount !== null
            ? "ভাষা ইমপোর্ট হয়েছে ({$languageCount} টি অনুবাদ) এবং সক্রিয় করা হয়েছে।"
            : 'সেটিংস সংরক্ষণ করা হয়েছে।';

        return redirect()->route('admin.settings.edit')->with('success', $message);
    }

    /**
     * @return array<string, string>
     */
    private function availableLocales(): array
    {
        $locales = ['bn' => 'বাংলা', 'en' => 'English'];

        foreach (glob(lang_path('*/static.json')) ?: [] as $path) {
            $code = basename(dirname($path));

            if (preg_match('/^[a-z]{2,8}(_[a-z]{2,8})?$/', $code) === 1) {
                $locales[$code] ??= strtoupper($code);
            }
        }

        foreach (glob(storage_path('app/languages/*.json')) ?: [] as $path) {
            $code = basename($path, '.json');

            if (preg_match('/^[a-z]{2,8}(_[a-z]{2,8})?$/', $code) === 1) {
                $locales[$code] ??= strtoupper($code);
            }
        }

        ksort($locales);

        return $locales;
    }

    private function importLanguage(string $path, string $locale): int
    {
        if (preg_match('/^[a-z]{2,8}(_[a-z]{2,8})?$/', $locale) !== 1) {
            throw ValidationException::withMessages([
                'language_code' => 'ভাষা কোড সঠিক নয়। উদাহরণ: en, hi, ar, es, pt_BR।',
            ]);
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded) || array_is_list($decoded)) {
            throw ValidationException::withMessages([
                'language_file' => 'JSON ফাইলটি {"বাংলা টেক্সট": "অনুবাদ"} ফরম্যাটে হতে হবে।',
            ]);
        }

        $dictionary = [];

        foreach ($decoded as $source => $translated) {
            if (is_string($source) && is_string($translated) && trim($source) !== '' && trim($translated) !== '') {
                $dictionary[$source] = $translated;
            }
        }

        if ($dictionary === []) {
            throw ValidationException::withMessages([
                'language_file' => 'ফাইলে কোনো ব্যবহারযোগ্য অনুবাদ পাওয়া যায়নি।',
            ]);
        }

        $target = storage_path('app/languages/'.$locale.'.json');
        File::ensureDirectoryExists(dirname($target));

        if (is_file($target)) {
            $existing = json_decode((string) file_get_contents($target), true);

            if (is_array($existing) && ! array_is_list($existing)) {
                $dictionary = array_merge($existing, $dictionary);
            }
        }

        File::put($target, json_encode($dictionary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL);

        return count($dictionary);
    }

    private function storeUpload(Request $request, string $field, string $settingKey): void
    {
        if (! $request->hasFile($field)) {
            return;
        }

        $old = Setting::get($settingKey);

        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        Setting::put($settingKey, $request->file($field)->store('site', 'public'), 'appearance');
    }

    /**
     * @return array<int, string>
     */
    private static function allKeys(): array
    {
        return [
            ...array_keys(self::TEXT_SETTINGS),
            ...array_map(fn (array $setting): string => $setting['key'], self::BOOLEAN_SETTINGS),
        ];
    }
}
