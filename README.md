# One Blood Bank

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

One Blood Bank is a multilingual, multi-country blood donor network built with Laravel and Tailwind CSS. It was created for Bangladesh and is designed so communities in any country can run their own localised instance with their own location hierarchy and language.

The public site is authored in Bengali. English is included as a complete UI translation, and any other language can be added with a JSON dictionary without touching Blade templates.

- **Repository:** <https://github.com/johirxofficial/one-blood-bank>
- **Developer:** [johirxofficial](https://github.com/johirxofficial)
- **License:** MIT (free for personal and commercial use)

---

## Table of contents

- [Features](#features)
- [Requirements](#requirements)
- [Quick start (local development)](#quick-start-local-development)
- [First-run installer](#first-run-installer)
- [Demo data](#demo-data)
- [Environment configuration](#environment-configuration)
- [Multi-country setup](#multi-country-setup)
- [Import a location hierarchy](#import-a-location-hierarchy)
- [Add or change a language](#add-or-change-a-language)
- [Roles and demo accounts](#roles-and-demo-accounts)
- [Donor card PDF](#donor-card-pdf)
- [Admin panel](#admin-panel)
- [Testing and code style](#testing-and-code-style)
- [Deployment guides](#deployment-guides)
  - [Shared hosting / cPanel without SSH](#shared-hosting--cpanel-without-ssh)
  - [cPanel with SSH or Terminal](#cpanel-with-ssh-or-terminal)
  - [Linux VM / VPS (Ubuntu + Nginx)](#linux-vm--vps-ubuntu--nginx)
  - [Windows with XAMPP](#windows-with-xampp)
  - [Windows Server / IIS](#windows-server--iis)
  - [Plesk](#plesk)
  - [Generic hosting checklist](#generic-hosting-checklist)
  - [Post-deployment checklist](#post-deployment-checklist)
- [Updating an existing installation](#updating-an-existing-installation)
- [Backups](#backups)
- [Troubleshooting](#troubleshooting)
- [Security notes](#security-notes)
- [Contributing](#contributing)
- [License](#license)
- [Credits](#credits)

---

## Features

- Verified donor directory with division, district, upazila and union filters
- Donor application, approval workflow and automatic 4-month availability window
- Phone OTP verification, email verification and secure password reset
- Donor ratings and reviews
- Donor ID card as a one-page PDF with QR verification
- Up to five additional contact numbers per donor
- Daily phone-number reveal limits and audit logging
- Blood requests, site donations, blog, pages, ads and contact messages
- Admin, manager, staff and donor roles
- Tabbed admin settings for branding, SEO, mail, SMS, donors, donations, ads and localisation
- First-run web installer with branding, admin account and language setup
- Multi-country donor addresses (`country`, `state`, `city`) plus importable location hierarchies
- English UI dictionary plus support for any locale through an importable dictionary
- Bengali-first design with a full English mode
- SQLite test suite and MySQL-ready production schema

---

## Requirements

### Server

| Component | Minimum | Recommended |
| --- | --- | --- |
| PHP | 8.3 | 8.4 |
| Composer | 2.x | latest 2.x |
| MySQL | 8.0 | 8.0+ / MariaDB 10.6+ |
| Node.js (build only) | 20 | 22 LTS |
| Web server | Apache 2.4 / Nginx 1.20 | latest stable |

Required PHP extensions:

`bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql` (or `pdo_sqlite`), `tokenizer`, `xml`, `zip`.

> `gd` is used for QR codes and image handling. `dom` and `mbstring` are required by Dompdf for the donor card PDF.

### Local tooling

- Composer: <https://getcomposer.org/download/>
- Node.js: <https://nodejs.org/>
- Git: <https://git-scm.com/>

On Windows, PHP and Composer are often not on `PATH`. Use the full executable paths or add them to `PATH`, for example `C:\xampp\php\php.exe` and `C:\ProgramData\ComposerSetup\bin\composer.bat`.

---

## Quick start (local development)

```bash
git clone https://github.com/johirxofficial/one-blood-bank.git
cd one-blood-bank

composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate

# Create a MySQL database, then set DB_* values in .env.
# Or keep DB_CONNECTION=sqlite and create the file:
#   Windows: type nul > database\database.sqlite
#   Linux/macOS: touch database/database.sqlite

php artisan migrate
npm install
npm run build
php artisan serve
```

Open <http://127.0.0.1:8000/install>. The first-run installer guides you through site name, country, language, branding, admin account and optional mail settings.

The same sequence is available as a Composer shortcut:

```bash
composer setup
```

`composer setup` installs dependencies, creates `.env`, generates the app key, migrates the database and builds assets. It does **not** create demo data, so the installer is still available afterwards.

### Development server with hot reload

```bash
composer dev
```

This runs the PHP server, queue listener, log viewer and Vite dev server together.

---

## First-run installer

The installer is available at `/install` and is guarded by two conditions:

1. `storage/app/installed.lock` must not exist.
2. The `users` table must be empty.

It performs these steps:

1. Environment and requirement checks
2. Database migration
3. Site details (name, country, language, contact information)
4. Branding (logo, favicon, primary colour) and optional mail settings
5. Administrator account creation
6. Writes the lock file, clears caches and signs the administrator in

Remove `storage/app/installed.lock` **only** when you intentionally want to re-run the installer. Never expose a fresh installation publicly before completing it.

---

## Demo data

For a local demo instead of the installer:

```bash
php artisan migrate:fresh --seed
```

Demo accounts (password: `password`):

| Role | Email |
| --- | --- |
| Admin | `admin@thebloodbank.test` |
| Manager | `manager@thebloodbank.test` |
| Staff | `staff@thebloodbank.test` |
| Donor | `donor@thebloodbank.test` |

The seeder also creates sample divisions, districts, upazilas, unions, donors, blog posts and pages. Because users exist, the web installer is skipped.

> Never run `migrate:fresh` or the demo seeder on a production database.

---

## Environment configuration

Copy `.env.example` to `.env` and adjust the values below.

### Application

```dotenv
APP_NAME="One Blood Bank"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-domain.com
```

Generate the key once with `php artisan key:generate --force`. Changing `APP_KEY` later invalidates encrypted sessions and cookies.

### Database (MySQL)

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=one_blood_bank
DB_USERNAME=obb
DB_PASSWORD=strong-password
```

Create the database with `utf8mb4`:

```sql
CREATE DATABASE one_blood_bank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Sessions, cache and queue

```dotenv
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

On very small shared hosting plans, `QUEUE_CONNECTION=sync` avoids stuck queued jobs. If you keep `database`, run a queue worker (see the Linux guide).

### Mail

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Mail settings can also be edited from **Admin → Settings → Mail** after installation. Email verification can be disabled in **Admin → Settings → Donors** if you do not have SMTP.

### Locale

```dotenv
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

The active site language is stored in the database and managed from **Admin → Settings → Others**. `APP_LOCALE` only affects framework-level strings.

---

## Multi-country setup

1. Complete the installer and choose the country the site serves.
2. Open **Admin → Settings → Others** and set **Location country code** to the ISO-3166 alpha-2 code, for example `BD`, `IN`, `US` or `GB`.
3. Import that country's division/district/upazila/union hierarchy (see below).
4. Donors can always enter free-text country, state and city details, even before a hierarchy is imported.

Bangladesh data is seeded automatically when Bangladesh is selected during installation. All location tables carry a `country_code` column, so one database can hold several countries without mixing their dropdowns.

---

## Import a location hierarchy

### Admin UI

Go to **Admin → Locations**, download the sample CSV and upload your file. The screen reports how many rows were created or updated.

### CLI

```bash
php artisan locations:import path/to/locations.csv --country=BD
```

Options:

| Option | Default | Description |
| --- | --- | --- |
| `--country=` | `BD` | ISO-3166 alpha-2 country code |
| `--delimiter=` | `,` | CSV delimiter (`;` or tab for some exports) |

CSV columns:

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

Rules:

- Rows are imported in order, so a parent must appear before its children.
- Imports are transactional: one invalid row rolls back the entire file.
- Re-importing the same file updates existing rows instead of duplicating them.
- Parent names must be unique within the same level and country, or use the parent `code`.

A ready-to-edit sample is at [`docs/location-import-example.csv`](docs/location-import-example.csv).

---

## Add or change a language

The source UI is Bengali. A dictionary maps Bengali text to the target language:

```json
{
  "লগইন করুন": "Log in",
  "ডোনার খুঁজুন": "Find donors",
  "রক্তের গ্রুপ": "Blood group"
}
```

Import it from the CLI:

```bash
php artisan language:import hi path/to/hi.json --activate
```

Or upload the JSON from **Admin → Settings → Others → Import a new language**, enter a locale code such as `hi`, `ar`, `es` or `pt_BR`, and save.

How dictionaries are resolved (highest priority first):

1. `storage/app/languages/{locale}.json`
2. `lang/{locale}.json`
3. `lang/{locale}/static.json`

Storage dictionaries survive deployments that replace application files. The translation middleware only translates text nodes and common attributes, so long user-authored content such as blog posts and policy pages is left untouched. Digit localisation and English-specific phrase rules only run for `en`.

Change the active language from **Admin → Settings → Others**, or set it directly in the database. A sample dictionary is at [`docs/language-dictionary-example.json`](docs/language-dictionary-example.json).

> To translate a new phrase, add the exact Bengali source text as the key. Matching is whitespace-normalised but otherwise literal.

---

## Roles and demo accounts

| Role | Capabilities |
| --- | --- |
| `admin` | Full access: settings, staff, locations, languages, approvals, donations, logs |
| `manager` | Donor approvals, content, requests and day-to-day operations |
| `staff` | Verification and moderation tasks assigned by admins |
| `user` | Public account; becomes a donor after approval |

Donor state lives in the separate `donors` table. A user can exist without being a donor.

---

## Donor card PDF

Approved donors can download a one-page A6 landscape ID card from their dashboard. The card includes:

- Donor name, blood group and location
- Photo or initials
- QR code that links to the public profile
- Verification status

The Bengali font `HindSiliguri-Bold.ttf` is committed under `resources/fonts/` so a fresh clone can generate cards without extra setup. If Dompdf font caches ever become corrupted, delete `storage/app/dompdf-fonts/*` and retry.

Public donor profiles are canonical at `/@username`; `/donors/{username}` also resolves to the same profile.

---

## Admin panel

The admin panel lives at `/admin`. Main areas:

- **Dashboard** — counts, pending approvals and recent activity
- **Donors** — approve, reject, suspend, verify and edit donors
- **Blood requests** — review and close requests
- **Site donations** — verify offline donations and publish thank-you entries
- **Blog / Pages / Ads** — content management
- **Messages** — contact form inbox
- **Staff** — create manager and staff accounts
- **Locations** — import country hierarchies from CSV
- **Settings** — branding, SEO, mail, SMS, donors, donations, ads and localisation
- **Logs** — audit trail

---

## Testing and code style

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

The test suite uses SQLite in memory. Production should use MySQL. To auto-fix formatting:

```bash
vendor/bin/pint
```

---

## Deployment guides

### Shared hosting / cPanel without SSH

This is the most common setup for small communities. You need cPanel File Manager, phpMyAdmin and (ideally) the ability to change a domain's document root.

#### 1. Build assets locally

Shared hosts often have no Node.js. Build on your computer first:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Then upload the project **including** `vendor/` and `public/build/`.

#### 2. Create the database

In cPanel → **MySQL® Databases**:

1. Create a database, e.g. `myuser_bloodbank`.
2. Create a database user with a strong password.
3. Add the user to the database with **All Privileges**.
4. Note the full names cPanel assigns (they are prefixed with your account name).

#### 3. Upload the files

Using **File Manager**:

1. Create `/home/myuser/one-blood-bank`.
2. Upload a ZIP of the project and extract it there.
3. Keep `public/` inside the project for now.

#### 4. Point the domain at `public/`

Preferred method: in cPanel → **Domains** (or **Addon Domains** / **Subdomains**), set the document root to:

```
/home/myuser/one-blood-bank/public
```

If your host does not allow changing the document root, use the fallback below.

<details>
<summary><strong>Fallback: host has a fixed <code>public_html</code> document root</strong></summary>

1. Move the contents of `one-blood-bank/public/` into `public_html/`.
2. Edit `public_html/index.php` and update the two require paths:

```php
require __DIR__.'/../one-blood-bank/vendor/autoload.php';
$app = require_once __DIR__.'/../one-blood-bank/bootstrap/app.php';
```

3. Copy `.htaccess` along with the public files.
4. Make sure the rest of the application stays **outside** `public_html`.
5. Uploaded files must still be reachable at `/storage/...`. Create a symlink if SSH is available:

```bash
ln -s /home/myuser/one-blood-bank/storage/app/public /home/myuser/public_html/storage
```

Without SSH, ask the host to create the symlink, or serve uploads through a small PHP proxy. The document-root method is strongly preferred.

</details>

#### 5. Configure `.env`

Create `.env` from `.env.example` and set at least:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=myuser_bloodbank
DB_USERNAME=myuser_obb
DB_PASSWORD=your-password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

Generate an app key locally (`php artisan key:generate --show`) and paste the value into `APP_KEY`, or run the command if cPanel Terminal is available.

#### 6. Run migrations

If cPanel **Terminal** is available:

```bash
cd ~/one-blood-bank
php artisan migrate --force
php artisan storage:link
```

If not, import the schema with phpMyAdmin:

1. Locally run `php artisan migrate --pretend` is **not** enough; instead export a migrated local database with `mysqldump`.
2. Import that `.sql` file into the cPanel database.
3. Open `/install` to finish branding and create the admin account. The installer runs migrations itself, so a fresh empty database usually works without phpMyAdmin.

#### 7. Set permissions

In File Manager, set these directories to `755` (or `775` if the host requires group write):

```
storage/
storage/app/
storage/app/public/
storage/framework/
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/
storage/logs/
bootstrap/cache/
```

Files inside can stay `644`.

#### 8. Finish

Visit `https://your-domain.com/install`, complete the wizard and delete any leftover installer access by keeping `storage/app/installed.lock` in place.

#### cPanel cron (optional)

Add a cron job if you enable scheduled tasks or queued jobs:

```
* * * * * /usr/local/bin/php /home/myuser/one-blood-bank/artisan schedule:run >> /dev/null 2>&1
```

Ask your host for the correct PHP CLI path (`which php` in Terminal).

---

### cPanel with SSH or Terminal

If your plan includes SSH, the process is close to a normal Linux deploy.

```bash
cd ~
git clone https://github.com/johirxofficial/one-blood-bank.git
cd one-blood-bank

composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Edit `.env` with the cPanel database credentials, then:

```bash
php artisan migrate --force
php artisan storage:link
npm ci && npm run build     # skip if public/build was uploaded
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set the document root to `~/one-blood-bank/public`, or symlink it:

```bash
ln -s ~/one-blood-bank/public ~/public_html
```

Then open `/install`.

---

### Linux VM / VPS (Ubuntu + Nginx)

Tested on Ubuntu 22.04 and 24.04. Commands assume a fresh server and a domain pointing at it.

#### 1. Install packages

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server unzip git curl \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl
```

Install Composer:

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Install Node.js 22 (build only):

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 2. Create the database

```bash
sudo mysql
```

```sql
CREATE DATABASE one_blood_bank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'obb'@'localhost' IDENTIFIED BY 'change-this-password';
GRANT ALL PRIVILEGES ON one_blood_bank.* TO 'obb'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Deploy the code

```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/johirxofficial/one-blood-bank.git
sudo chown -R $USER:www-data /var/www/one-blood-bank
cd /var/www/one-blood-bank

composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_DATABASE=one_blood_bank
DB_USERNAME=obb
DB_PASSWORD=change-this-password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Build and migrate:

```bash
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
```

#### 4. Permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

#### 5. Nginx site

Create `/etc/nginx/sites-available/one-blood-bank`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/one-blood-bank/public;

    index index.php;
    charset utf-8;
    client_max_body_size 12M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable it:

```bash
sudo ln -s /etc/nginx/sites-available/one-blood-bank /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 6. HTTPS

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

Certbot edits the Nginx config and sets up renewal automatically.

#### 7. Queue worker (optional)

If `QUEUE_CONNECTION=database`, create `/etc/systemd/system/obb-queue.service`:

```ini
[Unit]
Description=One Blood Bank queue worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=5
WorkingDirectory=/var/www/one-blood-bank
ExecStart=/usr/bin/php /var/www/one-blood-bank/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now obb-queue
```

#### 8. Scheduler (optional)

```bash
sudo crontab -u www-data -e
```

Add:

```
* * * * * cd /var/www/one-blood-bank && php artisan schedule:run >> /dev/null 2>&1
```

#### 9. Finish and cache

```bash
cd /var/www/one-blood-bank
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Visit `https://your-domain.com/install` and complete the wizard.

---

### Windows with XAMPP

This is the easiest local setup and also works for a small internal Windows server.

#### 1. Install XAMPP

Install XAMPP with PHP 8.3 or newer from <https://www.apachefriends.org/>. Start **Apache** and **MySQL** from the control panel.

Enable these extensions in `C:\xampp\php\php.ini` by removing the leading `;`:

```ini
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
```

Set a writable temp directory (fixes upload failures on Windows):

```ini
upload_tmp_dir = "C:\xampp\tmp"
sys_temp_dir  = "C:\xampp\tmp"
```

Restart Apache after editing `php.ini`.

#### 2. Place the project

Extract the project to `C:\xampp\htdocs\one-blood-bank`.

#### 3. Install dependencies

Open PowerShell in the project folder:

```powershell
composer install
copy .env.example .env
C:\xampp\php\php.exe artisan key:generate
```

If `php` is not on `PATH`, either add `C:\xampp\php` to `PATH` or prefix commands:

```powershell
C:\xampp\php\php.exe artisan migrate
```

#### 4. Create the database

Open <http://localhost/phpmyadmin>, create `one_blood_bank` with collation `utf8mb4_unicode_ci`, then set in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=one_blood_bank
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Build and migrate

```powershell
C:\xampp\php\php.exe artisan migrate
npm.cmd install
npm.cmd run build
```

> In PowerShell, use `npm.cmd` if script execution policy blocks `npm.ps1`.

#### 6. Option A — quick start with the built-in server

```powershell
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```

Open <http://127.0.0.1:8000/install>.

#### 7. Option B — Apache virtual host (recommended)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/one-blood-bank/public"
    ServerName onebloodbank.test
    <Directory "C:/xampp/htdocs/one-blood-bank/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts` (open Notepad as Administrator):

```
127.0.0.1 onebloodbank.test
```

Restart Apache and open <http://onebloodbank.test/install>.

#### 8. Storage link on Windows

```powershell
C:\xampp\php\php.exe artisan storage:link
```

On Windows this creates a junction. If it fails, run PowerShell as Administrator or create the junction manually:

```powershell
cmd /c mklink /J "C:\xampp\htdocs\one-blood-bank\public\storage" "C:\xampp\htdocs\one-blood-bank\storage\app\public"
```

---

### Windows Server / IIS

1. Install **PHP 8.3+ for IIS** (FastCGI) and enable the same extensions listed above.
2. Install the **URL Rewrite** module and **IIS Manager**.
3. Point the site's physical path to `C:\inetpub\one-blood-bank\public`.
4. Grant `IIS_IUSRS` and `IUSR` modify rights on `storage` and `bootstrap\cache`.
5. Create `web.config` in `public/` if it is missing:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="Laravel" stopProcessing="true">
          <match url="^(.*)$" />
          <conditions logicalGrouping="MatchAll">
            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
          </conditions>
          <action type="Rewrite" url="index.php" />
        </rule>
      </rules>
    </rewrite>
  </system.webServer>
</configuration>
```

6. Install Composer for Windows, run `composer install --no-dev --optimize-autoloader`, copy `.env.example` to `.env`, set `APP_URL`, database and mail values, then run `php artisan key:generate`, `php artisan migrate --force`, `php artisan storage:link` and `npm ci && npm run build`.
7. Open `/install` and finish.

---

### Plesk

1. Create a domain/subscription and a MySQL database in Plesk.
2. Use **Git** or **File Manager** to upload the project to `httpdocs/../one-blood-bank` (outside the web root).
3. In **Websites & Domains → Hosting Settings**, set the document root to `one-blood-bank/public`.
4. Select PHP 8.3+ and enable `curl`, `gd`, `intl`, `mbstring`, `pdo_mysql`, `zip`.
5. Use **PHP Composer** in Plesk or SSH to run `composer install --no-dev --optimize-autoloader`.
6. Build assets with Node via SSH, or upload a locally built `public/build`.
7. Run `php artisan key:generate`, configure `.env`, then `php artisan migrate --force` and `php artisan storage:link`.
8. Install a free Let's Encrypt certificate from **SSL/TLS Certificates**.
9. Open `/install`.

---

### Generic hosting checklist

Whatever the panel, verify:

- [ ] Document root points to `public/`
- [ ] PHP 8.3+ with required extensions
- [ ] MySQL database and user created
- [ ] `.env` present with `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`
- [ ] `APP_KEY` generated
- [ ] `vendor/` installed (upload it if the host has no Composer)
- [ ] `public/build/` present (build locally if the host has no Node.js)
- [ ] `storage/` and `bootstrap/cache/` writable
- [ ] `php artisan storage:link` completed
- [ ] `php artisan migrate --force` completed
- [ ] HTTPS enabled
- [ ] `/install` completed and `storage/app/installed.lock` exists

---

### Post-deployment checklist

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Then check:

1. Home page loads over HTTPS.
2. Login, registration and password reset work.
3. A test donor application can be submitted and approved.
4. Donor card PDF downloads as one page.
5. Avatar and logo uploads work.
6. English mode switches correctly.
7. Emails send (or verification is disabled).
8. `/admin` is reachable only by staff roles.
9. `storage/app/installed.lock` exists.
10. `APP_DEBUG=false` and directory listing is disabled.

After changing `.env` or config files in production, re-run `php artisan config:cache`. After changing routes or views, re-run `route:cache` / `view:cache`. To clear everything:

```bash
php artisan optimize:clear
```

---

## Updating an existing installation

Always back up the database and `storage/app` first.

```bash
cd /path/to/one-blood-bank

git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you uploaded files manually, repeat the same steps with your file manager and keep `.env`, `storage/app/public`, `storage/app/languages` and `storage/app/installed.lock` intact.

---

## Backups

Back up these items regularly:

- MySQL database (`mysqldump -u user -p one_blood_bank > backup.sql`)
- `storage/app/public` (avatars, logos, uploads)
- `storage/app/languages` (custom translations)
- `.env` (store securely, never in Git)

A simple daily cron backup:

```bash
0 2 * * * mysqldump -u obb -p'password' one_blood_bank | gzip > /home/backups/obb-$(date +\%F).sql.gz
```

Keep at least 7–30 days of backups and test restoring one occasionally.

---

## Troubleshooting

### 500 error after upload

- Check `storage/logs/laravel.log`.
- Ensure `storage/` and `bootstrap/cache/` are writable.
- Run `php artisan optimize:clear`.
- Confirm `APP_KEY` is set.

### “No application encryption key has been specified”

```bash
php artisan key:generate --force
```

### CSS or JS missing (unstyled site)

`public/build` is gitignored. Run `npm ci && npm run build` locally and upload the folder, or build on the server.

### Uploads fail on Windows

Set `upload_tmp_dir` and `sys_temp_dir` to a writable folder such as `C:\xampp\tmp` in `php.ini`, then restart Apache/PHP.

### Images return 404

```bash
php artisan storage:link
```

On shared hosting without SSH, ask the host to symlink `storage/app/public` to `public/storage`.

### PDF card spills onto a second page

Clear the Dompdf font cache:

```bash
rm -rf storage/app/dompdf-fonts/*
```

Then regenerate the card.

### Emails are not delivered

- Verify SMTP credentials in **Admin → Settings → Mail**.
- Check `storage/logs/laravel.log`.
- Some hosts block outbound SMTP; use the host's mail server or an external provider.
- If you do not need verification, disable it in **Admin → Settings → Donors**.

### Installer says the site is already installed

Remove `storage/app/installed.lock` **only** on a development copy, or empty the `users` table. Never do this on a live site with real accounts.

### Location dropdown is empty

Set the correct **Location country code** in **Admin → Settings → Others** and import that country's hierarchy. Free-text addresses still work without an import.

### Language file is ignored

Check the locale code matches the active language exactly (for example `pt_BR`, not `pt-br`) and that the JSON is valid. Clear caches with `php artisan optimize:clear`.

---

## Security notes

- Set `APP_DEBUG=false` in production.
- Use HTTPS everywhere.
- Keep `.env`, backups and `storage/app/installed.lock` out of version control.
- Use strong, unique database and admin passwords.
- Restrict `/admin` to trusted staff.
- Update dependencies periodically: `composer update` and `npm update`, then re-run tests.
- Review **Admin → Logs** for suspicious phone-reveal or login activity.
- Never commit `storage/app/public` uploads or customer data to a public repository.

---

## Contributing

Issues and pull requests are welcome at <https://github.com/johirxofficial/one-blood-bank>.

```bash
git checkout -b feature/my-improvement
# make changes
php artisan test
vendor/bin/pint --test
git commit -m "Describe the change"
git push origin feature/my-improvement
```

Please keep the Bengali-first UI, preserve English mode, and include tests for behaviour changes.

---

## License

One Blood Bank is open-source software licensed under the [MIT license](LICENSE). You may use, modify and distribute it, including commercially, provided the copyright notice is retained.

---

## Credits

- **Developer & maintainer:** [johirxofficial](https://github.com/johirxofficial)
- **Source code:** [github.com/johirxofficial/one-blood-bank](https://github.com/johirxofficial/one-blood-bank)
- Built with [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com), [Dompdf](https://github.com/dompdf/dompdf), [Simple QrCode](https://github.com/SimpleSoftwareIO/simple-qrcode) and the open-source community.
- Bengali donor-card font: [Hind Siliguri](https://fonts.google.com/specimen/Hind+Siliguri) (SIL Open Font License).

If you deploy this project, a link back to the repository is appreciated but not required.
