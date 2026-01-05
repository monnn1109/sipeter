@extends('layouts.guest')

@section('title', 'Link Tidak Valid')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-gray-500 to-gray-600 p-8 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4">
                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">⚠️ Link Tidak Valid</h1>
                <p class="text-gray-100">Verifikasi tidak dapat diproses</p>
            </div>

            <div class="p-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-gray-700 text-center">{{ $message }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">🔍 Kemungkinan Penyebab:</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <span>Link verifikasi sudah expired (lewat dari 3 hari)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <span>Verifikasi sudah pernah dilakukan sebelumnya</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <span>Link yang digunakan tidak valid</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">📞 Butuh Bantuan?</h3>
                    <p class="text-sm text-blue-800 mb-2">Silakan hubungi admin jika Anda merasa ini adalah kesalahan:</p>
                    <div class="space-y-1 text-sm text-blue-700">
                        <p>📧 Email: admin@sipeter.ac.id</p>
                        <p>📱 WhatsApp: +62 812-3456-7890</p>
                    </div>
                </div>

                <button onclick="window.close()" class="w-full px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition">
                    Tutup Halaman
                </button>
            </div>
        </div>

        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>SIPETER - Sistem Persuratan Terpadu</p>
        </div>
    </div>
</div>
@endsection
