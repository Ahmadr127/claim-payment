<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    @livewireStyles

    {{-- Pre-appbar: hide sidebar instantly before JS boots (no flash) --}}
    @if(auth()->user()->layout_preference === 'appbar')
    <style id="pre-appbar-style">
        @media(min-width:1024px){ .sidebar { width:0!important; opacity:0!important; visibility:hidden!important; } }
    </style>
    @endif

    {{-- sidebar.js MUST be before Alpine --}}
    <script src="{{ asset('js/sidebar.js') }}"></script>
</head>
<body class="bg-gray-100 overflow-x-hidden h-screen" data-layout="{{ auth()->user()->layout_preference }}">

    <div x-data="sidebarComponent('{{ auth()->user()->layout_preference }}')" x-init="init()" class="h-full flex overflow-x-hidden">

        {{-- ============================================================
             SIDEBAR
             Desktop: controlled by Alpine (appbar-mode / sidebar-collapsed-mode)
             Mobile:  slides in via mobile-open
        ============================================================ --}}
        <div class="sidebar bg-green-700 shadow-lg flex flex-col h-screen z-50 lg:static lg:inset-0 fixed inset-y-0 left-0"
             :class="sidebarClasses">

            {{-- Logo/Brand --}}
            <div class="flex items-center justify-between h-20 px-4 border-b border-green-600 flex-shrink-0">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-white rounded-xl border border-green-200 shadow-sm p-2 flex-shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                    </div>
                    <h1 class="sidebar-text text-xl font-bold text-white tracking-wide truncate">Sistem</h1>
                </div>
            </div>

            {{-- Sidebar Navigation --}}
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-4 py-6">
                @foreach(config('menu') as $item)
                    @php
                        $hasPermission = false;
                        if (is_array($item['permission'])) {
                            foreach ($item['permission'] as $perm) {
                                if (auth()->user()->hasPermission($perm)) { $hasPermission = true; break; }
                            }
                        } else {
                            $hasPermission = auth()->user()->hasPermission($item['permission']);
                        }
                    @endphp

                    @if($hasPermission)
                        @if(isset($item['children']))
                            @php
                                $isActive = false;
                                foreach($item['active'] as $act) {
                                    if(request()->routeIs($act)) { $isActive = true; break; }
                                }
                            @endphp
                            <div class="mb-1" x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                        class="w-full flex items-center justify-between px-4 py-3 text-white rounded-lg hover:bg-green-800 transition-colors">
                                    <div class="flex items-center min-w-0">
                                        <i class="{{ $item['icon'] }} w-5 sidebar-icon mr-3 flex-shrink-0"></i>
                                        <span class="sidebar-text">{{ $item['title'] }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down sidebar-text text-xs transition-transform duration-200 flex-shrink-0" :class="{ 'rotate-180': open }"></i>
                                </button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="ml-4 pl-4 border-l-2 border-green-600 space-y-1 mt-1">
                                    @foreach($item['children'] as $child)
                                        @if(auth()->user()->hasPermission($child['permission']))
                                        <a href="{{ route($child['route']) }}" wire:navigate
                                           class="flex items-center px-3 py-2 text-green-100 rounded-lg hover:bg-green-800 hover:text-white transition-colors text-sm {{ request()->routeIs($child['active']) ? 'bg-green-800 text-white' : '' }}">
                                            <i class="{{ $child['icon'] }} w-4 mr-2 flex-shrink-0"></i>
                                            <span class="sidebar-text">{{ $child['title'] }}</span>
                                        </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-1">
                                <a href="{{ route($item['route']) }}" wire:navigate
                                   class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-green-800 transition-colors {{ request()->routeIs($item['active']) ? 'bg-green-800' : '' }}">
                                    <i class="{{ $item['icon'] }} w-5 sidebar-icon mr-3 flex-shrink-0"></i>
                                    <span class="sidebar-text">{{ $item['title'] }}</span>
                                </a>
                            </div>
                        @endif
                    @endif
                @endforeach
            </nav>
        </div>

        {{-- ============================================================
             MAIN CONTENT
        ============================================================ --}}
        <div class="flex-1 flex flex-col overflow-x-hidden max-w-full h-full">

            {{-- Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between h-16 px-4 lg:px-6">

                    {{-- Left: Hamburger + AppBar nav --}}
                    <div class="flex items-center gap-3">

                        {{-- Mobile hamburger (always) --}}
                        <button @click="toggle()"
                                class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors lg:hidden"
                                :title="getToggleTitle()">
                            <i class="fas text-lg" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
                        </button>

                        {{-- Desktop: sidebar collapse toggle (sidebar mode only) --}}
                        <button x-show="layoutPref === 'sidebar'"
                                @click="toggle()"
                                class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors hidden lg:flex items-center justify-center"
                                :title="getToggleTitle()"
                                x-cloak>
                            <i class="fas text-lg" :class="isCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
                        </button>

                        {{-- Desktop: logo in appbar mode --}}
                        <div class="hidden lg:flex items-center" x-show="layoutPref === 'appbar'" x-cloak>
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                        </div>

                        {{-- Desktop: Appbar Navigation --}}
                        <nav class="hidden lg:flex h-16 gap-1"
                             x-show="layoutPref === 'appbar'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             x-cloak>
                            @foreach(config('menu') as $item)
                                @php
                                    $hasPermission = false;
                                    if (is_array($item['permission'])) {
                                        foreach ($item['permission'] as $perm) {
                                            if (auth()->user()->hasPermission($perm)) { $hasPermission = true; break; }
                                        }
                                    } else {
                                        $hasPermission = auth()->user()->hasPermission($item['permission']);
                                    }
                                @endphp
                                @if($hasPermission)
                                    @if(isset($item['children']))
                                        @php
                                            $isActive = false;
                                            foreach($item['active'] as $act) {
                                                if(request()->routeIs($act)) { $isActive = true; break; }
                                            }
                                        @endphp
                                        <div class="relative h-full flex items-center" x-data="{ open: false }"
                                             @mouseenter="open = true" @mouseleave="open = false">
                                            <button class="flex items-center gap-1.5 px-3 h-full text-sm font-medium border-b-2 transition-colors {{ $isActive ? 'text-green-800 border-green-800' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300' }}">
                                                {{ $item['title'] }}
                                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-150 ml-0.5" :class="{'rotate-180': open}"></i>
                                            </button>
                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                                 class="absolute top-full left-0 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 origin-top-left -mt-1">
                                                @foreach($item['children'] as $child)
                                                    @if(auth()->user()->hasPermission($child['permission']))
                                                    <a href="{{ route($child['route']) }}" wire:navigate
                                                       class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs($child['active']) ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                                                        <i class="{{ $child['icon'] }} w-4 text-center"></i>
                                                        {{ $child['title'] }}
                                                    </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="h-full flex items-center">
                                            <a href="{{ route($item['route']) }}" wire:navigate
                                               class="flex items-center gap-1.5 px-3 h-full text-sm font-medium border-b-2 transition-colors {{ request()->routeIs($item['active']) ? 'text-green-800 border-green-800' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300' }}">
                                                {{ $item['title'] }}
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </nav>

                        {{-- Page title (sidebar mode) --}}
                        <div class="hidden lg:block" x-show="layoutPref === 'sidebar'" x-cloak>
                            <h2 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                        </div>

                        {{-- Mobile page title --}}
                        <h2 class="text-base font-semibold text-gray-800 lg:hidden">@yield('title', 'Dashboard')</h2>
                    </div>

                    {{-- Right: User dropdown & Icons --}}
                    <div class="flex items-center gap-4">
                        
                        {{-- Search Bar (Appbar only) --}}
                        <div class="relative hidden lg:block" x-show="layoutPref === 'appbar'" x-cloak>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                            <input type="text" placeholder="Search resources..." 
                                   class="block w-64 pl-9 pr-3 py-1.5 border-transparent rounded-full bg-slate-100 text-sm placeholder-gray-500 focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500 transition-colors">
                        </div>

                        {{-- Icons (Appbar only) --}}
                        <div class="hidden lg:flex items-center gap-3 text-gray-500" x-show="layoutPref === 'appbar'" x-cloak>
                            <button class="hover:text-gray-800 transition-colors">
                                <i class="far fa-bell text-lg"></i>
                            </button>
                            <button class="hover:text-gray-800 transition-colors">
                                <i class="fas fa-cog text-lg"></i>
                            </button>
                            <div class="h-6 border-l border-gray-300 mx-1"></div>
                        </div>

                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open"
                                    class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</div>
                                    @php
                                        // Try to get primary role
                                        $primaryRole = 'USER';
                                        if (auth()->user()->roles && auth()->user()->roles->count() > 0) {
                                            $primaryRole = auth()->user()->roles->first()->name;
                                        }
                                    @endphp
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wide">{{ str_replace('_', ' ', $primaryRole) }}</div>
                                </div>
                                <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 flex-shrink-0 bg-green-100 flex items-center justify-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=047857&color=fff&bold=true" alt="Avatar" class="w-full h-full object-cover">
                                </div>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 origin-top-right"
                                 x-cloak>
                                @if(auth()->user()->hasPermission('view_dashboard'))
                                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-home w-4 text-gray-400"></i> Dashboard
                                </a>
                                @endif
                                <a href="{{ route('profile.index') }}" wire:navigate class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-user-circle w-4 text-gray-400"></i> Profil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>

                                {{-- Layout Toggle --}}
                                <button @click="toggleLayoutPreference('{{ route('profile.toggle-layout') }}', '{{ csrf_token() }}')"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas w-4 text-gray-400" :class="layoutPref === 'sidebar' ? 'fa-table-columns' : 'fa-sidebar'"></i>
                                    <span x-text="layoutPref === 'sidebar' ? 'Gunakan Appbar' : 'Gunakan Sidebar'"></span>
                                </button>

                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                                            onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                                        <i class="fas fa-sign-out-alt w-4"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-3 sm:p-4 lg:p-5 bg-gray-50 overflow-y-auto overflow-x-hidden">
                @yield('content')
            </main>
        </div>

        {{-- Mobile Overlay --}}
        <div x-show="mobileOpen"
             @click="mobileOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"
             x-cloak>
        </div>
    </div>

    {{-- Alpine.js is now included via Livewire --}}
    <x-toast />
    @livewireScripts
    @stack('scripts')
</body>
</html>
