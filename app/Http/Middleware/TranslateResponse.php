<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the English UI dictionary to rendered HTML when the site locale is
 * English. The application is authored in Bengali; keeping the dictionary in
 * one file makes the English mode reviewable and avoids duplicating 50+ views.
 */
class TranslateResponse
{
    /**
     * @var array<string, array<string, string>>
     */
    private static array $dictionaries = [];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $locale = app()->getLocale();

        if ($locale === 'bn' || ! $this->isHtml($response)) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        $dictionary = $this->dictionary($locale);

        if ($dictionary === []) {
            return $response;
        }

        // Translate text nodes and common attributes, while leaving scripts,
        // styles, and long user-authored prose untouched. This prevents blog
        // posts and policy pages from turning into mixed-language fragments.
        $segments = preg_split('/(<[^>]*>)/u', $content, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$content];
        $insideRawText = false;

        foreach ($segments as $index => $segment) {
            if ($segment === '') {
                continue;
            }

            if (str_starts_with($segment, '<')) {
                if (preg_match('/^<\s*(script|style)\b/i', $segment)) {
                    $insideRawText = true;
                } elseif (preg_match('/^<\s*\/\s*(script|style)\b/i', $segment)) {
                    $insideRawText = false;
                } elseif (! str_starts_with($segment, '<!--')) {
                    $segments[$index] = $this->translateAttributes($segment, $dictionary);
                }

                continue;
            }

            if ($insideRawText || str_starts_with($segment, '<!--')) {
                continue;
            }

            $segments[$index] = $this->translateText($segment, $dictionary);
        }

        $content = implode('', $segments);

        // Keep counters and dates consistent in English mode.
        if ($locale === 'en') {
            $content = strtr($content, [
                '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
                '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9',
            ]);
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * @param  array<string, string>  $dictionary
     */
    private function translateText(string $text, array $dictionary): string
    {
        if (! preg_match('/[\x{0980}-\x{09FF}]/u', $text)) {
            return $text;
        }

        $trimmed = trim($text);

        if ($trimmed === '') {
            return $text;
        }

        $normalized = (string) preg_replace('/\s+/u', ' ', $trimmed);

        // Dashboard activity lines prefix the description with a dash.
        if (preg_match('/^[—–]\s+(.+)$/u', $normalized, $match)) {
            $translated = $this->translateText($match[1], $dictionary);

            if ($translated !== $match[1]) {
                return str_replace($trimmed, '— '.$translated, $text);
            }
        }

        if (app()->getLocale() === 'en') {
            foreach ($this->patterns() as $pattern => $replacement) {
                if (preg_match($pattern, $normalized)) {
                    return (string) preg_replace($pattern, $replacement, $normalized);
                }
            }
        }

        if (isset($dictionary[$normalized])) {
            return str_replace($trimmed, $dictionary[$normalized], $text);
        }

        // Short labels often contain counts or names ("মোট ১২ জন"), so allow
        // phrase replacement there. Longer prose must match sentence by
        // sentence to avoid replacing words inside user-generated content.
        if (strlen($trimmed) <= 60) {
            foreach ($dictionary as $bengali => $english) {
                if (! str_contains($trimmed, $bengali)) {
                    continue;
                }

                $pattern = '/(?<![\x{0980}-\x{09FF}])'.preg_quote($bengali, '/').'(?![\x{0980}-\x{09FF}])/u';
                $trimmed = (string) preg_replace_callback($pattern, fn (): string => $english, $trimmed);
            }

            return str_replace(trim($text), $trimmed, $text);
        }

        // Titles and the footer use "prefix — site name" patterns. Translate
        // each side only when the Bengali part is an exact dictionary entry.
        if (preg_match('/\s+[—–]\s+/u', $normalized)) {
            $parts = preg_split('/\s+[—–]\s+/u', $normalized) ?: [];
            $translated = [];
            $matched = count($parts) > 1;

            foreach ($parts as $part) {
                if (! preg_match('/[\x{0980}-\x{09FF}]/u', $part)) {
                    $translated[] = $part;

                    continue;
                }

                if (! isset($dictionary[$part])) {
                    $matched = false;

                    break;
                }

                $translated[] = $dictionary[$part];
            }

            if ($matched) {
                return implode(' — ', $translated);
            }
        }

        $sentences = preg_split('/(?<=[।!?])\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($sentences) < 2) {
            return $text;
        }

        $translated = [];

        foreach ($sentences as $sentence) {
            if (! isset($dictionary[$sentence])) {
                return $text;
            }

            $translated[] = $dictionary[$sentence];
        }

        return implode(' ', $translated);
    }

    /**
     * @param  array<string, string>  $dictionary
     */
    private function translateAttributes(string $tag, array $dictionary): string
    {
        if ($dictionary === [] || ! str_contains($tag, '=')) {
            return $tag;
        }

        return (string) preg_replace_callback(
            '/\b(placeholder|title|aria-label|alt|value|content|onsubmit|onclick)="([^"]*)"/iu',
            function (array $match) use ($dictionary): string {
                $value = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $normalized = trim((string) preg_replace('/\s+/u', ' ', $value));
                $translated = $this->translatePhrase($normalized, $dictionary);

                if ($translated === null && in_array(strtolower($match[1]), ['onsubmit', 'onclick'], true)) {
                    $translated = $this->translateInline($normalized, $dictionary);
                }

                if ($translated === null) {
                    return $match[0];
                }

                return $match[1].'="'.htmlspecialchars(
                    $translated,
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8',
                ).'"';
            },
            $tag,
        );
    }

    /**
     * Translates an attribute value without touching user-authored text.
     *
     * @param  array<string, string>  $dictionary
     */
    private function translatePhrase(string $normalized, array $dictionary): ?string
    {
        if (! preg_match('/[\x{0980}-\x{09FF}]/u', $normalized)) {
            return null;
        }

        if (isset($dictionary[$normalized])) {
            return $dictionary[$normalized];
        }

        if (app()->getLocale() === 'en') {
            foreach ($this->patterns() as $pattern => $replacement) {
                if (preg_match($pattern, $normalized)) {
                    return (string) preg_replace($pattern, $replacement, $normalized);
                }
            }
        }

        // SEO titles often use "site name — Bengali tagline" while the tagline
        // alone is the dictionary key.
        if (preg_match('/\s+[—–]\s+/u', $normalized)) {
            $parts = preg_split('/\s+[—–]\s+/u', $normalized) ?: [];
            $translated = [];
            $matched = count($parts) > 1;

            foreach ($parts as $part) {
                if (! preg_match('/[\x{0980}-\x{09FF}]/u', $part)) {
                    $translated[] = $part;

                    continue;
                }

                if (! isset($dictionary[$part])) {
                    $matched = false;

                    break;
                }

                $translated[] = $dictionary[$part];
            }

            if ($matched) {
                return implode(' — ', $translated);
            }
        }

        return null;
    }

    /**
     * Translates dictionary phrases embedded in inline JavaScript attributes
     * such as confirmation dialogs, without touching non-dictionary text.
     *
     * @param  array<string, string>  $dictionary
     */
    private function translateInline(string $value, array $dictionary): ?string
    {
        $translated = $value;
        $changed = false;

        foreach ($dictionary as $bengali => $english) {
            $pattern = '/(?<![\x{0980}-\x{09FF}])'.preg_quote($bengali, '/').'(?![\x{0980}-\x{09FF}])/u';
            $count = 0;
            $candidate = (string) preg_replace_callback(
                $pattern,
                fn (): string => $english,
                $translated,
                -1,
                $count,
            );

            if ($count > 0) {
                $translated = $candidate;
                $changed = true;
            }
        }

        return $changed ? $translated : null;
    }

    /**
     * Common count-bearing headings that cannot be stored as exact keys.
     *
     * @return array<string, string>
     */
    private function patterns(): array
    {
        return [
            '/^আবেদন যাচাই, অনুমোদন, বাতিল ও স্থগিত করুন। মোট ([০-৯0-9]+) জন।$/u' => 'Review, approve, reject and suspend applications. Total $1 people.',
            '/^মোট ([০-৯0-9]+) টি অনুরোধ। সম্পন্ন বা মেয়াদোত্তীর্ণ হিসেবে চিহ্নিত করুন।$/u' => 'Total $1 requests. Mark them as completed or expired.',
            '/^মোট ([০-৯0-9]+) টি রিভিউ। অপ্রকাশিত বা অপমানজনক রিভিউ লুকিয়ে রাখুন।$/u' => 'Total $1 reviews. Hide unpublished or abusive reviews.',
            '/^মোট ([০-৯0-9]+) জন রেজিস্টার্ড ইউজার।$/u' => 'Total $1 registered users.',
            '/^অ্যাডমিন, ম্যানেজার ও স্টাফ অ্যাকাউন্ট তৈরি ও পরিচালনা করুন। মোট ([০-৯0-9]+) জন।$/u' => 'Create and manage admin, manager and staff accounts. Total $1 people.',
            '/^মোট ([০-৯0-9]+) টি পোস্ট। প্রকাশিত পোস্ট পাবলিক ব্লগে দেখা যাবে।$/u' => 'Total $1 posts. Published posts appear on the public blog.',
            '/^ব্যানার, পপআন্ডার ও স্ক্রিপ্ট বিজ্ঞাপন যোগ করুন। মোট ([০-৯0-9]+) টি।$/u' => 'Add banner, popunder and script ads. Total $1 items.',
            '/^আমাদের সম্পর্কে, শর্তাবলী, গোপনীয়তা নীতি ইত্যাদি পেজ পরিচালনা করুন। মোট ([০-৯0-9]+) টি।$/u' => 'Manage pages such as About, Terms and Privacy Policy. Total $1 items.',
            '/^সিস্টেম থেকে পাঠানো সব বার্তার লগ। মোট ([০-৯0-9]+) টি।$/u' => 'Log of all messages sent by the system. Total $1 items.',
            '/^সিস্টেমের সব গুরুত্বপূর্ণ কার্যক্রমের রেকর্ড। মোট ([০-৯0-9]+) টি।$/u' => 'Record of all important system activity. Total $1 items.',
            '/^সিস্টেম ও অ্যাডমিনের পাঠানো সব ইমেইলের রেকর্ড। মোট ([০-৯0-9]+) টি।$/u' => 'Record of all emails sent by the system and admins. Total $1 items.',
            '/^([০-৯0-9]+) জন ভেরিফাইড ডোনার পাওয়া গেছে$/u' => '$1 verified donors found',
            '/^সাইট সেটিংস আপডেট করা হয়েছে।$/u' => 'Site settings updated.',
            '/^কাস্টম মেসেজ পাঠানো হয়েছে: ([০-৯0-9]+) জনকে — (.*)$/u' => 'Custom message sent to $1 people — $2',
            '/^ডোনার নিজের তথ্য আপডেট করেছেন।$/u' => 'Donor updated their own information.',
            '/^নতুন ডোনার আবেদন জমা হয়েছে।$/u' => 'New donor application submitted.',
            '/^সাইট ডোনেশন মুছে ফেলা: (.*)$/u' => 'Site donation deleted: $1',
            '/^সাইট ডোনেশন (.*): (.*)$/u' => 'Site donation $1: $2',
            '/^ব্লগ পোস্ট মুছে ফেলা: (.*)$/u' => 'Blog post deleted: $1',
            '/^ব্লগ পোস্ট আপডেট: (.*)$/u' => 'Blog post updated: $1',
            '/^ব্লগ পোস্ট তৈরি: (.*)$/u' => 'Blog post created: $1',
            '/^ডোনার অনুমোদন করা হয়েছে: (.*)$/u' => 'Donor approved: $1',
            '/^ডোনার স্থগিত করা হয়েছে: (.*)$/u' => 'Donor suspended: $1',
            '/^ডোনার প্রোফাইল মুছে ফেলা হয়েছে: (.*)$/u' => 'Donor profile deleted: $1',
            '/^ডোনার বাতিল: (.*)$/u' => 'Donor rejected: $1',
            '/^রক্তের অনুরোধ মুছে ফেলা হয়েছে।$/u' => 'Blood request deleted.',
            '/^রক্তের অনুরোধ স্ট্যাটাস: (.*)$/u' => 'Blood request status: $1',
            '/^পেজ মুছে ফেলা হয়েছে।$/u' => 'Page deleted.',
            '/^পেজ আপডেট: (.*)$/u' => 'Page updated: $1',
            '/^নতুন পেজ: (.*)$/u' => 'New page: $1',
            '/^বিজ্ঞাপন মুছে ফেলা হয়েছে।$/u' => 'Ad deleted.',
            '/^বিজ্ঞাপন আপডেট: (.*)$/u' => 'Ad updated: $1',
            '/^নতুন বিজ্ঞাপন: (.*)$/u' => 'New ad: $1',
            '/^স্টাফ মুছে ফেলা হয়েছে: (.*)$/u' => 'Staff deleted: $1',
            '/^স্টাফ আপডেট: (.*)$/u' => 'Staff updated: $1',
            '/^নতুন স্টাফ যোগ: (.*)$/u' => 'New staff added: $1',
            '/^ইউজার মুছে ফেলা হয়েছে: (.*)$/u' => 'User deleted: $1',
            '/^ইউজার আপডেট: (.*)$/u' => 'User updated: $1',
            '/^রেটিং স্ট্যাটাস: (.*)$/u' => 'Rating status: $1',
            '/^একটি রেটিং মুছে ফেলা হয়েছে।$/u' => 'A rating was deleted.',
            '/^সক্রিয় ([০-৯0-9]+) টি অনুরোধ$/u' => '$1 active requests',
            '/^-এ জমা হবে। সর্বোচ্চ ([০-৯0-9,]+) জন প্রাপককে একবারে পাঠানো যাবে।$/u' => 'is stored. Up to $1 recipients can be sent at once.',
            '/^ডোনার: (.*) — (.*)$/u' => 'Donor: $1 — $2',
            '/^এডিট পোস্ট: (.*) — (.*)$/u' => 'Edit post: $1 — $2',
            '/^রক্তের গ্রুপ (.*)$/u' => 'Blood group $1',
        ];
    }

    private function isHtml(Response $response): bool
    {
        $contentType = (string) $response->headers->get('Content-Type', '');

        return $contentType === '' || str_contains(strtolower($contentType), 'text/html');
    }

    /**
     * @return array<string, string>
     */
    private function dictionary(string $locale): array
    {
        if (isset(self::$dictionaries[$locale])) {
            return self::$dictionaries[$locale];
        }

        if (preg_match('/^[a-z]{2,8}([_-][A-Za-z]{2,8})?$/', $locale) !== 1) {
            return self::$dictionaries[$locale] = [];
        }

        $paths = [
            lang_path($locale.'/static.json'),
            lang_path($locale.'.json'),
            storage_path('app/languages/'.$locale.'.json'),
        ];

        $dictionary = [];

        foreach ($paths as $path) {
            if (! is_file($path)) {
                continue;
            }

            $decoded = json_decode((string) file_get_contents($path), true);

            if (! is_array($decoded)) {
                continue;
            }

            foreach ($decoded as $source => $translated) {
                if (is_string($source) && is_string($translated) && $source !== '') {
                    $dictionary[$source] = $translated;
                }
            }
        }

        // Replace longer phrases first so a short label cannot split a
        // sentence translation into a mixed-language fragment.
        uksort($dictionary, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return self::$dictionaries[$locale] = $dictionary;
    }
}
