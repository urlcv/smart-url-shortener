<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired | URLCV</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-14">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-gray-900 font-bold text-lg">
                <img src="{{ asset('urlcv-logo-icon-300.jpg') }}" alt="URLCV" class="w-7 h-7 rounded">
                urlcv
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm text-center space-y-5">
            <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="space-y-2">
                <h1 class="text-lg font-semibold text-gray-900">This link has expired</h1>
                <p class="text-sm text-gray-500">The short link <span class="font-mono text-gray-700">urlcv.com/l/{{ $link->slug }}</span> is no longer active.</p>
            </div>
            <a href="{{ url('/tools/smart-url-shortener') }}" class="inline-block px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                Create a new short link
            </a>
        </div>
    </main>

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
