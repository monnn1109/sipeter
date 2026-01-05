@extends('layouts.guest')

@section('title', 'Verifikasi Berhasil')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
        @if($type === 'approved')
        {{-- ✅ APPROVED --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4">
                    <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">✅ Verifikasi Berhasil!</h1>
                <p class="text-green-50">Dokumen telah disetujui</p>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <p class="text-lg text-gray-700 mb-4">
                        Terima kasih, <span class="font-semibold text-gray-900">{{ $authority->name }}</span>.
                    </p>
                    <p class="text-gray-600">
                        Dokumen telah Anda setujui dan akan diproses ke tahap selanjutnya.
                    </p>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">📋 No. Dokumen:</span>
                            <span class="font-semibold text-gray-800">{{ $document->request_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">👤 Pemohon:</span>
                            <span class="font-semibold text-gray-800">{{ $document->applicant_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">📅 Tanggal:</span>
                            <span class="font-semibold text-gray-800">{{ now()->format('d F Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">✅ Status:</span>
                            <span class="font-semibold text-green-600">Disetujui</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">📌 Langkah Selanjutnya:</h3>
                    <ul class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-start">
                            <span class="mr-2">1.</span>
                            <span>Admin akan menghubungi Anda untuk proses penandatanganan digital</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">2.</span>
                            <span>Anda akan menerima konfirmasi via WhatsApp setelah dokumen selesai diproses</span>
                        </li>
                    </ul>
                </div>

                {{-- ✅ FIXED: Smart close button with fallback --}}
                <button id="closeButton" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition shadow-lg">
                    Tutup Halaman
                </button>

                {{-- Alternative: Back to Home --}}
                <a href="{{ route('home') }}" class="block w-full text-center mt-3 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        @else
        {{-- ❌ REJECTED --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-8 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4">
                    <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">✅ Penolakan Tercatat</h1>
                <p class="text-red-50">Verifikasi telah diproses</p>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <p class="text-lg text-gray-700 mb-4">
                        Terima kasih, <span class="font-semibold text-gray-900">{{ $authority->name }}</span>.
                    </p>
                    <p class="text-gray-600">
                        Dokumen telah Anda tolak. Alasan telah dicatat dan dikirim ke admin serta pemohon.
                    </p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">📋 No. Dokumen:</span>
                            <span class="font-semibold text-gray-800">{{ $document->request_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">👤 Pemohon:</span>
                            <span class="font-semibold text-gray-800">{{ $document->applicant_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">📅 Tanggal:</span>
                            <span class="font-semibold text-gray-800">{{ now()->format('d F Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">❌ Status:</span>
                            <span class="font-semibold text-red-600">Ditolak</span>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-yellow-900 mb-2">📝 Alasan Penolakan:</h3>
                    <p class="text-yellow-800">{{ $reason }}</p>
                </div>

                {{-- ✅ FIXED: Smart close button with fallback --}}
                <button id="closeButton" class="w-full px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition shadow-lg">
                    Tutup Halaman
                </button>

                {{-- Alternative: Back to Home --}}
                <a href="{{ route('home') }}" class="block w-full text-center mt-3 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        @endif

        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>SIPETER - Sistem Persuratan Terpadu</p>
            <p class="mt-1">© 2024 STABA Bandung. All rights reserved.</p>
        </div>
    </div>
</div>

{{-- ✅ FIXED: Smart JavaScript for closing window --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const closeButton = document.getElementById('closeButton');

    if (closeButton) {
        closeButton.addEventListener('click', function() {
            // Try to close the window
            window.close();

            // If window.close() doesn't work (opened from external link),
            // show message and redirect after delay
            setTimeout(function() {
                // Check if window is still open
                if (!window.closed) {
                    // Show toast notification
                    showToast('Halaman akan diarahkan ke beranda dalam 2 detik...');

                    // Redirect to home after 2 seconds
                    setTimeout(function() {
                        window.location.href = '{{ route("home") }}';
                    }, 2000);
                }
            }, 100);
        });
    }

    setTimeout(function() {
        if (confirm('Halaman ini akan ditutup otomatis. Klik OK untuk kembali ke beranda sekarang.')) {
            window.location.href = '{{ route("home") }}';
        }
    }, 60000); // 60 seconds
});

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(function() {
        toast.remove();
    }, 3000);
}
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
