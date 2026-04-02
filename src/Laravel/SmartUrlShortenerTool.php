<?php

declare(strict_types=1);

namespace URLCV\SmartUrlShortener\Laravel;

use App\Tools\Contracts\ToolInterface;

class SmartUrlShortenerTool implements ToolInterface
{
    public function slug(): string
    {
        return 'smart-url-shortener';
    }

    public function name(): string
    {
        return 'URL Shortener';
    }

    public function summary(): string
    {
        return 'Create short, trustworthy links with built-in safety checks, QR codes, and click tracking.';
    }

    public function descriptionMd(): ?string
    {
        return <<<'MD'
## URL Shortener

Create short, branded links at **urlcv.com/l/** with built-in trust and safety.

Every link shows a **preview page** before redirecting — visitors always see where they're going. This protects against phishing, hidden redirects, and link abuse.

### Features
- **Short branded links** — `urlcv.com/l/your-slug`
- **Safety checks** — HTTPS verification, phishing detection, redirect chain blocking
- **Preview page** — every link shows the destination before redirecting
- **QR code** — auto-generated for every link
- **Click tracking** — total clicks, unique visitors, device breakdown
- **Custom slugs** — choose your own or auto-generate
- **Anti-abuse** — rate limiting, honeypot bot protection, blocklists
MD;
    }

    public function categories(): array
    {
        return ['productivity', 'marketing'];
    }

    public function tags(): array
    {
        return ['links', 'url', 'shortener', 'smart-link', 'qr', 'routing', 'utm', 'trust', 'security'];
    }

    public function inputSchema(): array
    {
        return [];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function frontendView(): ?string
    {
        return 'smart-url-shortener::smart-url-shortener';
    }

    public function rateLimitPerMinute(): int
    {
        return 60;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 60;
    }
}

