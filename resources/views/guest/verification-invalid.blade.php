@extends('layouts.guest')

@section('title', 'Link Tidak Valid')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-100 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-fade-in">
            {{-- Header with gradient --}}
            <div class="bg-gradient-to-r from-red-500 to-orange-500 p-8 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4 animate-shake-rotate">
                    <svg class="w-16 h-16 text-red-600 animate-warning-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path class="warning-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 animate-slide-up">⚠️ Link Tidak Valid</h1>
                <p class="text-red-50 animate-slide-up-delay">Verifikasi tidak dapat diproses</p>
            </div>

            <div class="p-8">
                {{-- Error Message Box --}}
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-red-800 mb-1">Pesan Error:</h3>
                            <p class="text-sm text-red-700">
                                {{ $message ?? 'Link verifikasi tidak valid atau sudah expired. Silakan hubungi admin jika ada pertanyaan.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Possible Causes --}}
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-orange-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Kemungkinan Penyebab:
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Link Sudah Expired</h4>
                                <p class="text-sm text-gray-600 mt-1">Link verifikasi hanya berlaku 3 hari sejak dikirim</p>
                            </div>
                        </div>

                        <div class="flex items-start p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Sudah Diproses</h4>
                                <p class="text-sm text-gray-600 mt-1">Verifikasi sudah pernah dilakukan sebelumnya (approved/rejected)</p>
                            </div>
                        </div>

                        <div class="flex items-start p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Link Tidak Valid</h4>
                                <p class="text-sm text-gray-600 mt-1">Link yang digunakan salah, rusak, atau tidak lengkap</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Butuh Bantuan?
                    </h3>
                    <p class="text-sm text-blue-800 mb-3">Jika Anda yakin ini adalah kesalahan atau membutuhkan link baru, silakan hubungi admin SIPETER:</p>

                    <div class="space-y-2">
                        <a href="mailto:admin@sipeter.ac.id" class="flex items-center text-sm text-blue-700 hover:text-blue-900 p-3 bg-white rounded-lg hover:bg-blue-100 transition-all shadow-sm hover:shadow group">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Email</p>
                                <p class="text-xs text-gray-600">admin@sipeter.ac.id</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>

                        <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center text-sm text-blue-700 hover:text-blue-900 p-3 bg-white rounded-lg hover:bg-blue-100 transition-all shadow-sm hover:shadow group">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">WhatsApp</p>
                                <p class="text-xs text-gray-600">+62 812-3456-7890</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <button onclick="handleClose()" class="w-full px-6 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-lg hover:from-red-700 hover:to-orange-700 font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-3">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tutup Halaman
                    </span>
                </button>

                <a href="{{ route('home') }}" class="block w-full text-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-all">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Kembali ke Beranda
                    </span>
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-6 text-gray-600 text-sm space-y-1">
            <p class="font-semibold">SIPETER - Sistem Persuratan Terpadu</p>
            <p>STABA Bandung</p>
            <p class="text-xs text-gray-500">© 2024-{{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</div>

<script>
function handleClose() {
    window.close();

    setTimeout(function() {
        if (!window.closed) {
            showNotification('Mengalihkan ke beranda...', 'info');
            setTimeout(function() {
                window.location.href = '{{ route("home") }}';
            }, 2000);
        }
    }, 100);
}

function showNotification(message, type = 'info') {
    const colors = {
        info: 'from-blue-500 to-blue-600',
        success: 'from-green-500 to-green-600',
        warning: 'from-yellow-500 to-yellow-600',
        error: 'from-red-500 to-red-600'
    };

    const icons = {
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-slide-in max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icons[type]}
            </svg>
            <span class="font-medium">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(function() {
        notification.classList.add('animate-slide-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto-redirect after 2 minutes
let autoRedirectTimer = setTimeout(function() {
    showNotification('Halaman tidak aktif, mengalihkan ke beranda...', 'warning');
    setTimeout(function() {
        window.location.href = '{{ route("home") }}';
    }, 3000);
}, 120000);

// Reset timer on interaction
document.addEventListener('click', function() {
    clearTimeout(autoRedirectTimer);
    autoRedirectTimer = setTimeout(function() {
        window.location.href = '{{ route("home") }}';
    }, 120000);
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes shake-rotate {
    0%, 100% {
        transform: rotate(0deg) scale(1);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: rotate(-10deg) scale(0.95);
    }
    20%, 40%, 60%, 80% {
        transform: rotate(10deg) scale(1.05);
    }
}

@keyframes warning-pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(0.95);
    }
}

@keyframes pulse-glow-red {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slide-out {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

@keyframes draw-warning {
    0% {
        stroke-dasharray: 0, 200;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        stroke-dasharray: 200, 0;
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

.animate-shake-rotate {
    animation: shake-rotate 0.8s ease-out, pulse-glow-red 2s infinite 1s;
}

.animate-warning-pulse {
    animation: warning-pulse 1.5s ease-in-out infinite, draw-warning 0.8s ease-out 0.3s backwards;
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out 0.5s backwards;
}

.animate-slide-up-delay {
    animation: slide-up 0.6s ease-out 0.7s backwards;
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
    animation: slide-out 0.3s ease-out;
}

/* Continuous wobble on hover */
@keyframes wobble {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-5deg);
    }
    75% {
        transform: rotate(5deg);
    }
}

.animate-shake-rotate:hover {
    animation: wobble 0.5s ease-in-out infinite;
}

/* SVG path animation */
.warning-path {
    stroke-dasharray: 200;
    stroke-dashoffset: 200;
    animation: draw-warning-path 0.8s ease-out 0.3s forwards;
}

@keyframes draw-warning-path {
    to {
        stroke-dashoffset: 0;
    }
}
</style>
@endsection
