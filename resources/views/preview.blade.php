<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Link Preview — {{ $domain }} | URLCV</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col">

    {{-- Minimal header --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-14">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-gray-900 font-bold text-lg">
                <img src="{{ asset('urlcv-logo-icon-300.jpg') }}" alt="URLCV" class="w-7 h-7 rounded">
                urlcv
            </a>
        </div>
    </header>

    {{-- Main content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md space-y-5">

            {{-- Badge --}}
            <div class="text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-xs font-medium text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Link safety preview
                </span>
            </div>

            {{-- Main card --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Destination --}}
                <div class="px-6 pt-6 pb-5 border-b border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">You're about to visit</p>
                    <p class="text-xl font-bold text-gray-900">{{ $domain }}</p>
                    <p class="mt-2 text-xs text-gray-400 break-all leading-relaxed bg-gray-50 rounded-lg px-3 py-2 font-mono">{{ $link->destination_url }}</p>
                </div>

                {{-- Trust score --}}
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                    <span class="text-sm text-gray-600">Trust score</span>
                    <div class="flex items-center gap-2.5">
                        @php
                            $scoreColor = $link->trust_score >= 70 ? 'emerald' : ($link->trust_score >= 40 ? 'amber' : 'rose');
                            $scoreLabel = $link->trust_score >= 70 ? 'Trusted' : ($link->trust_score >= 40 ? 'Caution' : 'Risky');
                        @endphp
                        <span class="text-sm font-semibold text-{{ $scoreColor }}-700">{{ $scoreLabel }}</span>
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold text-white bg-{{ $scoreColor }}-500">
                            {{ $link->trust_score }}
                        </span>
                    </div>
                </div>

                {{-- Safety checks --}}
                <div class="px-6 py-4 space-y-2.5 border-b border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-3">Safety checks</p>
                    @foreach ($checks as $check)
                        <div class="flex items-center gap-2.5">
                            @if ($check['status'] === 'pass')
                                <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            @elseif ($check['status'] === 'warn')
                                <span class="shrink-0 w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-[10px] font-bold">!</span>
                            @else
                                <span class="shrink-0 w-5 h-5 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @endif
                            <span class="text-xs font-medium text-gray-700">{{ $check['label'] }}</span>
                            <span class="text-xs text-gray-400">{{ $check['detail'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Continue button --}}
                <div class="px-6 py-6">
                    <a href="{{ route('short-link.go', $link->slug) }}"
                       class="block w-full text-center py-3.5 px-4 rounded-xl font-semibold text-sm text-white transition-colors {{ $link->trust_score >= 40 ? 'bg-primary-600 hover:bg-primary-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                        Continue to {{ $domain }} →
                    </a>
                    <p class="mt-3 text-center text-[11px] text-gray-400 leading-relaxed">
                        This preview protects you by showing where the link goes before you visit.
                    </p>
                </div>
            </div>

            {{-- Footer info --}}
            <div class="text-center space-y-2">
                <p class="text-xs text-gray-400">
                    {{ number_format($link->click_count) }} {{ $link->click_count === 1 ? 'click' : 'clicks' }}
                    · Created {{ $link->created_at->diffForHumans() }}
                </p>
                <a href="{{ url('/tools/smart-url-shortener') }}" class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 font-medium transition-colors">
                    Create your own short link
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
    </main>

    {{-- Minimal footer --}}
    <footer class="border-t border-gray-100 py-4">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs text-gray-400">
                <a href="{{ url('/tools') }}" class="hover:text-gray-600 transition-colors">URLCV Tools</a>
                · Free tools for recruiters and hiring teams
            </p>
        </div>
    </footer>

</body>
</html>
