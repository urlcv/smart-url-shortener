<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Preview — {{ $domain }} | URLCV</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                primary: { 50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e' }
            }}}
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-12">

    <div class="w-full max-w-md space-y-5">

        {{-- URLCV branding --}}
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-gray-600 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                URLCV Short Link
            </a>
        </div>

        {{-- Main card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">You're about to visit</p>
                <p class="text-lg font-semibold text-gray-900 break-all">{{ $domain }}</p>
                <p class="mt-1 text-xs text-gray-400 break-all leading-relaxed">{{ $link->destination_url }}</p>
            </div>

            {{-- Trust score --}}
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <span class="text-sm text-gray-600">Trust score</span>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold {{ $link->trust_score >= 70 ? 'text-emerald-700' : ($link->trust_score >= 40 ? 'text-amber-700' : 'text-rose-700') }}">
                        {{ $link->trust_score >= 70 ? 'Trusted' : ($link->trust_score >= 40 ? 'Caution' : 'Risky') }}
                    </span>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold text-white {{ $link->trust_score >= 70 ? 'bg-emerald-500' : ($link->trust_score >= 40 ? 'bg-amber-500' : 'bg-rose-500') }}">
                        {{ $link->trust_score }}
                    </span>
                </div>
            </div>

            {{-- Safety checks --}}
            <div class="px-6 py-4 space-y-2 border-b border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Safety checks</p>
                @foreach ($checks as $check)
                    <div class="flex items-center gap-2.5 text-xs">
                        @if ($check['status'] === 'pass')
                            <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        @elseif ($check['status'] === 'warn')
                            <span class="shrink-0 w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-[10px] font-bold">!</span>
                        @else
                            <span class="shrink-0 w-5 h-5 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-[10px]">✗</span>
                        @endif
                        <span class="text-gray-700">{{ $check['label'] }}</span>
                        <span class="text-gray-400">— {{ $check['detail'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Continue button --}}
            <div class="px-6 py-5">
                <a href="{{ route('short-link.go', $link->slug) }}"
                   class="block w-full text-center py-3 px-4 rounded-xl font-semibold text-sm text-white transition-colors {{ $link->trust_score >= 40 ? 'bg-primary-600 hover:bg-primary-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                    Continue to {{ $domain }}
                </a>
                <p class="mt-3 text-center text-[11px] text-gray-400">
                    This preview page protects you from unexpected destinations.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center space-y-2">
            <p class="text-xs text-gray-400">
                {{ $link->click_count }} {{ $link->click_count === 1 ? 'click' : 'clicks' }} so far
                · Created {{ $link->created_at->diffForHumans() }}
            </p>
            <a href="{{ url('/tools/smart-url-shortener') }}" class="text-xs text-primary-600 hover:text-primary-800 font-medium transition-colors">
                Create your own short link →
            </a>
        </div>
    </div>

</body>
</html>
