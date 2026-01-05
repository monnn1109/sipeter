@php
use App\Enums\DocumentStatus;
use App\Enums\DeliveryMethod;
@endphp

@extends('layouts.internal')

@section('title', 'Detail Dokumen - ' . $documentRequest->request_code)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Detail Permohonan</h2>
            <p class="mt-1 text-gray-600">{{ $documentRequest->request_code }}</p>
        </div>
        <a href="{{ route('internal.my-documents.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    {{-- ✅ NEW: 3-Level Verification Progress Component --}}
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Dokumen</h3>
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
                            @if($documentRequest->delivery_method === 'download')
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download Online
                                </span>
                            @else
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 4h5m-5 4h5m-5 4h5"></path>
                                    </svg>
                                    Ambil di Tempat
                                </span>
                            @endif
                        </dd>
                    </div>

                    @if($documentRequest->purpose)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Keperluan</dt>
                            <dd class="mt-1 text-base text-gray-800">{{ $documentRequest->purpose }}</dd>
                        </div>
                    @endif

                    @if($documentRequest->notes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catatan Pemohon</dt>
                            <dd class="mt-1 text-base text-gray-800 italic">"{{ $documentRequest->notes }}"</dd>
                        </div>
                    @endif

                    @if($documentRequest->status->value === 'rejected' && $documentRequest->rejection_reason)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Alasan Penolakan</dt>
                            <dd class="mt-1 text-base text-red-600">{{ $documentRequest->rejection_reason }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($documentRequest->signature_status && $documentRequest->documentSignatures->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                    </svg>
                    Status Tanda Tangan
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Status Keseluruhan:</span>
                        <x-signature-status-badge :status="$documentRequest->signature_status" />
                    </div>

                    @php
                        $totalSignatures = $documentRequest->documentSignatures->count();
                        $completedSignatures = $documentRequest->documentSignatures
                            ->where('signature_status', 'SIGNATURE_VERIFIED')
                            ->count();
                        $progressPercent = $totalSignatures > 0 ? ($completedSignatures / $totalSignatures) * 100 : 0;
                    @endphp

                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-600">Progress:</span>
                            <span class="text-xs font-bold text-gray-900">{{ $completedSignatures }}/{{ $totalSignatures }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    <div class="pt-3 space-y-2">
                        <p class="text-xs font-medium text-gray-600 mb-2">Daftar Pejabat:</p>
                        @foreach($documentRequest->documentSignatures as $signature)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">
                                        {{ $signature->authority->user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $signature->authority->authority_type->label() }}
                                    </p>
                                </div>
                                <x-signature-status-badge :status="$signature->signature_status" size="sm" />
                            </div>
                            @if($signature->signed_at)
                            <p class="text-xs text-gray-500 mt-1">
                                Ditandatangani: {{ $signature->signed_at->format('d M Y, H:i') }} WIB
                            </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Riwayat Dokumen</h3>
                @if($documentRequest->activities->count() > 0)
                    <x-tracking-timeline :activities="$documentRequest->activities" />
                @else
                    <p class="text-center text-gray-500 py-4">Belum ada riwayat aktivitas</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            @if($documentRequest->delivery_method === 'download')
                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="text-lg font-semibold mb-4">Tindakan</h3>

                    @if($documentRequest->file_path &&
                        $documentRequest->file_uploaded_at &&
                        in_array($documentRequest->status->value, ['ready_for_pickup', 'picked_up', 'completed']))

                        <div class="space-y-3">
                            <a href="{{ route('internal.my-documents.download', $documentRequest->id) }}"
                               class="block w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-center transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Dokumen
                            </a>

                            @if($documentRequest->status->value !== 'completed')
                                <form action="{{ route('internal.my-documents.mark-as-taken', $documentRequest->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin sudah mendownload dan menyimpan dokumen ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-center transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Tandai Sudah Diambil
                                    </button>
                                </form>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="font-medium text-gray-900 text-sm">✅ Sudah Dikonfirmasi</p>
                                    </div>
                                </div>
                            @endif

                            <p class="text-xs text-gray-500 text-center">
                                Tersedia sejak: {{ $documentRequest->file_uploaded_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                    @else
                        @if($documentRequest->status->value === 'pending')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-blue-900 text-sm">⏳ Menunggu Persetujuan</p>
                                        <p class="text-xs text-blue-800 mt-1">
                                            Permohonan Anda sedang dalam antrian review admin.
                                        </p>
                                        <p class="text-xs text-blue-700 mt-2">
                                            Diajukan: {{ $documentRequest->created_at->format('d M Y, H:i') }} WIB
                                        </p>
                                    </div>
                                </div>
                            </div>

                        @elseif($documentRequest->status->value === 'approved')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-green-900 text-sm">✅ Permohonan Disetujui</p>
                                        <p class="text-xs text-green-800 mt-1">
                                            Admin sedang memproses dan akan meng-upload dokumen Anda segera.
                                        </p>
                                        @if($documentRequest->approved_at)
                                            <p class="text-xs text-green-700 mt-2">
                                                Disetujui: {{ $documentRequest->approved_at->format('d M Y, H:i') }} WIB
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @elseif($documentRequest->status->value === 'rejected')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-red-900 text-sm">❌ Permohonan Ditolak</p>
                                        <p class="text-xs text-red-800 mt-1">
                                            Dokumen tidak dapat diproses. Silakan hubungi admin untuk informasi lebih lanjut.
                                        </p>
                                        @if($documentRequest->rejection_reason)
                                            <p class="text-xs text-red-700 mt-2 font-medium">
                                                Alasan: {{ $documentRequest->rejection_reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-yellow-900 text-sm">📄 Dokumen Belum Tersedia</p>
                                        <p class="text-xs text-yellow-800 mt-1">
                                            Admin sedang memproses dokumen Anda. Silakan cek kembali nanti.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-lg font-semibold mb-4">Ringkasan Status</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Diajukan</span>
                        <span class="font-medium text-gray-900">{{ $documentRequest->created_at->format('d M Y') }}</span>
                    </div>
                    @if($documentRequest->approved_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Disetujui</span>
                            <span class="font-medium text-green-600">{{ $documentRequest->approved_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    @if($documentRequest->file_uploaded_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">File Diupload</span>
                            <span class="font-medium text-blue-600">{{ $documentRequest->file_uploaded_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    @if($documentRequest->ready_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Siap Diambil</span>
                            <span class="font-medium text-purple-600">{{ $documentRequest->ready_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    @if($documentRequest->completed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Selesai</span>
                            <span class="font-medium text-gray-900">{{ $documentRequest->completed_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Butuh Bantuan?</h3>
                <p class="text-xs text-blue-800">
                    Hubungi admin jika ada pertanyaan tentang permohonan Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
