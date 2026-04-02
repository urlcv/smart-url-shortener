<?php

declare(strict_types=1);

namespace URLCV\SmartUrlShortener\Laravel;

use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use URLCV\SmartUrlShortener\Services\UrlTrustService;

class SmartUrlShortenerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'smart-url-shortener');

        $this->registerCreateRoute();
        $this->registerStatsRoute();
        $this->registerPreviewRoute();
        $this->registerRedirectRoute();
    }

    // ── Create short link ────────────────────────────────────────────────

    private function registerCreateRoute(): void
    {
        Route::post(
            '/tools/smart-url-shortener/create',
            function (Request $request) {
                // Honeypot: if filled, return fake success
                if ($request->filled('website_url')) {
                    return response()->json(['success' => true, 'slug' => 'abc123', 'short_url' => url('/l/abc123')]);
                }

                $validated = $request->validate([
                    'url'  => ['required', 'url', 'max:2048'],
                    'slug' => ['nullable', 'string', 'regex:/^[a-z0-9][a-z0-9-]{1,30}[a-z0-9]$/'],
                    'title' => ['nullable', 'string', 'max:255'],
                ]);

                $url = $validated['url'];

                // Trust & abuse checks
                $trustService = new UrlTrustService();
                $blocked = $trustService->isBlocked($url);
                if ($blocked) {
                    return response()->json(['success' => false, 'message' => $blocked], 422);
                }

                // Slug
                $slug = $validated['slug'] ?? null;
                if ($slug) {
                    if (ShortLink::where('slug', $slug)->exists()) {
                        return response()->json(['success' => false, 'message' => 'That slug is already taken. Try another.'], 422);
                    }
                } else {
                    $slug = ShortLink::generateSlug();
                }

                $trustScore = $trustService->computeTrustScore($url);
                $checks = $trustService->getTrustChecks($url);

                $link = ShortLink::create([
                    'slug'            => $slug,
                    'destination_url' => $url,
                    'title'           => $validated['title'] ?? null,
                    'trust_score'     => $trustScore,
                    'creator_ip_hash' => ShortLink::hashIp($request->ip()),
                ]);

                return response()->json([
                    'success'     => true,
                    'slug'        => $link->slug,
                    'short_url'   => $link->shortUrl(),
                    'preview_url' => $link->shortUrl(),
                    'trust_score' => $link->trust_score,
                    'checks'      => $checks,
                ]);
            }
        )
        ->middleware(['web', 'throttle:30,1'])
        ->name('tools.smart-url-shortener.create');
    }

    // ── Stats ────────────────────────────────────────────────────────────

    private function registerStatsRoute(): void
    {
        Route::get(
            '/l/{slug}/stats',
            function (string $slug) {
                $link = ShortLink::where('slug', $slug)->firstOrFail();

                $clicks = $link->clicks()->selectRaw('
                    COUNT(*) as total,
                    COUNT(DISTINCT ip_hash) as unique_clicks,
                    SUM(CASE WHEN is_preview = false THEN 1 ELSE 0 END) as redirects
                ')->first();

                $devices = $link->clicks()
                    ->where('is_preview', false)
                    ->selectRaw('device_type, COUNT(*) as count')
                    ->groupBy('device_type')
                    ->pluck('count', 'device_type');

                $countries = $link->clicks()
                    ->where('is_preview', false)
                    ->whereNotNull('country_code')
                    ->selectRaw('country_code, COUNT(*) as count')
                    ->groupBy('country_code')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->pluck('count', 'country_code');

                return response()->json([
                    'click_count'   => $link->click_count,
                    'unique_clicks' => $clicks->unique_clicks ?? 0,
                    'redirects'     => $clicks->redirects ?? 0,
                    'devices'       => $devices,
                    'countries'     => $countries,
                    'trust_score'   => $link->trust_score,
                    'created_at'    => $link->created_at->toIso8601String(),
                ]);
            }
        )
        ->middleware(['web', 'throttle:60,1'])
        ->name('tools.smart-url-shortener.stats');
    }

    // ── Preview / interstitial page ──────────────────────────────────────

    private function registerPreviewRoute(): void
    {
        Route::get(
            '/l/{slug}',
            function (Request $request, string $slug) {
                $link = ShortLink::where('slug', $slug)->firstOrFail();

                if ($link->is_blocked) {
                    abort(410, 'This link has been disabled.');
                }

                if ($link->isExpired()) {
                    return view('smart-url-shortener::expired', ['link' => $link]);
                }

                // Record preview hit
                $trustService = new UrlTrustService();
                ShortLinkClick::create([
                    'short_link_id' => $link->id,
                    'referrer'      => $request->header('Referer'),
                    'device_type'   => $trustService->detectDeviceType($request->userAgent() ?? ''),
                    'country_code'  => $trustService->guessCountry($request->header('Accept-Language', '')),
                    'ip_hash'       => ShortLink::hashIp($request->ip()),
                    'is_preview'    => true,
                    'clicked_at'    => now(),
                ]);

                $checks = $trustService->getTrustChecks($link->destination_url);
                $domain = parse_url($link->destination_url, PHP_URL_HOST);

                return view('smart-url-shortener::preview', [
                    'link'   => $link,
                    'domain' => $domain,
                    'checks' => $checks,
                ]);
            }
        )
        ->middleware(['web'])
        ->name('short-link.preview');
    }

    // ── Actual redirect ──────────────────────────────────────────────────

    private function registerRedirectRoute(): void
    {
        Route::get(
            '/l/{slug}/go',
            function (Request $request, string $slug) {
                $link = ShortLink::where('slug', $slug)->firstOrFail();

                if ($link->is_blocked) {
                    abort(410, 'This link has been disabled.');
                }

                if ($link->isExpired()) {
                    return redirect()->route('short-link.preview', $slug);
                }

                // Record redirect click
                $trustService = new UrlTrustService();
                ShortLinkClick::create([
                    'short_link_id' => $link->id,
                    'referrer'      => $request->header('Referer'),
                    'device_type'   => $trustService->detectDeviceType($request->userAgent() ?? ''),
                    'country_code'  => $trustService->guessCountry($request->header('Accept-Language', '')),
                    'ip_hash'       => ShortLink::hashIp($request->ip()),
                    'is_preview'    => false,
                    'clicked_at'    => now(),
                ]);

                $link->increment('click_count');

                return redirect()->away($link->destination_url, 302);
            }
        )
        ->middleware(['web', 'throttle:120,1'])
        ->name('short-link.go');
    }
}
