@php
use App\Enums\DocumentStatus;
use App\Enums\DeliveryMethod;
@endphp

@extends('layouts.admin')

@section('title', 'Detail Dokumen - ' . $documentRequest->request_code)

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- HEADER --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Detail Permohonan</h2>
            <p class="mt-1 text-gray-600">{{ $documentRequest->request_code }}</p>
        </div>
        <a href="{{ route('admin.documents.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    {{-- SECTION: PROGRESS VERIFIKASI 3-LEVEL --}}
    @if($documentRequest->current_verification_step > 0 || $documentRequest->verifications->count() > 0)
        <div class="mb-6 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Progress Verifikasi 3-Level
            </h3>
            @include('components.verification-progress-3level', [
                'document' => $documentRequest,
                'showDetails' => true,
                'compact' => false
            ])
        </div>
    @endif

    {{-- SECTION: PROGRESS TTD 3-LEVEL --}}
    @if($documentRequest->signatures->count() > 0)
        <div class="mb-6 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Progress Tanda Tangan Digital 3-Level
            </h3>
            @include('components.signature-progress-3level', [
                'document' => $documentRequest,
                'showDetails' => true,
                'compact' => false
            ])
        </div>
    @endif

    {{-- SECTION: ADMIN ACTIONS UNTUK VERIFIKASI TTD --}}
    @php
        $uploadedSignatures = $documentRequest->signatures()->where('status', 'uploaded')->get();
        $verifiedSignatures = $documentRequest->signatures()->where('status', 'verified')->get();
        $allSignaturesVerified = $documentRequest->signatures()->count() === 3
            && $verifiedSignatures->count() === 3;
    @endphp

    @if($uploadedSignatures->count() > 0)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-yellow-800 mb-3">
                        ⚠️ Ada TTD yang Perlu Diverifikasi!
                    </h4>
                    <div class="space-y-4">
                        @foreach($uploadedSignatures as $signature)
                            <div class="bg-white rounded-lg border border-yellow-200 p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900 mb-1">
                                            TTD Level {{ $signature->signature_level }}: {{ $signature->authority->name ?? 'N/A' }}
                                        </h5>
                                        <p class="text-sm text-gray-600">
                                            {{ $signature->authority->position ?? 'N/A' }}
                                            ({{ $signature->authority->getAuthorityTypeLabel() }})
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            📤 Diupload: {{ $signature->uploaded_at->format('d/m/Y H:i') }} WIB
                                        </p>
                                    </div>
                                    <div class="flex flex-col gap-2 ml-4">
                                        <button onclick="toggleSignaturePreview({{ $signature->id }})"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Preview
                                        </button>

                                        {{-- Preview Container (Hidden by default) --}}
                                        <div id="preview-{{ $signature->id }}" class="hidden mt-3 p-4 bg-white border-2 border-blue-300 rounded-lg shadow-lg">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-semibold text-gray-900">Preview TTD Level {{ $signature->signature_level }}</h4>
                                                <button onclick="toggleSignaturePreview({{ $signature->id }})" class="text-gray-500 hover:text-gray-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            @if($signature->signature_file && Storage::disk('public')->exists($signature->signature_file))
                                                <div class="bg-gray-50 p-4 rounded border">
                                                    <img src="{{ asset('storage/' . $signature->signature_file) }}"
                                                         alt="TTD {{ $signature->authority->name }}"
                                                         class="max-w-full h-auto max-h-48 mx-auto"
                                                         onerror="this.parentElement.innerHTML='<p class=text-red-600>Error loading image</p>'">
                                                </div>
                                                @if($signature->qr_code_file && Storage::disk('public')->exists($signature->qr_code_file))
                                                    <div class="mt-3 bg-gray-50 p-4 rounded border">
                                                        <p class="text-xs text-gray-600 mb-2">QR Code:</p>
                                                        <img src="{{ asset('storage/' . $signature->qr_code_file) }}"
                                                             alt="QR Code"
                                                             class="max-w-32 h-auto mx-auto">
                                                    </div>
                                                @endif
                                            @else
                                                <p class="text-gray-500 text-sm">File tidak ditemukan</p>
                                            @endif
                                        </div>

                                        <button onclick="showVerifySignatureModal({{ $signature->id }}, {{ $signature->signature_level }})"
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Verifikasi
                                        </button>
                                        <button onclick="showRejectSignatureModal({{ $signature->id }}, {{ $signature->signature_level }})"
                                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Tolak
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- SECTION: DOWNLOAD TTD FILES --}}
    @if($verifiedSignatures->count() > 0)
        <div class="mb-6 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                📥 Download TTD untuk Embed Manual
            </h3>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800">
                    <strong>ℹ️ Instruksi:</strong> Download semua TTD yang sudah terverifikasi di bawah ini,
                    kemudian gunakan Adobe Acrobat / Foxit Reader untuk embed TTD ke PDF template secara manual.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($verifiedSignatures as $signature)
                    <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-green-700">LEVEL {{ $signature->signature_level }}</div>
                                    <div class="text-sm font-bold text-gray-900">{{ $signature->authority->getAuthorityTypeShortLabel() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-600 mb-3">
                            <strong>{{ $signature->authority->name }}</strong><br>
                            {{ $signature->authority->position }}
                        </div>
                        <div class="space-y-2">
                            @if($signature->signature_file && Storage::disk('public')->exists($signature->signature_file))
                                <a href="{{ asset('storage/' . $signature->signature_file) }}"
                                   target="_blank"
                                   download
                                   class="block w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors text-center">
                                    📄 Download TTD
                                </a>
                            @endif
                            @if($signature->qr_code_file && Storage::disk('public')->exists($signature->qr_code_file))
                                <a href="{{ asset('storage/' . $signature->qr_code_file) }}"
                                   target="_blank"
                                   download
                                   class="block w-full px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded transition-colors text-center">
                                    📲 Download QR Code
                                </a>
                            @endif
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            ✅ Verified: {{ $signature->verified_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if($allSignaturesVerified)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-700 mb-3">
                        💡 <strong>Tip:</strong> Anda juga bisa download semua TTD sebagai ZIP untuk kemudahan.
                    </p>
                    <p class="text-xs text-gray-500">
                        Total file: 6 files (3 TTD + 3 QR Code)
                    </p>
                </div>
            @endif
        </div>
    @endif

    {{-- 🔥 SECTION: UPLOAD DOKUMEN FINAL (Hanya untuk DOWNLOAD ONLINE, atau PICKUP yang belum upload) --}}
    @if($allSignaturesVerified && !$documentRequest->file_path)
        @php
            $isDownloadMethod = $documentRequest->delivery_method->value === 'download';
        @endphp

        {{-- Hanya tampilkan section ini untuk DOWNLOAD ONLINE --}}
        @if($isDownloadMethod)
            <div class="mb-6 bg-gradient-to-r from-green-50 to-blue-50 border-l-4 border-green-500 rounded-r-lg p-6 shadow-lg">
                <div class="flex items-start">
                    <svg class="w-8 h-8 text-green-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            🎉 Semua TTD Sudah Terverifikasi!
                        </h3>
                        <p class="text-gray-700 mb-4">
                            Langkah terakhir: <strong>Embed 3 TTD ke PDF template</strong> secara manual menggunakan Adobe Acrobat / Foxit Reader,
                            kemudian upload PDF final di bawah ini.
                        </p>

                        <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Instruksi Embed TTD Manual:
                            </h4>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                <li>Download semua TTD (3 file signature + 3 file QR Code) dari section di atas</li>
                                <li>Buka PDF template dokumen menggunakan <strong>Adobe Acrobat Pro / Foxit PhantomPDF</strong></li>
                                <li>Isi data pemohon (Nama, NIM, Keperluan, dll) ke dalam template</li>
                                <li>Insert/Embed gambar TTD Level 1 (Pa Riko) + QR Code ke kolom TTD Level 1</li>
                                <li>Insert/Embed gambar TTD Level 2 (Pa Firman) + QR Code ke kolom TTD Level 2</li>
                                <li>Insert/Embed gambar TTD Level 3 (Bu Rani) + QR Code ke kolom TTD Level 3</li>
                                <li>Save PDF dengan nama: <code class="bg-gray-100 px-2 py-1 rounded">{{ $documentRequest->request_code }}_Final.pdf</code></li>
                                <li>Upload PDF final tersebut di form di bawah ini</li>
                            </ol>
                        </div>

                        <button onclick="showUploadFinalDocumentModal()"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors flex items-center gap-2 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            📤 Upload Dokumen Final (Sudah Ter-embed TTD)
                        </button>
                        <p class="text-xs text-gray-600 mt-2">
                            ⚠️ <strong>Pastikan</strong> PDF sudah ter-embed dengan 3 TTD sebelum upload!
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT COLUMN: Document Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Informasi Dokumen
                </h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis Dokumen</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->documentType->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$documentRequest->status" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Metode Pengambilan</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">
                            @if($documentRequest->delivery_method === 'download' || $documentRequest->delivery_method->value === 'download')
                                <span class="flex items-center gap-2 text-green-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    📥 Download Online
                                </span>
                            @else
                                <span class="flex items-center gap-2 text-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    📦 Pickup Fisik (Ambil di Tempat)
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Keperluan</dt>
                        <dd class="mt-1 text-base text-gray-800">{{ $documentRequest->purpose }}</dd>
                    </div>

                    @if($documentRequest->notes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catatan Pemohon</dt>
                            <dd class="mt-1 text-base text-gray-800 italic bg-gray-50 p-3 rounded-lg">"{{ $documentRequest->notes }}"</dd>
                        </div>
                    @endif

                    @if($documentRequest->file_path)
                        <div class="border-t pt-4">
                            <dt class="text-sm font-medium text-gray-500 mb-2">File Dokumen</dt>
                            <dd class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $documentRequest->getFileName() }}</p>
                                    <p class="text-sm text-gray-600">Diupload: {{ $documentRequest->file_uploaded_at?->format('d/m/Y H:i') }}</p>
                                </div>
                                <a href="{{ route('admin.documents.download', $documentRequest->id) }}"
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </dd>
                        </div>
                    @endif

                    @if($documentRequest->admin_notes)
                        <div class="border-t pt-4">
                            <dt class="text-sm font-medium text-gray-500">Catatan Admin</dt>
                            <dd class="mt-1 text-base text-gray-800 bg-yellow-50 p-3 rounded-lg whitespace-pre-line">{{ $documentRequest->admin_notes }}</dd>
                        </div>
                    @endif

                    @if($documentRequest->status->value === 'rejected' && $documentRequest->rejection_reason)
                        <div class="border-t pt-4">
                            <dt class="text-sm font-medium text-gray-500">Alasan Penolakan</dt>
                            <dd class="mt-1 text-base text-red-700 bg-red-50 p-3 rounded-lg font-semibold">{{ $documentRequest->rejection_reason }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Informasi Pemohon
                </h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipe Pemohon</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                {{ $documentRequest->applicant_type->value === 'mahasiswa' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($documentRequest->applicant_type->value) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            {{ $documentRequest->applicant_type->value === 'mahasiswa' ? 'NIM' : 'NIP/NIDN' }}
                        </dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_identifier }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_email }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nomor WhatsApp</dt>
                        <dd class="mt-1">
                            <a href="https://wa.me/{{ $documentRequest->applicant_phone }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 font-semibold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                {{ $documentRequest->applicant_phone }}
                            </a>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit/Departemen</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_unit }}</dd>
                    </div>

                    @if($documentRequest->applicant_address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-base text-gray-800">{{ $documentRequest->applicant_address }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- 🔥 SECTION TINDAKAN - FINAL FIX (No More Loop!) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Tindakan
            </h3>

            <div class="space-y-3">
                @php
                    $status = $documentRequest->status->value;
                    $currentStep = $documentRequest->current_verification_step ?? 0;
                    $deliveryMethod = $documentRequest->delivery_method->value;
                    $isPickup = $deliveryMethod === 'pickup';
                    $isDownload = $deliveryMethod === 'download';

                    // Check all signatures verified
                    $verifiedSignaturesCount = $documentRequest->signatures()
                        ->where('status', 'verified')
                        ->count();
                    $allSignaturesVerified = $verifiedSignaturesCount === 3;

                    // Check if document has file
                    $hasFile = $documentRequest->hasFile();
                @endphp

                {{-- ✅ STATUS: COMPLETED atau PICKED_UP (FINAL STATE - NO MORE ACTIONS!) --}}
                @if(in_array($status, ['completed', 'picked_up']))
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-green-700">🎉 Selesai</p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($isPickup)
                                Dokumen telah diserahkan kepada pemohon (Pickup Fisik)
                            @else
                                Dokumen telah didownload dan dikonfirmasi oleh pemohon
                            @endif
                        </p>
                    </div>

                {{-- ✅ PENDING/SUBMITTED: Approve/Reject --}}
                @elseif(in_array($status, ['submitted', 'pending']))
                    <button onclick="showApproveModal()"
                            class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Setujui Dokumen
                    </button>

                    <button onclick="showRejectModal()"
                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tolak Dokumen
                    </button>

                {{-- ✅ APPROVED: Mulai Verifikasi 3-Level --}}
                @elseif($status === 'approved' && $currentStep === 0)
                    <a href="{{ route('admin.verifications.send.form', $documentRequest->id) }}"
                    class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        🚀 Mulai Verifikasi 3-Level
                    </a>
                    <p class="text-xs text-center text-gray-500 mt-2">
                        Dokumen sudah disetujui. Mulai proses verifikasi 3-level.
                    </p>

                {{-- ⏳ VERIFICATION IN PROGRESS --}}
                @elseif(in_array($status, [
                    'verification_step_1_requested',
                    'verification_step_2_requested',
                    'verification_step_3_requested'
                ]))
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-3">
                            <svg class="w-8 h-8 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700">⏳ Verifikasi Level {{ $currentStep }} Pending</p>
                        <p class="text-xs text-gray-500 mt-1">Menunggu approval dari pejabat</p>
                    </div>

                {{-- ✅ ALL VERIFICATION COMPLETED: Request TTD --}}
                @elseif($status === 'verification_step_3_approved' || ($currentStep === 3 && $documentRequest->verifications->where('verification_level', 3)->where('status', 'approved')->count() > 0))
                    @php
                        $hasSignatureRequest = $documentRequest->signatures()->exists();
                    @endphp

                    @if(!$hasSignatureRequest)
                        <form action="{{ route('admin.signatures.request', $documentRequest->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                ✍️ Request Tanda Tangan Digital
                            </button>
                        </form>
                        <p class="text-xs text-center text-gray-500 mt-2">
                            🎉 Semua verifikasi selesai! Lanjut ke TTD.
                        </p>
                    @else
                        {{-- TTD sudah direquest, cek apakah semua sudah verified --}}
                        @if($allSignaturesVerified)
                            {{-- 🔥 PEMBEDA: PICKUP vs DOWNLOAD --}}
                            @if($isPickup)
                                {{-- 📦 PICKUP FISIK --}}
                                @if($status === 'ready_for_pickup')
                                    {{-- Status READY: Tampilkan button "Tandai Sudah Diambil" --}}
                                    <button onclick="showPickedUpModal()"
                                            class="w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        ✅ Tandai Sudah Diambil
                                    </button>
                                    <p class="text-xs text-center text-gray-500 mt-2">
                                        📦 Verifikasi identitas dan serahkan dokumen fisik
                                    </p>
                                @else
                                    {{-- Belum ready: Tampilkan button "Tandai Siap Diambil" --}}
                                    <button onclick="showReadyForPickupModal()"
                                            class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        📦 Tandai Dokumen Siap Diambil
                                    </button>
                                    <p class="text-xs text-center text-purple-700 font-semibold mt-2 bg-purple-50 p-3 rounded-lg border border-purple-200">
                                        ✅ Semua TTD verified!<br>
                                        📥 Download TTD → Embed manual → Klik tombol ini
                                    </p>
                                @endif

                            @else
                                {{-- 📥 DOWNLOAD ONLINE --}}
                                @if(!$hasFile)
                                    {{-- Belum upload PDF: Tampilkan button "Upload Final" --}}
                                    <button onclick="showUploadFinalDocumentModal()"
                                            class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        📤 Upload Dokumen Final (Ter-embed TTD)
                                    </button>
                                    <p class="text-xs text-center text-green-700 font-semibold mt-2 bg-green-50 p-3 rounded-lg border border-green-200">
                                        ✅ Semua TTD verified!<br>
                                        📥 Download TTD → Embed manual → Upload PDF
                                    </p>
                                @else
                                    {{-- Sudah upload: Menunggu user download --}}
                                    <div class="text-center py-4">
                                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-3">
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-green-700">✅ Selesai (Admin)</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Menunggu user download & konfirmasi
                                        </p>
                                    </div>
                                @endif
                            @endif

                        @else
                            {{-- TTD masih dalam proses upload/verify --}}
                            <div class="text-center py-4">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-700">✍️ Proses Tanda Tangan</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Verified: {{ $verifiedSignaturesCount }}/3 TTD
                                </p>
                            </div>
                        @endif
                    @endif

                {{-- ❌ VERIFICATION REJECTED --}}
                @elseif($status === 'verification_rejected')
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-red-700">❌ Verifikasi Ditolak</p>
                        <p class="text-xs text-gray-500 mt-1">Proses dihentikan</p>
                    </div>

                {{-- ❌ REJECTED --}}
                @elseif($status === 'rejected')
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-red-700">❌ Ditolak</p>
                        <p class="text-xs text-gray-500 mt-1">Dokumen ditolak oleh admin</p>
                    </div>

                {{-- ⚠️ FALLBACK: Status lainnya --}}
                @else
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500">
                            Tidak ada tindakan untuk status
                            <span class="font-semibold block mt-1">{{ $documentRequest->status->label() }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            Status: {{ $status }}<br>
                            TTD Verified: {{ $verifiedSignaturesCount }}/3<br>
                            Delivery: {{ $deliveryMethod }}<br>
                            Has File: {{ $hasFile ? 'Yes' : 'No' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

            {{-- RIWAYAT AKTIVITAS --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Aktivitas
                </h3>
                @if($documentRequest->activities->count() > 0)
                    <x-tracking-timeline :activities="$documentRequest->activities" />
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada riwayat aktivitas</p>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- MODAL: Approve Document --}}
<div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideApproveModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900">Setujui Permohonan</h3>
        <p class="text-gray-600 mb-6">Anda yakin ingin menyetujui permohonan ini? Setelah disetujui, Anda perlu request verifikasi ke pejabat.</p>
        <form action="{{ route('admin.documents.approve', $documentRequest->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                <textarea name="admin_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideApproveModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                    Ya, Setujui
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Reject Document --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideRejectModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900">Tolak Permohonan</h3>
        <form action="{{ route('admin.documents.reject', $documentRequest->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Jelaskan alasan penolakan dengan jelas..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideRejectModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                    Tolak Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Upload Final Document --}}
<div id="uploadFinalDocumentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideUploadFinalDocumentModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            Upload Dokumen Final (Ter-embed 3 TTD)
        </h3>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-green-800 mb-2">
                <strong>✅ Semua TTD Verified!</strong>
            </p>
            <p class="text-xs text-green-700">
                Upload PDF yang sudah ter-embed dengan:<br>
                • TTD Level 1: Ketua Akademik<br>
                • TTD Level 2: Wakil Direktur 3<br>
                • TTD Level 3: Direktur
            </p>
        </div>

        <form action="{{ route('admin.upload.submit-final', $documentRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    File PDF <span class="text-red-500">*</span>
                </label>
                <input type="file" name="document_file" accept=".pdf" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Max 10MB, format PDF</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideUploadFinalDocumentModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload Dokumen Final
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Verify Signature --}}
<div id="verifySignatureModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideVerifySignatureModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900">✅ Verifikasi TTD</h3>
        <p class="text-gray-600 mb-6">Anda yakin TTD Level <span id="verifyLevelText"></span> sudah benar dan ingin diverifikasi?</p>
        <form id="verifySignatureForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                          placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideVerifySignatureModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                    Ya, Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Reject Signature --}}
<div id="rejectSignatureModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideRejectSignatureModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900">❌ Tolak TTD</h3>
        <p class="text-gray-600 mb-4">Tolak TTD Level <span id="rejectLevelText"></span> dan minta pejabat untuk upload ulang.</p>
        <form id="rejectSignatureForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Jelaskan alasan penolakan (contoh: TTD tidak jelas, QR Code rusak, dll)"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideRejectSignatureModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                    Tolak TTD
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 🔥 MODAL: Ready for Pickup (MANUAL - untuk PICKUP FISIK) --}}
<div id="readyForPickupModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideReadyForPickupModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900">📦 Tandai Siap Diambil</h3>
        <p class="text-gray-600 mb-6">Dokumen akan ditandai sebagai "Siap Diambil" dan pemohon akan menerima notifikasi WA.</p>
        <form action="{{ route('admin.documents.mark-ready-pickup', $documentRequest->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                          placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideReadyForPickupModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                    Ya, Tandai Siap Diambil
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Picked Up (untuk PICKUP FISIK) --}}
<div id="pickedUpModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hidePickedUpModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Tandai Sudah Diambil (Pickup Fisik)
        </h3>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-800">
                <strong>ℹ️ Instruksi:</strong> Verifikasi identitas mahasiswa (KTP/KTM/SIM) sebelum menyerahkan dokumen fisik.
            </p>
        </div>

        <form action="{{ route('admin.documents.mark-picked-up', $documentRequest->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Identitas <span class="text-red-500">*</span>
                </label>
                <select name="verification_id_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Jenis Identitas</option>
                    <option value="KTP">KTP</option>
                    <option value="KTM">KTM (Kartu Mahasiswa)</option>
                    <option value="SIM">SIM</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Identitas <span class="text-red-500">*</span>
                </label>
                <input type="text" name="verification_id_number" required
                       placeholder="Contoh: 3201234567890123"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3"
                          placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-xs text-gray-600 mb-2 font-semibold">Info Pemohon:</p>
                <div class="space-y-1 text-sm">
                    <p><strong>Nama:</strong> {{ $documentRequest->applicant_name }}</p>
                    <p><strong>NIM/NIP:</strong> {{ $documentRequest->applicant_identifier }}</p>
                    <p><strong>WhatsApp:</strong> {{ $documentRequest->applicant_phone }}</p>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hidePickedUpModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Ya, Sudah Diambil
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Completed --}}
<div id="completedModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideCompletedModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4 text-gray-900">Tandai Selesai</h3>
        <p class="text-gray-600 mb-6">Dokumen akan ditandai sebagai selesai dan tidak dapat diubah lagi.</p>
        <form action="{{ route('admin.documents.update-status', $documentRequest->id) }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="completed">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Penutup (Opsional)</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                          placeholder="Tambahkan catatan penutup..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideCompletedModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                    Ya, Tandai Selesai
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Include existing upload modal --}}
@include('admin.documents.upload-modal', ['documentRequest' => $documentRequest])

@push('scripts')
<script>
    // Modal functions
    function showApproveModal() { document.getElementById('approveModal').classList.remove('hidden'); }
    function hideApproveModal() { document.getElementById('approveModal').classList.add('hidden'); }

    function showRejectModal() { document.getElementById('rejectModal').classList.remove('hidden'); }
    function hideRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

    function showUploadModal() { document.getElementById('uploadModal').classList.remove('hidden'); }
    function hideUploadModal() { document.getElementById('uploadModal').classList.add('hidden'); }

    function showUploadFinalDocumentModal() { document.getElementById('uploadFinalDocumentModal').classList.remove('hidden'); }
    function hideUploadFinalDocumentModal() { document.getElementById('uploadFinalDocumentModal').classList.add('hidden'); }

    function showReadyForPickupModal() { document.getElementById('readyForPickupModal').classList.remove('hidden'); }
    function hideReadyForPickupModal() { document.getElementById('readyForPickupModal').classList.add('hidden'); }

    function showPickedUpModal() { document.getElementById('pickedUpModal').classList.remove('hidden'); }
    function hidePickedUpModal() { document.getElementById('pickedUpModal').classList.add('hidden'); }

    function showCompletedModal() { document.getElementById('completedModal').classList.remove('hidden'); }
    function hideCompletedModal() { document.getElementById('completedModal').classList.add('hidden'); }

    // Signature modals
    function toggleSignaturePreview(signatureId) {
        const previewDiv = document.getElementById('preview-' + signatureId);
        if (previewDiv.classList.contains('hidden')) {
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
        }
    }

    function showVerifySignatureModal(signatureId, level) {
        const modal = document.getElementById('verifySignatureModal');
        const form = document.getElementById('verifySignatureForm');
        const levelText = document.getElementById('verifyLevelText');

        levelText.textContent = level;
        form.action = `/admin/signatures/${signatureId}/approve`;

        modal.classList.remove('hidden');
    }
    function hideVerifySignatureModal() {
        document.getElementById('verifySignatureModal').classList.add('hidden');
    }

    function showRejectSignatureModal(signatureId, level) {
        const modal = document.getElementById('rejectSignatureModal');
        const form = document.getElementById('rejectSignatureForm');
        const levelText = document.getElementById('rejectLevelText');

        levelText.textContent = level;
        form.action = `/admin/signatures/${signatureId}/reject`;

        modal.classList.remove('hidden');
    }
    function hideRejectSignatureModal() {
        document.getElementById('rejectSignatureModal').classList.add('hidden');
    }

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideApproveModal();
            hideRejectModal();
            hideUploadModal();
            hideUploadFinalDocumentModal();
            hideReadyForPickupModal();
            hidePickedUpModal();
            hideCompletedModal();
            hideVerifySignatureModal();
            hideRejectSignatureModal();
        }
    });
</script>
@endpush
@endsection
