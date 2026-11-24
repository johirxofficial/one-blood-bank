<?php

namespace App\Providers;

use App\Enums\DonorStatus;
use App\Models\District;
use App\Models\Setting;
use App\Support\LocationCountry;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->letters()->numbers());

        Paginator::useTailwind();

        $this->applyRuntimeSettings();

        View::composer('layouts.app', function ($view): void {
            $districts = Cache::remember('footer.districts.'.LocationCountry::code(), now()->addHour(), fn () => District::query()
                ->forCountry()
                ->withCount(['donors' => fn ($query) => $query->where('status', DonorStatus::Approved)])
                ->orderByDesc('donors_count')
                ->orderBy('name')
                ->take(8)
                ->get(['id', 'name', 'name_bn'])
                ->map(fn (District $district) => [
                    'id' => $district->id,
                    'name' => $district->name,
                    'name_bn' => $district->name_bn,
                ])
                ->all());

            $view->with('footerDistricts', $districts);
        });
    }

    /**
     * Read administrator settings that must exist before a request is handled.
     * Failures are ignored so installers and migrations can still boot.
     */
    private function applyRuntimeSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $timezone = (string) Setting::get('others.timezone', config('app.timezone'));

            if ($timezone !== '' && in_array($timezone, timezone_identifiers_list(), true)) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }

            $locale = (string) Setting::get('others.locale', 'bn');
            $locale = strtolower(str_replace('-', '_', $locale));

            $supported = $locale === 'bn'
                || is_file(lang_path($locale.'/static.json'))
                || is_file(lang_path($locale.'.json'))
                || is_file(storage_path('app/languages/'.$locale.'.json'));

            if ($supported && preg_match('/^[a-z]{2,8}(_[a-z]{2,8})?$/', $locale) === 1) {
                app()->setLocale($locale);
                config(['app.locale' => $locale]);
                Carbon::setLocale($locale);
            }

            Setting::applyMailConfig();
        } catch (\Throwable) {
            // Booting must never fail because the database is not ready yet.
        }
    }
}
