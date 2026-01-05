@extends('layouts.guest')

@section('title', $title ?? 'Upload Error')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 px-8 py-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">{{ $title ?? 'Terjadi Kesalahan' }}</h1>
            </div>

            <div class="px-8 py-8">
                <div class="mb-6">
                    <p class="text-gray-700 text-center">{{ $message ?? 'Terjadi kesalahan pada sistem.' }}</p>
                </div>

                @if(isset($expired_at))
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Link kadaluarsa pada:</p>
                                <p class="text-sm text-yellow-700">{{ $expired_at }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($signature) && $signature->uploaded_at)
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-green-800">Status Upload</p>
                                <p class="text-sm text-green-700">
                                    Tanda tangan sudah diupload pada {{ $signature->uploaded_at->format('d M Y H:i') }}
                                </p>
                                @if($signature->verified_at)
                                    <p class="text-sm text-green-700 mt-1">
                                        @if($signature->is_verified)
                                            ✅ Sudah diverifikasi oleh Admin
                                        @else
                                            ❌ Ditolak oleh Admin
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(isset($can_view) && $can_view)
                        <div class="mb-6">
                            <a href="{{ route('guest.signature.preview', $signature->signature_token) }}"
                               class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                                Lihat Upload Saya
                            </a>
                        </div>
                    @endif
                @endif

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-900 mb-2">Butuh Bantuan?</p>
                    <p class="text-sm text-gray-600 mb-3">Silakan hubungi Admin TU untuk link upload baru</p>
                    <div class="flex items-center justify-center space-x-4 text-xs text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            (022) 123-4567
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            admin@staba.ac.id
                        </div>
                    </div>
                </div>

                {{-- ✅ BONUS: Tombol kembali ke home --}}
                <div class="mt-6">
                    <a href="{{ route('home') }}"
                       class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                        ← Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">SIPETER - Sistem Persuratan Terpadu</p>
            <p class="text-xs text-gray-500 mt-1">STABA Bandung</p>
        </div>
    </div>
</div>
@endsection
