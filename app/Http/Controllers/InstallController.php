<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Division;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Support\LocationCountry;
use Database\Seeders\GeoLocationSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Throwable;

class InstallController extends Controller
{
    /**
     * Sensible locale/currency defaults for the countries customers ask about most.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const COUNTRY_PRESETS = [
        'Bangladesh' => ['Asia/Dhaka', 'BDT'],
        'India' => ['Asia/Kolkata', 'INR'],
        'Pakistan' => ['Asia/Karachi', 'PKR'],
        'Nepal' => ['Asia/Kathmandu', 'NPR'],
        'Bhutan' => ['Asia/Thimphu', 'BTN'],
        'Sri Lanka' => ['Asia/Colombo', 'LKR'],
        'Maldives' => ['Indian/Maldives', 'MVR'],
        'Afghanistan' => ['Asia/Kabul', 'AFN'],
        'Myanmar' => ['Asia/Yangon', 'MMK'],
        'Malaysia' => ['Asia/Kuala_Lumpur', 'MYR'],
        'Singapore' => ['Asia/Singapore', 'SGD'],
        'Indonesia' => ['Asia/Jakarta', 'IDR'],
        'Thailand' => ['Asia/Bangkok', 'THB'],
        'Vietnam' => ['Asia/Ho_Chi_Minh', 'VND'],
        'Philippines' => ['Asia/Manila', 'PHP'],
        'China' => ['Asia/Shanghai', 'CNY'],
        'Japan' => ['Asia/Tokyo', 'JPY'],
        'South Korea' => ['Asia/Seoul', 'KRW'],
        'Saudi Arabia' => ['Asia/Riyadh', 'SAR'],
        'United Arab Emirates' => ['Asia/Dubai', 'AED'],
        'Qatar' => ['Asia/Qatar', 'QAR'],
        'Kuwait' => ['Asia/Kuwait', 'KWD'],
        'Bahrain' => ['Asia/Bahrain', 'BHD'],
        'Oman' => ['Asia/Muscat', 'OMR'],
        'Jordan' => ['Asia/Amman', 'JOD'],
        'Lebanon' => ['Asia/Beirut', 'LBP'],
        'Iraq' => ['Asia/Baghdad', 'IQD'],
        'Iran' => ['Asia/Tehran', 'IRR'],
        'Israel' => ['Asia/Jerusalem', 'ILS'],
        'Turkey' => ['Europe/Istanbul', 'TRY'],
        'United States' => ['America/New_York', 'USD'],
        'United Kingdom' => ['Europe/London', 'GBP'],
        'Canada' => ['America/Toronto', 'CAD'],
        'Australia' => ['Australia/Sydney', 'AUD'],
        'New Zealand' => ['Pacific/Auckland', 'NZD'],
        'Germany' => ['Europe/Berlin', 'EUR'],
        'France' => ['Europe/Paris', 'EUR'],
        'Italy' => ['Europe/Rome', 'EUR'],
        'Spain' => ['Europe/Madrid', 'EUR'],
        'Netherlands' => ['Europe/Amsterdam', 'EUR'],
        'Belgium' => ['Europe/Brussels', 'EUR'],
        'Austria' => ['Europe/Vienna', 'EUR'],
        'Portugal' => ['Europe/Lisbon', 'EUR'],
        'Ireland' => ['Europe/Dublin', 'EUR'],
        'Switzerland' => ['Europe/Zurich', 'CHF'],
        'Sweden' => ['Europe/Stockholm', 'SEK'],
        'Norway' => ['Europe/Oslo', 'NOK'],
        'Denmark' => ['Europe/Copenhagen', 'DKK'],
        'Poland' => ['Europe/Warsaw', 'PLN'],
        'Russia' => ['Europe/Moscow', 'RUB'],
        'South Africa' => ['Africa/Johannesburg', 'ZAR'],
        'Nigeria' => ['Africa/Lagos', 'NGN'],
        'Kenya' => ['Africa/Nairobi', 'KES'],
        'Ghana' => ['Africa/Accra', 'GHS'],
        'Egypt' => ['Africa/Cairo', 'EGP'],
        'Brazil' => ['America/Sao_Paulo', 'BRL'],
        'Mexico' => ['America/Mexico_City', 'MXN'],
    ];

    public function show(): View|RedirectResponse
    {
        if (self::alreadyInstalled()) {
            return redirect()->route('home')
                ->with('info', 'ইনস্টলেশন ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        return view('install.index', [
            'countries' => config('countries', []),
            'presets' => self::COUNTRY_PRESETS,
            'timezones' => timezone_identifiers_list(),
            'checks' => $this->checks(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (self::alreadyInstalled()) {
            return redirect()->route('home')
                ->with('info', 'ইনস্টলেশন ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        $checks = $this->checks();

        if (! $checks['database']['ok']) {
            return back()
                ->withErrors(['database' => 'ডাটাবেস সংযোগ পাওয়া যায়নি। আগে .env-এ DB তথ্য দিন।'])
                ->withInput();
        }

        try {
            $this->ensureAppKey();
            $this->prepareDatabase();
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors(['database' => 'ডাটাবেস প্রস্তুত করা যায়নি: '.$exception->getMessage()])
                ->withInput();
        }

        $request->merge([
            'admin_phone' => PhoneNumber::normalize((string) $request->input('admin_phone')),
            'admin_username' => strtolower((string) $request->input('admin_username')),
            'admin_email' => strtolower((string) $request->input('admin_email')),
        ]);

        $data = $request->validate([
            'step' => ['nullable', 'integer', 'between:1,5'],
            'language' => ['required', Rule::in(['bn', 'en'])],
            'country' => ['required', 'string', 'max:80'],
            'timezone' => ['required', 'timezone'],
            'currency' => ['required', 'string', 'max:10'],
            'site_name' => ['required', 'string', 'max:80'],
            'site_tagline' => ['nullable', 'string', 'max:160'],
            'site_email' => ['nullable', 'email', 'max:120'],
            'site_phone' => ['nullable', 'string', 'max:30'],
            'site_address' => ['nullable', 'string', 'max:200'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
            'admin_name' => ['required', 'string', 'min:3', 'max:120'],
            'admin_username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'lowercase', 'unique:users,username'],
            'admin_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'admin_phone' => ['required', 'string', 'max:30', new PhoneNumber, 'unique:users,phone'],
            'admin_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'require_phone_verification' => ['nullable', 'boolean'],
            'require_email_verification' => ['nullable', 'boolean'],
            'mail_driver' => ['nullable', Rule::in(['log', 'smtp'])],
            'mail_host' => ['nullable', 'string', 'max:180'],
            'mail_port' => ['nullable', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:180'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', Rule::in(['tls', 'ssl', 'starttls'])],
            'mail_from_address' => ['nullable', 'email', 'max:180'],
            'mail_from_name' => ['nullable', 'string', 'max:120'],
            'install_base_data' => ['nullable', 'boolean'],
        ]);

        $logoPath = $request->file('logo')?->store('site', 'public');
        $faviconPath = $request->file('favicon')?->store('site', 'public');

        try {
            $admin = DB::transaction(function () use ($data, $logoPath, $faviconPath): User {
                return $this->createAdmin($data, $logoPath, $faviconPath);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors(['database' => 'ইনস্টলেশন সম্পন্ন করা যায়নি: '.$exception->getMessage()])
                ->withInput();
        }

        Storage::disk('local')->put('installed.lock', json_encode([
            'installed_at' => now()->toIso8601String(),
            'site' => $data['site_name'],
            'language' => $data['language'],
            'admin' => $admin->email,
        ], JSON_PRETTY_PRINT));

        Auth::login($admin);
        app()->setLocale($data['language']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'ইনস্টলেশন সফলভাবে সম্পন্ন হয়েছে। স্বাগতম!');
    }

    public static function alreadyInstalled(): bool
    {
        try {
            if (Storage::disk('local')->exists('installed.lock')) {
                return true;
            }

            return Schema::hasTable('users') && User::query()->exists();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, array{label: string, ok: bool, detail: string, required: bool}>
     */
    private function checks(): array
    {
        $database = true;
        $databaseDetail = 'সংযোগ ঠিক আছে';

        try {
            DB::connection()->getPdo();
        } catch (Throwable $exception) {
            $database = false;
            $databaseDetail = 'সংযোগ পাওয়া যায়নি';
        }

        return [
            'php' => [
                'label' => 'PHP 8.2+',
                'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'detail' => PHP_VERSION,
                'required' => true,
            ],
            'pdo' => [
                'label' => 'PDO / MySQL',
                'ok' => extension_loaded('pdo') && extension_loaded('pdo_mysql'),
                'detail' => extension_loaded('pdo_mysql') ? 'চালু' : 'pdo_mysql প্রয়োজন',
                'required' => true,
            ],
            'mbstring' => [
                'label' => 'Mbstring',
                'ok' => extension_loaded('mbstring'),
                'detail' => extension_loaded('mbstring') ? 'চালু' : 'প্রয়োজন',
                'required' => true,
            ],
            'gd' => [
                'label' => 'GD / ছবি',
                'ok' => extension_loaded('gd'),
                'detail' => extension_loaded('gd') ? 'চালু' : 'ছবি আপলোডের জন্য প্রয়োজন',
                'required' => true,
            ],
            'storage' => [
                'label' => 'Storage writable',
                'ok' => is_writable(storage_path()),
                'detail' => is_writable(storage_path()) ? 'লেখার অনুমতি আছে' : 'storage ফোল্ডারে লেখার অনুমতি দিন',
                'required' => true,
            ],
            'cache' => [
                'label' => 'Bootstrap cache writable',
                'ok' => is_writable(base_path('bootstrap/cache')),
                'detail' => is_writable(base_path('bootstrap/cache')) ? 'লেখার অনুমতি আছে' : 'bootstrap/cache-এ লেখার অনুমতি দিন',
                'required' => true,
            ],
            'env' => [
                'label' => '.env writable',
                'ok' => is_file(base_path('.env')) && is_writable(base_path('.env')),
                'detail' => is_file(base_path('.env')) ? (is_writable(base_path('.env')) ? 'লেখার অনুমতি আছে' : 'APP_KEY তৈরি করতে .env লিখনযোগ্য করুন') : '.env ফাইল নেই',
                'required' => false,
            ],
            'database' => [
                'label' => 'ডাটাবেস',
                'ok' => $database,
                'detail' => $databaseDetail,
                'required' => true,
            ],
        ];
    }

    /**
     * A fresh copy needs migrations before the installer can write users/settings.
     */
    private function prepareDatabase(): void
    {
        Artisan::call('migrate', ['--force' => true]);

        if (Schema::hasTable('settings')) {
            app(SettingsSeeder::class)->run();
        }

        if (Schema::hasTable('pages') && Page::query()->count() === 0) {
            app(PageSeeder::class)->run();
        }

        if (request('country') === 'Bangladesh' && Schema::hasTable('divisions') && Division::query()->count() === 0) {
            app(GeoLocationSeeder::class)->run();
        }
    }

    private function ensureAppKey(): void
    {
        if (filled(config('app.key'))) {
            return;
        }

        $path = base_path('.env');

        if (! is_file($path) || ! is_writable($path)) {
            throw new \RuntimeException('.env ফাইল লিখনযোগ্য নয়, APP_KEY তৈরি করা যাচ্ছে না।');
        }

        $key = 'base64:'.base64_encode(random_bytes(32));
        $contents = (string) file_get_contents($path);

        if (preg_match('/^APP_KEY=.*$/m', $contents) === 1) {
            $contents = (string) preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$key, $contents);
        } else {
            $contents .= PHP_EOL.'APP_KEY='.$key.PHP_EOL;
        }

        file_put_contents($path, $contents);
        config(['app.key' => $key]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createAdmin(array $data, ?string $logoPath, ?string $faviconPath): User
    {
        $settings = [
            'site.name' => [$data['site_name'], 'general', 'string'],
            'site.tagline' => [($data['site_tagline'] ?? null) ?: 'রক্ত দিন, জীবন বাঁচান', 'general', 'string'],
            'site.contact_email' => [($data['site_email'] ?? null) ?: $data['admin_email'], 'general', 'string'],
            'site.contact_phone' => [($data['site_phone'] ?? null) ?: $data['admin_phone'], 'general', 'string'],
            'site.address' => [($data['site_address'] ?? null) ?: $data['country'], 'general', 'string'],
            'site.country' => [$data['country'], 'general', 'string'],
            'site.currency' => [$data['currency'], 'general', 'string'],
            'seo.title' => [$data['site_name'].' — Verified Blood Donor Network', 'seo', 'string'],
            'seo.description' => [
                ($data['site_tagline'] ?? null) ?: 'Find verified blood donors quickly and safely.',
                'seo',
                'string',
            ],
            'others.timezone' => [$data['timezone'], 'others', 'string'],
            'others.locale' => [$data['language'], 'others', 'string'],
            'others.location_country_code' => [LocationCountry::codeForName($data['country']), 'others', 'string'],
            'security.require_phone_verification' => [(bool) ($data['require_phone_verification'] ?? false), 'security', 'boolean'],
            'security.require_email_verification' => [(bool) ($data['require_email_verification'] ?? false), 'security', 'boolean'],
            'security.allow_registration' => [true, 'security', 'boolean'],
            'donors.require_login' => [true, 'donors', 'boolean'],
            'donors.daily_reveal_limit' => [15, 'donors', 'integer'],
            'donors.auto_approve' => [false, 'donors', 'boolean'],
            'donation.currency' => [$data['currency'], 'donation', 'string'],
            'donation.enabled' => [true, 'donation', 'boolean'],
            'mail.driver' => [($data['mail_driver'] ?? null) ?: 'log', 'mail', 'string'],
            'mail.host' => [$data['mail_host'] ?? '', 'mail', 'string'],
            'mail.port' => [$data['mail_port'] ?? 587, 'mail', 'integer'],
            'mail.username' => [$data['mail_username'] ?? '', 'mail', 'string'],
            'mail.password' => [$data['mail_password'] ?? '', 'mail', 'string'],
            'mail.encryption' => [($data['mail_encryption'] ?? null) ?: 'tls', 'mail', 'string'],
            'mail.from_address' => [($data['mail_from_address'] ?? null) ?: $data['admin_email'], 'mail', 'string'],
            'mail.from_name' => [($data['mail_from_name'] ?? null) ?: $data['site_name'], 'mail', 'string'],
        ];

        if ($logoPath) {
            $settings['appearance.logo_path'] = [$logoPath, 'appearance', 'string'];
        }

        if ($faviconPath) {
            $settings['appearance.favicon_path'] = [$faviconPath, 'appearance', 'string'];
        }

        foreach ($settings as $key => [$value, $group, $type]) {
            Setting::put($key, $value, $group, $type);
        }

        return User::query()->create([
            'name' => $data['admin_name'],
            'username' => $data['admin_username'],
            'email' => $data['admin_email'],
            'phone' => $data['admin_phone'],
            'password' => $data['admin_password'],
            'role' => UserRole::Admin,
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }
}
