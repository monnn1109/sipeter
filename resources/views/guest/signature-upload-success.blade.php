@extends('layouts.guest')

@section('title', 'Upload Berhasil - SIPETER')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="text-center py-12 px-6">
                <!-- Success Icon -->
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full mb-6 shadow-lg animate-bounce">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-3">✅ Upload Tanda Tangan Berhasil!</h1>
                <p class="text-gray-600 mb-8">Tanda tangan digital Anda telah berhasil diunggah ke sistem</p>

                <!-- Document Info -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6 mb-6 text-left">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">📄 Informasi Dokumen</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Kode:</span>
                            <p class="font-semibold text-gray-900">{{ $documentRequest->request_code }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Jenis:</span>
                            <p class="font-semibold text-gray-900">{{ $documentRequest->documentType->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Level TTD:</span>
                            <p class="font-semibold text-purple-600">Level {{ $signature->signature_level ?? 1 }} / 3</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Waktu Upload:</span>
                            <p class="font-semibold text-gray-900">{{ $signature->uploaded_at ? $signature->uploaded_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Authority Info -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6 text-left">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">👤 Pejabat yang Menandatangani</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama:</span>
                            <span class="font-semibold text-gray-900">{{ $authority->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jabatan:</span>
                            <span class="font-semibold text-gray-900">{{ $authority->position }}</span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
                    <h4 class="text-sm font-semibold text-yellow-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Langkah Selanjutnya
                    </h4>
                    <p class="text-sm text-yellow-800">
                        @if(($signature->signature_level ?? 1) < 3)
                            ⏳ Sistem akan <strong>otomatis mengirim</strong> permintaan tanda tangan ke
                            <strong>Level {{ ($signature->signature_level ?? 1) + 1 }}</strong>.
                            <br><br>
                            Pejabat Level berikutnya akan menerima notifikasi WhatsApp dalam beberapa saat.
                        @else
                            🎉 <strong>Semua tanda tangan sudah lengkap!</strong>
                            <br><br>
                            Admin akan memverifikasi dan memproses dokumen final. Pemohon akan mendapatkan notifikasi setelah dokumen siap.
                        @endif
                    </p>
                </div>

                <!-- Progress Bar -->
                @php
                    $progress = (($signature->signature_level ?? 1) / 3) * 100;
                @endphp
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress TTD</span>
                        <span class="font-semibold">{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-500"
                             style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span class="{{ ($signature->signature_level ?? 1) >= 1 ? 'text-green-600 font-semibold' : '' }}">
                            ✓ Level 1
                        </span>
                        <span class="{{ ($signature->signature_level ?? 1) >= 2 ? 'text-green-600 font-semibold' : '' }}">
                            {{ ($signature->signature_level ?? 1) >= 2 ? '✓' : '○' }} Level 2
                        </span>
                        <span class="{{ ($signature->signature_level ?? 1) >= 3 ? 'text-green-600 font-semibold' : '' }}">
                            {{ ($signature->signature_level ?? 1) >= 3 ? '✓' : '○' }} Level 3
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Terima kasih atas partisipasi Anda dalam sistem SIPETER
            </p>
            <p class="text-xs text-gray-500 mt-2">
                Jika ada pertanyaan, silakan hubungi admin di
                <a href="tel:+6282295837826" class="text-purple-600 hover:underline">+62 822-9583-7826</a>
            </p>
        </div>
    </div>
</div>
@endsection
