<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Parampara') }} - Admin</title>

        <!-- Favicon -->
        @php
            $settings = \App\Models\CompanyProfile::first();
            $favicon16 = $settings && $settings->favicon_16 ? asset('storage/' . $settings->favicon_16) : asset('img/favicon/16x16.png');
            $favicon32 = $settings && $settings->favicon_32 ? asset('storage/' . $settings->favicon_32) : asset('img/favicon/32x32.png');
        @endphp
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon32 }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $favicon16 }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <div class="w-64 bg-gray-900 text-white shadow-lg">
                <div class="p-6 border-b border-gray-700">
                    <a href="/" class="flex items-center">
                        @php
                            $settings = \App\Models\CompanyProfile::first();
                            $logoPath = $settings && $settings->logo ? asset('storage/' . $settings->logo) : asset('img/logo-svg.svg');
                            $companyName = $settings && $settings->company_name ? $settings->company_name : 'Parampara';
                        @endphp
                        <img src="{{ $logoPath }}" alt="Logo" class="h-10 w-10 mr-3 object-contain">
                        <span class="text-xl font-bold">{{ $companyName }}</span>
                    </a>
                </div>

                <!-- Navigation Menu -->
                <nav class="mt-6 pb-32">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.dashboard.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                            </svg>
                            📊 Dashboard
                        </span>
                    </a>

                    <!-- Products -->
                    <a href="{{ route('admin.products.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8-4m-8 4v10l8-4m0 0l-8-4"></path>
                            </svg>
                            📦 Product
                        </span>
                    </a>

                    <!-- Purchase -->
                    <a href="{{ route('admin.purchases.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.purchases.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9a2 2 0 012-2z"></path>
                            </svg>
                            🛒 Purchase
                        </span>
                    </a>

                    <!-- Sell -->
                    <a href="{{ route('admin.sells.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.sells.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            💰 Sell
                        </span>
                    </a>

                    <!-- Purchase Returns -->
                    <a href="{{ route('admin.purchase-returns.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.purchase-returns.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            ↩️ Purchase Return
                        </span>
                    </a>

                    <!-- Sell Returns -->
                    <a href="{{ route('admin.sell-returns.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.sell-returns.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            ↩️ Sell Return
                        </span>
                    </a>

                    <!-- Expenses -->
                    <a href="{{ route('admin.expenses.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.expenses.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            💸 Expenses
                        </span>
                    </a>

                    <!-- Reports -->
                    <a href="{{ route('admin.reports.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.reports.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            📊 Report
                        </span>
                    </a>

                    <!-- Stock Management -->
                    <a href="{{ route('admin.stocks.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.stocks.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8-4m-8 4v10l8-4m0 0l-8-4"></path>
                            </svg>
                            📦 Stock
                        </span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('admin.settings.index') }}" class="block px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            ⚙️ Settings
                        </span>
                    </a>

                    <!-- DB Backup -->
                    <a href="{{ route('admin.db-backup.download') }}" class="block px-6 py-3 hover:bg-gray-800 transition">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                            </svg>
                            💾 DB Backup
                        </span>
                    </a>
                </nav>


            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Top Bar -->
                <div class="bg-white shadow">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Admin Panel')</h1>
                        <div class="flex items-center gap-4">
                            <div class="relative group">
                                <button class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-gray-600">{{ Auth::user()->name ?? 'Admin' }}</span>
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block z-50">
                                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Settings & Profile
                                        </span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <div class="flex-1 overflow-auto p-6">
                    <!-- Flash Messages -->
                    @if ($message = Session::get('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $message }}
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $message }}
                        </div>
                    @endif

                    @yield('content')
                </div>

                <!-- Footer -->
                <div class="bg-white border-t border-gray-200 px-6 py-4 text-center text-gray-600 text-sm">
                    © {{ date('Y') }} Parampara. All rights reserved.
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        @include('components.delete-confirmation-modal')
    </body>
</html>

