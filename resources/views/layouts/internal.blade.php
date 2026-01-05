<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIPETER Internal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <div x-data="{ mobileMenuOpen: false }" class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 border-b border-blue-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="text-blue-800 font-black text-lg">S</span>
                    </div>
                    <div>
                        <h1 class="font-black text-lg">SIPETER</h1>
                        <p class="text-xs text-blue-300">{{ auth()->user()->role->label() }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('internal.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('internal.dashboard') ? 'bg-white text-blue-800 font-bold shadow-lg' : 'hover:bg-blue-700' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('internal.documents.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('internal.request.*') ? 'bg-white text-blue-800 font-bold shadow-lg' : 'hover:bg-blue-700' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Buat Pengajuan</span>
                </a>

                <a href="{{ route('internal.my-documents.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('internal.my-documents*') || request()->routeIs('internal.documents-detail') ? 'bg-white text-blue-800 font-bold shadow-lg' : 'hover:bg-blue-700' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Pengajuan Saya</span>
                </a>

                <a href="{{ route('internal.profile.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('internal.profile*') ? 'bg-white text-blue-800 font-bold shadow-lg' : 'hover:bg-blue-700' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span>Profile</span>
                </a>
            </nav>

            <div class="p-4 border-t border-blue-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-700 transition-all w-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-4 md:px-6 py-4 flex items-center justify-between">
                <div class="flex-1">
                    <h2 class="text-lg md:text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    @hasSection('page-subtitle')
                        <p class="text-xs md:text-sm text-gray-500">@yield('page-subtitle')</p>
                    @endif
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->nip_nidn }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </header>

            <div x-show="mobileMenuOpen"
                 x-transition
                 class="md:hidden bg-gradient-to-b from-blue-800 to-blue-900 text-white px-4 py-4 border-b border-blue-700">
                <nav class="space-y-2">
                    <a href="{{ route('internal.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('internal.dashboard') ? 'bg-blue-700 font-bold' : 'hover:bg-blue-700' }} transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('internal.documents.create') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('internal.request.*') ? 'bg-blue-700 font-bold' : 'hover:bg-blue-700' }} transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Buat Pengajuan</span>
                    </a>

                    <a href="{{ route('internal.my-documents.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('internal.my-documents*') || request()->routeIs('internal.documents-detail') ? 'bg-blue-700 font-bold' : 'hover:bg-blue-700' }} transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Pengajuan Saya</span>
                    </a>

                    <a href="{{ route('internal.profile.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('internal.profile*') ? 'bg-blue-700 font-bold' : 'hover:bg-blue-700' }} transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span>Profile</span>
                    </a>

                    <div class="border-t border-blue-700 pt-2 mt-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 transition-all w-full">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </div>

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl">
                        <p class="text-green-700 font-semibold text-sm md:text-base">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl">
                        <p class="text-red-700 font-semibold text-sm md:text-base">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
