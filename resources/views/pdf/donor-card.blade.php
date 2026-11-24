<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $siteName }} — Donor Card</title>
    <style>
        @page { margin: 0; }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            width: 148mm;
        }

        body {
            font-family: 'HindSiliguri', 'DejaVu Sans', sans-serif;
            color: #0f172a;
            font-size: 0;
            line-height: 0;
        }

        .en { font-family: 'DejaVu Sans', sans-serif; }

        .card {
            width: 147mm;
            margin: 0.5mm;
            overflow: hidden;
            background: #ffffff;
            border: 0.35mm solid #b91c1c;
            border-radius: 2.5mm;
            font-size: 8px;
            line-height: 1.2;
        }

        .band {
            padding: 3.2mm 5mm;
            background: #b91c1c;
            color: #ffffff;
            border-radius: 2.1mm 2.1mm 0 0;
        }

        .brand-table { width: 100%; border-collapse: collapse; }
        .brand-table td { padding: 0; vertical-align: middle; }

        .mark-cell { width: 14mm; }

        .mark {
            width: 12mm;
            height: 12mm;
            border-radius: 2.5mm;
            background: #ffffff;
            color: #b91c1c;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 0.4px;
            border-collapse: collapse;
        }

        .mark td {
            width: 12mm;
            height: 12mm;
            text-align: center;
            vertical-align: middle;
        }

        .brand-name {
            font-size: 14px;
            font-weight: bold;
            line-height: 1.1;
            margin: 0;
        }

        .brand-tagline {
            margin: 1mm 0 0;
            font-size: 7px;
            color: #fecaca;
            line-height: 1.2;
        }

        .verified-cell { width: 32mm; text-align: right; }

        .verified {
            display: inline-block;
            padding: 1.5mm 3mm;
            border-radius: 4mm;
            background: #ffffff;
            color: #b91c1c;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6.5px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .main {
            min-height: 62mm;
            padding: 3.5mm 5mm 2mm;
        }

        .layout { width: 100%; border-collapse: collapse; }
        .layout > tbody > tr > td { vertical-align: top; padding: 0; }

        .photo-cell { width: 28mm; padding-right: 4mm !important; }

        .photo {
            width: 24mm;
            height: 29mm;
            overflow: hidden;
            border: 0.9mm solid #fecaca;
            border-radius: 2mm;
            background: #fff1f2;
            text-align: center;
        }

        .photo img {
            width: 24mm;
            height: 29mm;
        }

        .initials {
            width: 24mm;
            height: 29mm;
            border-collapse: collapse;
            color: #b91c1c;
            font-family: 'HindSiliguri', 'DejaVu Sans', sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .initials td {
            width: 24mm;
            height: 29mm;
            text-align: center;
            vertical-align: middle;
        }

        .blood-box {
            margin-top: 2.5mm;
            padding: 1.2mm 0;
            border: 0.35mm solid #fecaca;
            border-radius: 2mm;
            background: #fff1f2;
            text-align: center;
        }

        .blood-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 5.5px;
            font-weight: bold;
            letter-spacing: 0.4px;
            color: #9f1239;
            text-transform: uppercase;
        }

        .blood-value {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 17px;
            font-weight: bold;
            line-height: 1.05;
            color: #b91c1c;
        }

        .info-cell { padding-right: 4mm !important; }

        .donor-name {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.15;
            color: #0f172a;
        }

        .username {
            margin: 1mm 0 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: #64748b;
        }

        .location {
            margin: 1.8mm 0 0;
            font-size: 8px;
            line-height: 1.35;
            color: #334155;
        }

        .rule {
            height: 0.35mm;
            margin: 2.6mm 0;
            background: #e2e8f0;
        }

        .details { width: 100%; border-collapse: collapse; }
        .details td {
            width: 50%;
            padding: 0 2mm 1.8mm 0;
            vertical-align: top;
        }

        .label {
            display: block;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 5.5px;
            font-weight: bold;
            letter-spacing: 0.35px;
            text-transform: uppercase;
            color: #94a3b8;
            line-height: 1.1;
        }

        .value {
            display: block;
            margin-top: 0.4mm;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.15;
            color: #0f172a;
        }

        .contact-line {
            margin: 0.4mm 0 0;
            font-size: 7.5px;
            line-height: 1.2;
            color: #334155;
        }

        .contact-label { font-weight: bold; color: #b91c1c; }

        .qr-cell { width: 23mm; text-align: center; }

        .qr-box {
            width: 22mm;
            height: 22mm;
            padding: 0.8mm;
            border: 0.35mm solid #e2e8f0;
            border-radius: 1.5mm;
            background: #ffffff;
        }

        .qr-box svg, .qr-box img { width: 20.4mm; height: 20.4mm; }

        .donor-id {
            margin: 1.2mm 0 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 5.8px;
            font-weight: bold;
            color: #475569;
            letter-spacing: 0.2px;
        }

        .verified-note {
            margin: 1mm 0 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 5.5px;
            font-weight: bold;
            color: #047857;
        }

        .footer {
            padding: 2mm 5mm;
            background: #0f172a;
            color: #ffffff;
            border-radius: 0 0 2.1mm 2.1mm;
        }

        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { padding: 0; vertical-align: middle; }
        .footer-left { font-size: 7px; line-height: 1.3; }
        .footer-right {
            text-align: right;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6.5px;
            line-height: 1.3;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    @php
        $phones = collect([[
            'label' => 'Mobile',
            'phone' => $donor->user->phone,
        ]])->merge($donor->contacts->map(fn ($contact) => [
            'label' => $contact->displayLabel(),
            'phone' => $contact->phone,
        ]))->filter(fn ($phone) => filled($phone['phone']))
            ->unique(fn ($phone) => preg_replace('/\D+/', '', (string) $phone['phone']) ?? '')
            ->take(3)
            ->values();

        $availability = $donor->is_available
            ? 'Available now'
            : ($donor->available_from?->format('d M Y') ?? '—');

        $memberSince = $donor->verified_at?->format('M Y') ?? $donor->created_at?->format('M Y') ?? '—';
        $siteHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'thebloodbank';
        $donorId = 'TBB-'.str_pad((string) $donor->id, 6, '0', STR_PAD_LEFT);
    @endphp

    <div class="card">
        <div class="band">
            <table class="brand-table">
                <tr>
                    <td class="mark-cell">
                        <table class="mark"><tr><td>{{ $siteMark ?: 'BB' }}</td></tr></table>
                    </td>
                    <td>
                        <p class="brand-name">{{ $siteName }}</p>
                        <p class="brand-tagline">{{ $siteTagline }}</p>
                    </td>
                    <td class="verified-cell">
                        <span class="verified">✓ VERIFIED DONOR</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="main">
            <table class="layout">
                <tr>
                    <td class="photo-cell">
                        <div class="photo">
                            @if ($photo)
                                <img src="{{ $photo }}" alt="">
                            @else
                                <table class="initials"><tr><td>{{ $initials }}</td></tr></table>
                            @endif
                        </div>

                        <div class="blood-box">
                            <div class="blood-label">Blood group</div>
                            <div class="blood-value">{{ $donor->blood_group }}</div>
                        </div>
                    </td>

                    <td class="info-cell">
                        <p class="donor-name">{{ $donor->user->name }}</p>
                        <p class="username">@ {{ $donor->user->username }}</p>
                        <p class="location">{{ $donor->locationLabel() ?: '—' }}</p>

                        <div class="rule"></div>

                        <table class="details">
                            <tr>
                                <td>
                                    <span class="label">Total donations</span>
                                    <span class="value">{{ $donor->total_donations }} times</span>
                                </td>
                                <td>
                                    <span class="label">Last donation</span>
                                    <span class="value">{{ $donor->last_donation_date?->format('d M Y') ?? '—' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Available from</span>
                                    <span class="value">{{ $availability }}</span>
                                </td>
                                <td>
                                    <span class="label">Member since</span>
                                    <span class="value">{{ $memberSince }}</span>
                                </td>
                            </tr>
                        </table>

                        <div class="rule"></div>

                        <span class="label">Contact numbers</span>
                        @foreach ($phones as $phone)
                            <div class="contact-line">
                                <span class="contact-label">{{ $phone['label'] }}:</span>
                                <span class="en">{{ $phone['phone'] }}</span>
                            </div>
                        @endforeach
                    </td>

                    <td class="qr-cell">
                        <div class="qr-box"><img src="{{ $qrDataUri }}" alt="QR"></div>
                        <p class="donor-id">{{ $donorId }}</p>
                        <p class="verified-note">✓ VERIFIED</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        <strong>Emergency use only.</strong> Scan the QR code to verify this donor profile.
                    </td>
                    <td class="footer-right">
                        {{ $siteHost }}<br>
                        {{ $donor->user->profileUrl() }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
