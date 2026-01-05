<nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50 w-full border-b border-green-100">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo (CLICKABLE - acts as Home) -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group cursor-pointer">
                    <div class="relative">
                        <div class="absolute inset-0 bg-green-400 rounded-xl blur-sm group-hover:blur-md transition-all opacity-50"></div>
                        <img src="{{ asset('images/STABA.png') }}" alt="Logo STABA" class="h-12 w-12 object-contain relative z-10 transform group-hover:scale-110 transition-transform duration-300">
                    </div>
                    <div>
                        <span class="font-black text-2xl bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent group-hover:from-green-700 group-hover:to-green-900 transition-all">SIPETER</span>
                        <p class="text-xs text-gray-500 font-medium group-hover:text-gray-700 transition-colors">Sistem Pengajuan Terpadu</p>
                    </div>
                </a>
            </div>

            <!-- Desktop: Login Button Only -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <div class="flex items-center gap-3 bg-green-50 px-4 py-2 rounded-xl">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm text-gray-700 font-semibold">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold transition-all duration-300 shadow-md hover:shadow-lg">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:scale-105">
                        Login
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button onclick="toggleMobileMenu()" class="p-2 rounded-lg text-gray-600 hover:text-green-600 hover:bg-green-50 transition-all duration-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-green-100 bg-white/95 backdrop-blur-md">
        <div class="px-4 pt-4 pb-4 space-y-2">
            @auth
                <div class="flex items-center gap-3 px-4 py-2 bg-green-50 rounded-lg mb-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="text-sm text-gray-700 font-semibold">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-all font-semibold">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg text-center hover:from-green-700 hover:to-green-800 transition-all font-bold shadow-md">
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}
</script>
