<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Visiting Card Management System - Digitize, organize, and manage your business contacts">

    @php
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $companyName = $settings['company_name'] ?? config('app.name', 'VCMS');
        $companyLogo = $settings['company_logo'] ?? null;
    @endphp

    <title>{{ $companyName }} — @yield('title', 'Dashboard') | Visiting Card Management System</title>

    @if ($companyLogo)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($companyLogo) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    
    <!-- Intl Tel Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    
    <style>
        /* Tom Select Dark Mode Overrides */
        .ts-control {
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            min-height: unset !important;
            color: inherit !important;
        }

        .ts-wrapper.single .ts-control {
            background-color: transparent !important;
        }

        .ts-dropdown {
            background-color: #171717 !important;
            /* dark: surface-900 */
            border-color: #262626 !important;
            /* dark: surface-800 */
            color: #d4d4d4 !important;
            /* dark: surface-300 */
            border-radius: 0.75rem !important;
            margin-top: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3) !important;
        }

        .light .ts-dropdown {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
            color: #374151 !important;
        }

        .ts-dropdown .active {
            background-color: #262626 !important;
            /* dark: surface-800 */
            color: #6366f1 !important;
            /* primary-500 */
        }

        .light .ts-dropdown .active {
            background-color: #f3f4f6 !important;
            color: #4f46e5 !important;
        }

        .ts-dropdown .option:hover {
            background-color: #262626 !important;
        }

        .light .ts-dropdown .option:hover {
            background-color: #f3f4f6 !important;
        }

        .ts-dropdown .create {
            color: #818cf8 !important;
            /* primary-400 */
            font-weight: 500;
        }

        .light .ts-dropdown .create {
            color: #4f46e5 !important;
        }

        /* Intl Tel Input Global Overrides */
        .iti {
            width: 100%;
            display: block;
        }
        .iti__country-list {
            background-color: #171717 !important; /* dark: surface-900 */
            border-color: #262626 !important; /* dark: surface-800 */
            color: #d4d4d4 !important; /* dark: surface-300 */
            border-radius: 0.75rem !important;
            margin-top: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3) !important;
            z-index: 50 !important;
            max-width: 300px;
        }
        .light .iti__country-list {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
            color: #374151 !important;
        }
        .iti__country:hover, .iti__country.iti__highlight {
            background-color: #262626 !important; /* dark: surface-800 */
        }
        .light .iti__country:hover, .light .iti__country.iti__highlight {
            background-color: #f3f4f6 !important;
        }
        .iti__flag-container {
            padding: 2px;
        }
        .iti__selected-flag {
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
            background-color: rgba(255, 255, 255, 0.03) !important;
        }
        .light .iti__selected-flag {
            background-color: rgba(0, 0, 0, 0.02) !important;
        }
        .iti--allow-dropdown input {
            padding-left: 52px !important;
        }
    </style>

    <!-- Scripts -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-surface-950 text-surface-200 min-h-screen">

    <!-- Flash Messages -->
    @if (session('success'))
        <div data-flash
            class="fixed top-4 right-4 z-[100] max-w-md px-5 py-3 rounded-xl bg-success-500/15 border border-success-400/30 text-success-400 text-sm font-medium shadow-lg backdrop-blur-md transition-all duration-300 animate-slide-in-right">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div data-flash
            class="fixed top-4 right-4 z-[100] max-w-md px-5 py-3 rounded-xl bg-danger-500/15 border border-danger-400/30 text-danger-400 text-sm font-medium shadow-lg backdrop-blur-md transition-all duration-300 animate-slide-in-right">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="flex min-h-screen">
        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"
            onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="mobile-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:z-auto lg:h-screen transition-transform duration-300 ease-in-out">
            <div
                class="flex flex-col h-full bg-surface-900/80 backdrop-blur-xl border-r border-surface-700/50 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-surface-700/50">
                    @if ($companyLogo)
                        <img src="{{ Storage::url($companyLogo) }}" alt="{{ $companyName }}"
                            class="h-10 w-auto object-contain">
                        <div>
                            <h1 class="text-lg font-bold text-white">{{ $companyName . ' - VCMS' ?? 'VCMS' }}</h1>
                            <p class="text-xs text-surface-400">Card Management</p>
                        </div>
                    @else
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white">{{ $companyName ?? 'VCMS' }}</h1>
                            <p class="text-xs text-surface-400">Card Management</p>
                        </div>
                    @endif
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <p class="px-3 mb-3 text-xs font-semibold uppercase tracking-wider text-surface-500">Main</p>

                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('contacts.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('contacts.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Contacts
                    </a>

                    <a href="{{ route('events.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('events.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Events
                    </a>

                    <a href="{{ route('email-templates.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('email-templates.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        Templates
                    </a>

                    <a href="{{ route('email-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('email-logs.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Email Logs
                    </a>

                    <div class="px-3 mt-6 mb-3 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500">Theme</p>
                        <button onclick="toggleTheme()"
                            class="p-1.5 rounded-lg bg-surface-800 border border-surface-700 text-surface-400 hover:text-primary-400 transition-all focus:outline-none"
                            title="Toggle Light/Dark Mode">
                            <!-- Sun Icon (visible in light mode) -->
                            <svg class="w-4 h-4 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <!-- Moon Icon (visible in dark mode) -->
                            <svg class="w-4 h-4 moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <p class="px-3 mt-4 mb-3 text-xs font-semibold uppercase tracking-wider text-surface-500">Admin
                        </p>

                        <a href="{{ route('email-configs.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('email-configs.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Email Config
                        </a>

                        <a href="{{ route('settings.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Global Settings
                        </a>

                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-primary-500/15 text-primary-400 shadow-sm' : 'text-surface-400 hover:text-surface-200 hover:bg-surface-800/50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            Users
                        </a>
                    @endif
                </nav>

                <!-- User Info -->
                <div class="px-4 py-4 border-t border-surface-700/50">
                    <div data-dropdown class="relative">
                        <button data-dropdown-trigger
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm hover:bg-surface-800/50 transition-all duration-200">
                            <div
                                class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 text-left">
                                <p class="text-sm font-medium text-surface-200">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-surface-500 capitalize">{{ Auth::user()->role }}</p>
                            </div>
                            <svg class="w-4 h-4 text-surface-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div data-dropdown-content
                            class="absolute bottom-full left-0 right-0 mb-2 rounded-xl bg-surface-800 border border-surface-700 shadow-xl overflow-hidden transition-all duration-200"
                            style="display: none; opacity: 0; transform: scale(0.95);">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-surface-300 hover:bg-surface-700 hover:text-surface-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-danger-400 hover:bg-surface-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header
                class="sticky top-0 z-30 flex items-center gap-4 px-6 py-4 bg-surface-950/80 backdrop-blur-xl border-b border-surface-800/50">
                <!-- Mobile menu button -->
                <button onclick="toggleSidebar()"
                    class="lg:hidden p-2 rounded-xl text-surface-400 hover:text-surface-200 hover:bg-surface-800 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-white">@yield('title', 'Dashboard')</h2>
                    @hasSection('subtitle')
                        <p class="text-sm text-surface-400 mt-0.5">@yield('subtitle')</p>
                    @endif
                </div>

                @yield('header-actions')
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
    @stack('scripts')
</body>

</html>
