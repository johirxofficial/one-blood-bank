# One Blood Bank

One Blood Bank is a multilingual, multi-country blood donor network built with Laravel and Tailwind CSS. It was created for Bangladesh and is designed so communities in other countries can run their own localised instance with their own location hierarchy and language.

The public site is authored in Bengali. English is included as a complete UI translation, and any other language can be added with a JSON dictionary without changing Blade templates.

## Features

- Verified donor directory with district, upazila and union filters
- Donor application, approval workflow and 4-month availability window
- Phone OTP verification, email verification and secure password reset
- Donor ratings and reviews
- Donor ID card as a one-page PDF with QR verification
- Up to five additional contact numbers per donor
- Phone-number reveal limits and audit logging
- Blood requests, site donations, blog, pages, ads and contact messages
- Admin, manager, staff and donor roles
- Tabbed admin settings for branding, SEO, mail, SMS, donors, donations, ads and localisation
- First-run web installer with branding, admin account and language setup
- Multi-country donor addresses (`country`, `state`, `city`) plus importable location hierarchies
- English UI dictionary plus support for any locale through an importable dictionary
- SQLite test suite and MySQL-ready production schema

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js 20 or newer and npm
- MySQL 8 (recommended for production) or SQLite
- A writable `storage` and `bootstrap/cache` directory

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create a database and update `DB_*` values in `.env`. Then run:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`. If the application is not installed yet, the first-run installer will guide you through site name, country, language, branding, admin account and optional mail settings.

The installer is guarded by `storage/app/installed.lock` and by existing user accounts. Remove the lock only when you intentionally want to run the installer again.

## Multi-country setup

1. Install the site and choose the country it serves.
2. Open **Admin → Settings → Others** and set **Location country code** to the ISO-3166 alpha-2 code for that country (for example `BD`, `IN`, `US`, `GB`).
3. Import the country’s divisions, districts, upazilas and unions (see below).
4. Donors can always enter free-text country/state/city details, even when a hierarchy has not been imported yet.

Bangladesh data is seeded automatically when Bangladesh is selected during installation.

## Import locations

### Admin UI

Go to **Admin → Locations** and upload a CSV file. A sample file can be downloaded from the same page. This screen is available to administrators.

### CLI

```bash
php artisan locations:import path/to/locations.csv --country=BD
```

The CSV must have `level` and `name` columns. Supported levels are `division`, `district`, `upazila` and `union`.

| Column | Required | Description |
| --- | --- | --- |
| `level` | Yes | `division`, `district`, `upazila` or `union` |
| `name` | Yes | English/local name |
| `name_bn` | No | Bengali or secondary name |
| `parent` | No for division | Parent name or parent `code` |
| `code` | No | Government or internal code; usable later as a parent reference |
| `latitude` | No | Decimal latitude |
| `longitude` | No | Decimal longitude |

Example:

```csv
level,name,name_bn,parent,code,latitude,longitude
division,Dhaka,ঢাকা,,BD-DHA,23.8103,90.4125
district,Dhaka,ঢাকা,Dhaka,BD-DHA-DHA,23.8103,90.4125
upazila,Savar,সাভার,Dhaka,BD-DHA-SAV,23.8583,90.2667
union,Example Union,উদাহরণ ইউনিয়ন,Savar,BD-DHA-SAV-001,,
```

Rows are imported in order, so a parent must appear before its children. Imports are transactional: an invalid row rolls back the whole file. Re-importing the same file updates existing rows instead of duplicating them.

A ready-to-edit sample is available at [`docs/location-import-example.csv`](docs/location-import-example.csv).

## Add a language

The source UI is Bengali. A translation dictionary maps Bengali text to the target language:

```json
{
  "লগইন করুন": "Log in",
  "ডোনার খুঁজুন": "Find donors",
  "রক্তের গ্রুপ": "Blood group"
}
```

Import it with the CLI:

```bash
php artisan language:import hi path/to/hi.json --activate
```

Or upload the JSON from **Admin → Settings → Others → Import a new language**, enter a locale code such as `hi`, `ar`, `es` or `pt_BR`, and save. The language is stored at `storage/app/languages/{locale}.json`, so it survives deployments that replace application files.

The active language can be changed from the same settings screen. The translation middleware only translates text nodes and common attributes, so long user-authored content such as blog posts and policy pages is left untouched.

A sample dictionary is available at [`docs/language-dictionary-example.json`](docs/language-dictionary-example.json).

## Testing and formatting

```bash
php artisan test
vendor/bin/pint --test
```

The test suite uses SQLite in memory. Production should use MySQL.

## Production deployment

- Point the document root to `public/`.
- Set `APP_ENV=production`, `APP_DEBUG=false` and a strong `APP_KEY`.
- Run `php artisan migrate --force` and `php artisan storage:link`.
- Build assets with `npm ci && npm run build`.
- Configure a queue worker and scheduler if you enable mail/SMS notifications.
- Keep `.env`, `storage/app/installed.lock`, uploaded files and database backups out of version control.

The application has been written to run on shared cPanel hosting, standard Linux servers and Windows/XAMPP.

## License

One Blood Bank is open-source software licensed under the [MIT license](LICENSE).

## Credits

Built with [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com), [Dompdf](https://github.com/dompdf/dompdf) and the open-source community.
