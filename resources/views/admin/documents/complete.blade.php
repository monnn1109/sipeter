@extends('layouts.guest')

@section('title', 'Dokumen Selesai')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full">
        <!-- Success Animation -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full mb-6 animate-bounce shadow-2xl">
                <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-4xl font-black text-gray-900 mb-3">Dokumen Selesai!</h1>
            <p class="text-lg text-gray-600">Proses pengajuan dokumen Anda telah selesai</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Kode Dokumen</p>
                        <p class="text-2xl font-bold">{{ $document->document_code }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">Status</p>
                        <span class="inline-flex items-center px-4 py-2 bg-white text-green-600 rounded-full text-sm font-bold shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            SELESAI
                        </span>
                    </div>
                </div>
            </div>

            <!-- Document Info -->
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 mb-2">Jenis Dokumen</p>
                        <p class="text-lg font-bold text-gray-900">{{ $document->documentType->name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 mb-2">Pemohon</p>
                        <p class="text-lg font-bold text-gray-900">{{ $document->applicant_name }}</p>
                        <p class="text-sm text-gray-600">{{ $document->applicant_identifier }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 mb-2">Metode Pengambilan</p>
                        <p class="text-lg font-bold text-gray-900">
                            @if($document->delivery_method === 'download')
                                📥 Download Digital
                            @else
                                🏢 Pickup di TU
                            @endif
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 mb-2">Tanggal Selesai</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $document->completed_at ? $document->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Ringkasan Proses
                    </h3>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Request Submitted</p>
                                    <p class="text-xs text-gray-600">{{ $document->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            @if($document->approved_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Admin Approved</p>
                                    <p class="text-xs text-gray-600">{{ $document->approved_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @endif

                            @if($document->verified_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Verified</p>
                                    <p class="text-xs text-gray-600">{{ $document->verified_at->format('d M Y, H:i') }}</p>
                                    @if($document->verified_by)
                                        <p class="text-xs text-gray-500">oleh {{ $document->verified_by }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($document->signature_verified_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Signature Verified</p>
                                    <p class="text-xs text-gray-600">{{ $document->signature_verified_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @endif

                            @if($document->file_uploaded_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Document Ready</p>
                                    <p class="text-xs text-gray-600">{{ $document->file_uploaded_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @endif

                            @if($document->marked_as_taken_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Document Received</p>
                                    <p class="text-xs text-gray-600">{{ $document->marked_as_taken_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-4">
                    @if($document->hasFile() && $document->isDownloadable())
                        <a href="{{ route('guest.documents.download', $document->id) }}"
                           class="block w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Dokumen
                        </a>
                    @endif

                    <a href="{{ route('tracking.show', $document->id) }}"
                       class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-4 px-6 rounded-xl text-center border-2 border-gray-200 transition-all">
                        <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Lihat Detail Tracking
                    </a>

                    <a href="{{ route('home') }}"
                       class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-xl text-center transition-all">
                        <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Butuh Bantuan?
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div>
                            <p class="text-gray-600">Telepon/WhatsApp</p>
                            <p class="font-semibold text-gray-900">{{ env('STABA_PHONE', '0361-123456') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">{{ env('STABA_EMAIL', 'akademik@staba.ac.id') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Office Hours -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Jam Layanan
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Senin - Jumat</span>
                        <span class="font-semibold text-gray-900">08.00 - 16.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sabtu</span>
                        <span class="font-semibold text-gray-900">08.00 - 12.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Minggu</span>
                        <span class="font-semibold text-red-600">Tutup</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="text-center bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl shadow-lg p-8">
            <h3 class="text-2xl font-bold mb-2">Terima Kasih!</h3>
            <p class="text-green-50">
                Terima kasih telah menggunakan layanan SIPETER.<br>
                Semoga dokumen Anda bermanfaat.
            </p>
        </div>
    </div>
</div>

<style>
@keyframes bounce {
    0%, 100% {
        transform: translateY(-5%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>
@endsection
