<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.app_name')) — {{ __('app.app_name') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#6366f1', 600: '#4f46e5' }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Locale-aware JS config -->
    <script>
        window.appLocale = "{{ app()->getLocale() }}";
        window.appDir = "{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}";
        window.csrfToken = "{{ csrf_token() }}";
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-all; }
        .sidebar-link.active { @apply bg-indigo-50 text-indigo-700 font-semibold; }
    </style>
</head>
<body class="antialiased font-sans bg-gray-50" x-data="{ sidebarOpen: false }">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>

    <!-- Sidebar -->
    <aside class="fixed top-0 start-0 h-full w-60 bg-white border-e border-gray-200 z-30 transform transition-transform duration-300
        -translate-x-full lg:translate-x-0 rtl:translate-x-full rtl:lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0 rtl:translate-x-0' : ''">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
            <span class="text-2xl">💰</span>
            <span class="text-lg font-bold text-indigo-600">{{ __('app.app_name') }}</span>
        </div>

        <!-- Nav Links -->
        <nav class="flex flex-col gap-1 p-4 flex-1">
            <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('app.dashboard') }}
            </a>
            <a href="/transactions" class="sidebar-link {{ request()->is('transactions*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('app.transactions') }}
            </a>
            <a href="/categories" class="sidebar-link {{ request()->is('categories*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                {{ __('app.categories') }}
            </a>
            <a href="/reports" class="sidebar-link {{ request()->is('reports*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('app.reports') }}
            </a>
        </nav>

        <!-- Bottom: locale switcher + logout -->
        <div class="p-4 border-t border-gray-100">
            <!-- Locale Switcher -->
            <form method="POST" action="/locale" class="mb-3">
                @csrf
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                    <button type="submit" name="locale" value="en"
                        class="flex-1 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'en' ? 'bg-white shadow text-indigo-600' : 'text-gray-500 hover:text-gray-700' }} transition">
                        EN
                    </button>
                    <button type="submit" name="locale" value="ar"
                        class="flex-1 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'ar' ? 'bg-white shadow text-indigo-600' : 'text-gray-500 hover:text-gray-700' }} transition">
                        AR
                    </button>
                </div>
            </form>
            <!-- User info + logout -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" title="{{ __('app.logout') }}" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ps-60 min-h-screen flex flex-col">
        <!-- Top Navbar (Mobile) -->
        <header class="lg:hidden sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 min-w-[44px] min-h-[44px] flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="text-lg font-bold text-indigo-600">💰 {{ __('app.app_name') }}</span>
            <form method="POST" action="/locale">
                @csrf
                <div class="flex gap-0.5">
                    <button type="submit" name="locale" value="en"
                        class="px-2 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-500' }}">EN</button>
                    <button type="submit" name="locale" value="ar"
                        class="px-2 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-500' }}">AR</button>
                </div>
            </form>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">✕</button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
