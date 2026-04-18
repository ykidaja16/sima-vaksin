<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Reminder Vaksin')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/medical.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        
        /* Sidebar Styles */
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-overlay {
            transition: opacity 0.3s ease-in-out;
        }
        
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #1e40af;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 3px;
        }
        
        /* Main content transition */
        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* Mobile menu animation */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                opacity: 0;
                pointer-events: none;
            }
            
            .sidebar-overlay.active {
                opacity: 1;
                pointer-events: auto;
            }
        }
        
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
            
            .main-content {
                margin-left: 16rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Mobile Header -->
    <div class="lg:hidden bg-blue-600 text-white shadow-lg fixed top-0 left-0 right-0 z-40 h-16">
        <div class="flex items-center justify-between h-full px-4">
            <button id="mobile-menu-btn" class="p-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-white">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <a href="{{ route('patients.index') }}" class="flex items-center space-x-2">
                <i class="fas fa-syringe text-2xl"></i>
                <span class="font-bold text-lg">VaksinReminder</span>
            </a>
            <div class="w-10"></div>
        </div>
    </div>

    <!-- Sidebar Overlay (Mobile Only) -->
    <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 bg-blue-800 text-white z-50 flex flex-col shadow-xl">
        <!-- Sidebar Header -->
        <div class="p-6 border-b border-blue-700">
            <a href="{{ route('patients.index') }}" class="flex items-center space-x-3">
                <div class="bg-white p-2 rounded-lg">
                    <i class="fas fa-syringe text-2xl text-blue-600"></i>
                </div>
                <div>
                    <span class="font-bold text-xl block">Vaksin</span>
                    <span class="text-sm text-blue-200">Reminder</span>
                </div>
            </a>
        </div>

        <!-- User Info -->
        @auth
        <div class="px-4 py-3 border-b border-blue-700 bg-blue-900">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2 rounded-full">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-300 uppercase">{{ auth()->user()->role_name }}</p>
                </div>
            </div>
        </div>
        @endauth

        <!-- Sidebar Navigation -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-4">
            <ul class="space-y-1 px-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        {{-- Admin Menu --}}
                        <li>
                            <a href="{{ route('patients.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('patients.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                                <i class="fas fa-users w-5 text-center"></i>
                                <span class="font-medium">Data Pasien</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reminders.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('reminders.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                                <i class="fas fa-bell w-5 text-center"></i>
                                <span class="font-medium">Reminder H-7</span>
                            </a>
                        </li>
                        <li class="relative" x-data="{ open: {{ request()->routeIs('import.*') || request()->routeIs('manual-input.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('import.*') || request()->routeIs('manual-input.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-keyboard w-5 text-center"></i>
                                    <span class="font-medium">Input Data</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- Submenu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="mt-1 space-y-1">
                                <a href="{{ route('import.excel') }}" 
                                   class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('import.*') && !request()->routeIs('manual-input.*') ? 'bg-blue-500 text-white' : 'text-blue-200 hover:bg-blue-700 hover:text-white' }} pl-12">
                                    <i class="fas fa-file-excel w-5 text-center"></i>
                                    <span class="text-sm">Import Excel</span>
                                </a>
                                <a href="{{ route('manual-input.index') }}" 
                                   class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('manual-input.*') ? 'bg-blue-500 text-white' : 'text-blue-200 hover:bg-blue-700 hover:text-white' }} pl-12">
                                    <i class="fas fa-edit w-5 text-center"></i>
                                    <span class="text-sm">Input Manual</span>
                                </a>
                            </div>
                        </li>
                    @endif

                    @if(auth()->user()->isIT())
                        {{-- IT Menu --}}
                        <li class="pt-4 pb-2">
                            <span class="px-4 text-xs font-semibold text-blue-400 uppercase tracking-wider">Master Data</span>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                                <i class="fas fa-user-cog w-5 text-center"></i>
                                <span class="font-medium">Manajemen User</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vaccine-types.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('vaccine-types.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                                <i class="fas fa-syringe w-5 text-center"></i>
                                <span class="font-medium">Manajemen Vaksin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('branches.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('branches.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                                <i class="fas fa-hospital w-5 text-center"></i>
                                <span class="font-medium">Manajemen Cabang</span>
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-blue-700 bg-blue-900 space-y-2">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 rounded-lg text-blue-100 hover:bg-blue-700 hover:text-white transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @endauth
            <div class="text-center text-xs text-blue-400 pt-2">
                <p>&copy; {{ date('Y') }} Sistem Reminder Vaksin</p>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content min-h-screen flex flex-col pt-16 lg:pt-0">
        <!-- Flash Messages -->
        <div class="px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('import_errors'))
                <div class="bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Peringatan Import:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} Sistem Reminder Vaksin. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Mobile menu button click
        document.getElementById('mobile-menu-btn').addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Close sidebar when clicking on a link (mobile only)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
