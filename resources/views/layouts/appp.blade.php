<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'SemestaAirline') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 bg-cover bg-center" style="background-image: url('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg');">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Navigation Links -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('pelanggan.dashboard') }}" class="text-2xl font-bold text-blue-600">
                                <i class="fas fa-plane mr-2"></i>
                                {{ config('app.name', 'SemestaAirline') }}
                            </a>
                        </div>
                        
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('pelanggan.dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pelanggan.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition duration-150 ease-in-out">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('pelanggan.pesanan.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pelanggan.pesanan.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition duration-150 ease-in-out">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Pesanan
                            </a>
                          
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Notifications -->
                        <button class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition duration-150 ease-in-out mr-4">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="sr-only">Notifications</span>
                        </button>

                        <!-- Profile dropdown -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <div>
                                <button @click="open = ! open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs"></i>
                                </button>
                            </div>

                            <div x-show="open" 
                                 @click.outside="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('pelanggan.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user-circle mr-3"></i>
                                        Profil Saya
                                    </a>
                                   
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-3"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div class="sm:hidden" x-show="open" x-data="{ open: false }">
                <div class="pt-2 pb-3 space-y-1 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('pelanggan.dashboard') }}" class="flex items-center pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('pelanggan.dashboard') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-600' }} text-base font-medium hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-plane-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('pelanggan.pesanan.index') }}" class="flex items-center pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('pelanggan.pesanan.*') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-600' }} text-base font-medium hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Pesanan
                    </a>
                   
                </div>

                <!-- Mobile User Menu -->
                <div class="pt-4 pb-3 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center px-4">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white w-10 h-10 rounded-full flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a href="{{ route('pelanggan.profile') }}" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition duration-150 ease-in-out">
                            <i class="fas fa-user-circle mr-3"></i>
                            Profil Saya
                        </a>
                       
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-base font-medium text-red-500 hover:text-red-700 hover:bg-gray-100 transition duration-150 ease-in-out">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        @if (session('success'))
            <div class="bg-green-100 bsorder-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <p>{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-2">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-store text-blue-600 text-2xl mr-2"></i>
                            <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'SemestaAirline') }}</span>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Platform terpercaya untuk semua kebutuhan pesanan Anda. 
                            Nikmati layanan terbaik dengan kemudahan dan kenyamanan.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Menu</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('pelanggan.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm">Dashboard</a></li>
                            <li><a href="{{ route('pelanggan.pesanan.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">Pesanan</a></li>
                          
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Bantuan</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">FAQ</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Kontak</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-8">
                    <p class="text-center text-gray-500 text-sm">
                        &copy; 2025 {{ config('app.name', 'SemestaAirline') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>