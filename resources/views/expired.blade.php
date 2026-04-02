<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired | URLCV</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm text-center space-y-4">
        <div class="mx-auto w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-xl">⏱</div>
        <h1 class="text-lg font-semibold text-gray-900">This link has expired</h1>
        <p class="text-sm text-gray-500">The short link <span class="font-mono">urlcv.com/l/{{ $link->slug }}</span> is no longer active.</p>
        <a href="{{ url('/tools/smart-url-shortener') }}" class="inline-block mt-4 px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
            Create a new short link
        </a>
    </div>
</body>
</html>
