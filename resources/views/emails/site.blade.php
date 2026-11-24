<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:'Hind Siliguri',Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#d32f2f;padding:20px 28px;">
                            <p style="margin:0;font-size:18px;font-weight:800;color:#ffffff;">{{ $footerNote ?: 'One Blood Bank' }}</p>
                            <p style="margin:4px 0 0;font-size:11px;letter-spacing:1.5px;color:#ffd9d9;">VERIFIED BLOOD DONOR NETWORK</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#111827;">{{ $heading }}</h1>

                            @foreach ($lines as $line)
                                <p style="margin:0 0 12px;font-size:14px;line-height:1.7;color:#374151;">{{ $line }}</p>
                            @endforeach

                            @if ($actionUrl)
                                <table role="presentation" cellspacing="0" cellpadding="0" style="margin:24px 0 8px;">
                                    <tr>
                                        <td style="background:#d32f2f;border-radius:10px;">
                                            <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;">{{ $actionLabel ?: 'বিস্তারিত দেখুন' }}</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:20px 0 0;font-size:12px;line-height:1.6;color:#6b7280;">
                                এই ইমেইলটি {{ \App\Models\Setting::get('site.name', 'One Blood Bank') }} থেকে স্বয়ংক্রিয়ভাবে পাঠানো হয়েছে।
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 28px;font-size:11px;color:#9ca3af;">
                            © {{ now()->year }} {{ \App\Models\Setting::get('site.name', 'One Blood Bank') }} · বাংলাদেশ
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
