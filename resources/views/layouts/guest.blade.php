<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.app_name')) — {{ __('app.app_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Locale switch form in page header area -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased font-sans">
    <!-- Locale Switcher (guest pages) -->
    <div class="fixed top-4 end-4 z-50">
        <form method="POST" action="/locale">
            @csrf
            <div class="flex gap-1 bg-white rounded-lg shadow p-1">
                <button type="submit" name="locale" value="en"
                    class="px-3 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    EN
                </button>
                <button type="submit" name="locale" value="ar"
                    class="px-3 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    AR
                </button>
            </div>
        </form>
    </div>

    @yield('content')
</body>
</html>
