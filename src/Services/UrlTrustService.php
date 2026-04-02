<?php

declare(strict_types=1);

namespace URLCV\SmartUrlShortener\Services;

class UrlTrustService
{
    private const BLOCKED_SHORTENERS = [
        'bit.ly', 'bitly.com', 'tinyurl.com', 't.co', 'is.gd', 'ow.ly',
        'buff.ly', 'goo.gl', 'rb.gy', 'shorturl.at', 'cutt.ly', 'rebrand.ly',
        'tiny.cc', 'v.gd', 'lnkd.in', 'surl.li', 'qr.ae',
    ];

    private const LOOKALIKE_PATTERNS = [
        'paypa1', 'g00gle', 'amaz0n', 'micros0ft', 'faceb00k', 'app1e',
        'netfl1x', 'l1nkedin', 'tw1tter', 'drop8ox', 'go0gle',
    ];

    public function isBlocked(string $url): string|false
    {
        $parsed = parse_url($url);
        if (! $parsed || ! isset($parsed['host'])) {
            return 'Invalid URL.';
        }

        $scheme = strtolower($parsed['scheme'] ?? '');
        if (! in_array($scheme, ['http', 'https'])) {
            return 'Only HTTP and HTTPS URLs are allowed.';
        }

        $host = strtolower($parsed['host']);

        // Block self-referencing
        if (str_contains($host, 'urlcv.com') || str_contains($host, 'urlcv.on-forge.com')) {
            return 'Cannot shorten URLCV links.';
        }

        // Block IP addresses
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return 'IP address URLs are not allowed for safety reasons.';
        }

        // Block other shorteners
        foreach (self::BLOCKED_SHORTENERS as $shortener) {
            if ($host === $shortener || str_ends_with($host, '.' . $shortener)) {
                return 'Shortening another shortener link is not allowed.';
            }
        }

        // Block obvious lookalike/phishing domains
        foreach (self::LOOKALIKE_PATTERNS as $pattern) {
            if (str_contains($host, $pattern)) {
                return 'This URL resembles a known brand and may be a phishing attempt.';
            }
        }

        return false;
    }

    public function computeTrustScore(string $url): int
    {
        $score = 50;
        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');
        $scheme = strtolower($parsed['scheme'] ?? '');

        // HTTPS
        $score += ($scheme === 'https') ? 15 : -15;

        // Well-known domains get a boost
        $trustedTlds = ['.gov', '.edu', '.ac.uk', '.gov.uk'];
        foreach ($trustedTlds as $tld) {
            if (str_ends_with($host, $tld)) {
                $score += 15;
                break;
            }
        }

        // Suspicious query parameters
        $suspiciousParams = ['redirect', 'redir', 'goto', 'next', 'url', 'link', 'continue', 'return_to', 'callback'];
        parse_str($parsed['query'] ?? '', $params);
        foreach (array_keys($params) as $key) {
            if (in_array(strtolower($key), $suspiciousParams)) {
                $score -= 10;
                break;
            }
        }

        // Very long URLs are suspicious
        if (strlen($url) > 500) {
            $score -= 5;
        }

        // Number substitutions in domain (lookalike signal)
        $domainWithoutTld = preg_replace('/\.[a-z]+$/', '', $host);
        if ($domainWithoutTld && preg_match('/[a-z][0-9]|[0-9][a-z]/', $domainWithoutTld)) {
            $score -= 10;
        }

        return max(0, min(100, $score));
    }

    public function getTrustChecks(string $url): array
    {
        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');
        $scheme = strtolower($parsed['scheme'] ?? '');
        $isHttps = $scheme === 'https';

        parse_str($parsed['query'] ?? '', $params);
        $suspiciousParams = ['redirect', 'redir', 'goto', 'next', 'url', 'link', 'continue', 'return_to', 'callback'];
        $hasSuspicious = ! empty(array_intersect(array_map('strtolower', array_keys($params)), $suspiciousParams));

        $isShortener = false;
        foreach (self::BLOCKED_SHORTENERS as $s) {
            if ($host === $s || str_ends_with($host, '.' . $s)) {
                $isShortener = true;
                break;
            }
        }

        $isLookalike = false;
        foreach (self::LOOKALIKE_PATTERNS as $p) {
            if (str_contains($host, $p)) {
                $isLookalike = true;
                break;
            }
        }

        return [
            ['label' => 'HTTPS', 'detail' => $isHttps ? 'Destination uses HTTPS' : 'No HTTPS — lower trust', 'status' => $isHttps ? 'pass' : 'fail'],
            ['label' => 'Query params', 'detail' => $hasSuspicious ? 'Suspicious redirect parameters' : 'Clean parameters', 'status' => $hasSuspicious ? 'warn' : 'pass'],
            ['label' => 'Redirect chain', 'detail' => $isShortener ? 'Points to another shortener' : 'No redirect chain', 'status' => $isShortener ? 'fail' : 'pass'],
            ['label' => 'Domain', 'detail' => $host, 'status' => $isLookalike ? 'fail' : 'pass'],
            ['label' => 'Lookalike check', 'detail' => $isLookalike ? 'Resembles a known brand' : 'No lookalike patterns', 'status' => $isLookalike ? 'fail' : 'pass'],
        ];
    }

    public function detectDeviceType(string $userAgent): string
    {
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        if (str_contains($ua, 'mozilla') || str_contains($ua, 'chrome') || str_contains($ua, 'safari') || str_contains($ua, 'firefox')) {
            return 'desktop';
        }

        return 'unknown';
    }

    public function guessCountry(string $acceptLanguage): ?string
    {
        // Parse Accept-Language for region codes like en-GB, en-US, de-DE
        if (preg_match('/[a-z]{2}-([A-Z]{2})/', $acceptLanguage, $m)) {
            return $m[1];
        }

        return null;
    }
}
