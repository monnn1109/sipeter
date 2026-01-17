<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - SIPETER Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-gradient-to-b from-indigo-900 to-purple-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 border-b border-indigo-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="text-indigo-900 font-black text-lg">A</span>
                    </div>
                    <div>
                        <h1 class="font-black text-lg">SIPETER ADMIN</h1>
                        <p class="text-xs text-indigo-300">Administrator</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.documents.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.documents.index') || request()->routeIs('admin.documents.show') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Kelola Dokumen</span>
                </a>

                <a href="{{ route('admin.documents.pending') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.documents.pending') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>Pending Approval</span>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="ml-auto px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>

                <div class="border-t border-indigo-800 my-2"></div>

                <a href="{{ route('admin.verifications.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.verifications.*') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Verifikasi</span>
                </a>

                <a href="{{ route('admin.signatures.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.signatures.index') || request()->routeIs('admin.signatures.pending') || request()->routeIs('admin.signatures.verify.*') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    <span>Tanda Tangan</span>
                </a>

                <div class="border-t border-indigo-800 my-2"></div>

                <!-- ✅ FIXED: Riwayat Dokumen - route ke admin.history.index -->
                <a href="{{ route('admin.history.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.history.*') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <span>Riwayat Dokumen</span>
                </a>

                <!-- ✅ NEW: Riwayat Tanda Tangan - menu terpisah -->
                <a href="{{ route('admin.signatures.history') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.signatures.history') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Riwayat Tanda Tangan</span>
                </a>

                <div class="border-t border-indigo-800 my-2"></div>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.users.*') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <span>Kelola User</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.notifications.*') ? 'bg-white text-indigo-900 font-bold shadow-lg' : 'hover:bg-indigo-800' }} transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <span>Notifikasi</span>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="ml-auto px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">{{ $unreadNotifications }}</span>
                    @endif
                </a>
            </nav>

            <div class="p-4 border-t border-indigo-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition-all w-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
                    <p class="text-sm text-gray-500">{{ $subtitle ?? '' }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                            @if(isset($unreadNotifications) && $unreadNotifications > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-bold">
                                    {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                </span>
                            @endif
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-red-700 font-semibold">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
