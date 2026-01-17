@extends('layouts.guest')

@section('title', $type === 'approved' ? 'Verifikasi Berhasil' : 'Penolakan Tercatat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
        @if($type === 'approved')
        {{-- ✅ APPROVED --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-fade-in">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4 animate-bounce-scale">
                    <svg class="w-16 h-16 text-green-600 animate-check-draw" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path class="check-circle" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 animate-slide-up">✅ Verifikasi Berhasil!</h1>
                <p class="text-green-50 animate-slide-up-delay">Dokumen {{ $levelLabel ?? 'Level ' . ($currentLevel ?? '') }} telah disetujui</p>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <p class="text-lg text-gray-700 mb-4">
                        Terima kasih, <span class="font-semibold text-gray-900">{{ $authority->name }}</span>.
                    </p>
                    <p class="text-gray-600">
                        Persetujuan Anda telah tercatat di sistem dan dokumen akan diproses ke tahap selanjutnya.
                    </p>
                </div>

                {{-- 📊 Document Info --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📋 No. Dokumen:</span>
                            <span class="font-semibold text-gray-900">{{ $document->request_code }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">👤 Pemohon:</span>
                            <span class="font-semibold text-gray-900">{{ $document->applicant_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📝 Jenis Dokumen:</span>
                            <span class="font-semibold text-gray-900">{{ $document->documentType->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📅 Tanggal Verifikasi:</span>
                            <span class="font-semibold text-gray-900">{{ now()->format('d F Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">✅ Status:</span>
                            <span class="font-semibold text-green-600">Disetujui</span>
                        </div>
                    </div>
                </div>

                {{-- 📈 Progress Bar --}}
                @if(isset($progressPercentage))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-blue-900">📊 Progress Verifikasi:</h3>
                        <span class="text-sm font-bold text-blue-700">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500 ease-out animate-progress"
                             style="width: {{ $progressPercentage }}%">
                        </div>
                    </div>
                    <p class="text-xs text-blue-700 mt-2">
                        @if($progressPercentage < 100)
                            Lanjut ke: {{ $nextStep ?? 'Tahap berikutnya' }}
                        @else
                            🎉 Semua verifikasi selesai!
                        @endif
                    </p>
                </div>
                @endif

                {{-- 📌 Next Steps --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-indigo-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Apa yang Terjadi Selanjutnya?
                    </h3>
                    <ul class="space-y-2 text-sm text-indigo-800">
                        @if($isFinalLevel ?? false)
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2 mt-0.5">✓</span>
                                <span><strong>Semua verifikasi selesai!</strong> Admin akan memproses dokumen untuk penandatanganan.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-500 mr-2 mt-0.5">•</span>
                                <span>Anda akan dihubungi untuk upload tanda tangan digital + QR Code</span>
                            </li>
                        @else
                            <li class="flex items-start">
                                <span class="text-indigo-500 mr-2 mt-0.5">•</span>
                                <span>Dokumen akan otomatis dikirim ke {{ $nextStep ?? 'verifikator berikutnya' }}</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-500 mr-2 mt-0.5">•</span>
                                <span>Admin dan pemohon akan menerima notifikasi WhatsApp</span>
                            </li>
                        @endif
                        <li class="flex items-start">
                            <span class="text-indigo-500 mr-2 mt-0.5">•</span>
                            <span>Anda akan menerima konfirmasi via WhatsApp</span>
                        </li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <button onclick="handleClose()" class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Selesai - Tutup Halaman
                    </span>
                </button>

                <a href="{{ route('home') }}" class="block w-full text-center mt-3 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-all">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        @else
        {{-- ❌ REJECTED --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-fade-in">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 p-8 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4 animate-bounce-scale">
                    <svg class="w-16 h-16 text-orange-600 animate-check-draw" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path class="check-circle" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 animate-slide-up">✅ Penolakan Berhasil Tercatat</h1>
                <p class="text-orange-50 animate-slide-up-delay">Verifikasi {{ $levelLabel ?? 'Level ' . ($currentLevel ?? '') }} telah diproses</p>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <p class="text-lg text-gray-700 mb-4">
                        Terima kasih, <span class="font-semibold text-gray-900">{{ $authority->name }}</span>.
                    </p>
                    <p class="text-gray-600">
                        Penolakan Anda telah tercatat di sistem. Alasan telah dikirim ke admin dan pemohon.
                    </p>
                </div>

                {{-- 📊 Document Info --}}
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📋 No. Dokumen:</span>
                            <span class="font-semibold text-gray-900">{{ $document->request_code }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">👤 Pemohon:</span>
                            <span class="font-semibold text-gray-900">{{ $document->applicant_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📝 Jenis Dokumen:</span>
                            <span class="font-semibold text-gray-900">{{ $document->documentType->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📅 Tanggal Penolakan:</span>
                            <span class="font-semibold text-gray-900">{{ now()->format('d F Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">❌ Status:</span>
                            <span class="font-semibold text-red-600">Ditolak</span>
                        </div>
                    </div>
                </div>

                {{-- 📝 Rejection Reason --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-yellow-800 mb-1">Alasan Penolakan:</h3>
                            <p class="text-sm text-yellow-700">{{ $reason ?? 'Tidak ada alasan yang diberikan' }}</p>
                        </div>
                    </div>
                </div>

                {{-- 📌 What Happens Next --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Apa yang Terjadi Selanjutnya?
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2 mt-0.5">⚠️</span>
                            <span><strong>Proses verifikasi dihentikan</strong> - Dokumen tidak akan lanjut ke level berikutnya</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-0.5">•</span>
                            <span>Admin akan menerima notifikasi penolakan via WhatsApp</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-0.5">•</span>
                            <span>Pemohon akan diberitahu dan dapat mengajukan permohonan baru setelah perbaikan</span>
                        </li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <button onclick="handleClose()" class="w-full px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Selesai - Tutup Halaman
                    </span>
                </button>

                <a href="{{ route('home') }}" class="block w-full text-center mt-3 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-all">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="text-center mt-6 text-gray-600 text-sm space-y-1">
            <p class="font-semibold">SIPETER - Sistem Persuratan Terpadu</p>
            <p>STABA Bandung</p>
            <p class="text-xs text-gray-500">© 2024-{{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</div>

{{-- ✅ Smart JavaScript for closing window --}}
<script>
function handleClose() {
    // Try to close the window
    window.close();

    // Fallback: If window.close() doesn't work (opened from external link)
    setTimeout(function() {
        if (!window.closed) {
            showNotification('Halaman akan diarahkan ke beranda...', 'info');

            setTimeout(function() {
                window.location.href = '{{ route("home") }}';
            }, 2000);
        }
    }, 100);
}

function showNotification(message, type = 'success') {
    const colors = {
        success: 'bg-green-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-xl z-50 animate-slide-in max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(function() {
        notification.classList.add('animate-slide-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto-redirect after 2 minutes of inactivity
let autoRedirectTimer = setTimeout(function() {
    if (confirm('Halaman ini sudah tidak aktif. Klik OK untuk kembali ke beranda.')) {
        window.location.href = '{{ route("home") }}';
    }
}, 120000); // 2 minutes

// Reset timer on any user interaction
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

@keyframes bounce-scale {
    0%, 100% {
        transform: scale(1);
    }
    25% {
        transform: scale(0.9);
    }
    50% {
        transform: scale(1.1);
    }
    75% {
        transform: scale(0.95);
    }
}

@keyframes check-draw {
    0% {
        stroke-dasharray: 0, 100;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        stroke-dasharray: 100, 0;
        opacity: 1;
    }
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
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

@keyframes progress {
    from {
        width: 0%;
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

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

.animate-bounce-scale {
    animation: bounce-scale 0.8s ease-out, pulse-glow 2s infinite 1s;
}

.animate-check-draw {
    animation: check-draw 0.8s ease-out 0.3s backwards;
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out 0.5s backwards;
}

.animate-slide-up-delay {
    animation: slide-up 0.6s ease-out 0.7s backwards;
}

.animate-progress {
    animation: progress 1.5s ease-out;
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
    animation: slide-out 0.3s ease-out;
}

/* Continuous subtle animations */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce-scale:hover {
    animation: float 2s ease-in-out infinite;
}

/* SVG path animation */
.check-circle {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: draw-circle 0.6s ease-out 0.3s forwards;
}

@keyframes draw-circle {
    to {
        stroke-dashoffset: 0;
    }
}
</style>
@endsection
