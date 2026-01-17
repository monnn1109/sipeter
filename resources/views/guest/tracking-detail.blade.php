@extends('layouts.guest')

@section('title', 'Detail Tracking - ' . $documentRequest->request_code)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('mahasiswa.tracking') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Tracking
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $documentRequest->documentType->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Kode: {{ $documentRequest->request_code }}</p>
                </div>
                <x-status-badge :status="$documentRequest->status" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase">Informasi Dokumen</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ $documentRequest->applicant_type->value === 'mahasiswa' ? 'NIM' : 'NIP' }}</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_identifier }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kontak (WA)</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $documentRequest->applicant_phone }}</dd>
                        </div>
                        @if($documentRequest->purpose)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Keperluan</dt>
                            <dd class="mt-1 text-base text-gray-800">{{ $documentRequest->purpose }}</dd>
                        </div>
                        @endif
                        @if($documentRequest->notes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                            <dd class="mt-1 text-base text-gray-800 italic">"{{ $documentRequest->notes }}"</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase">Metode Pengambilan</h4>
                    <div>
                        @php
                            $deliveryMethod = $documentRequest->delivery_method?->value ?? 'pickup';
                        @endphp

                        @if($deliveryMethod === 'download')
                            <span class="flex items-center gap-2 text-base font-semibold text-gray-900">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Online
                            </span>
                        @else
                            <span class="flex items-center gap-2 text-base font-semibold text-gray-900">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Ambil di Tempat
                            </span>
                        @endif
                    </div>

                    @if($deliveryMethod === 'download' && $documentRequest->hasFile() && $documentRequest->isDownloadable())
                        <div class="pt-2 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase">Tindakan</h4>

                            <a href="{{ route('guest.documents.download', $documentRequest->id) }}"
                               class="block w-full px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-lg text-center transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Dokumen
                            </a>

                            @if($documentRequest->status->value !== 'completed')
                                <form action="{{ route('guest.documents.mark-as-taken', $documentRequest->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah mendownload dan menyimpan dokumen ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-lg text-center transition-all shadow-lg hover:shadow-xl">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Konfirmasi Sudah Diterima
                                    </button>
                                </form>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="font-medium text-gray-900 text-sm">✅ Sudah Dikonfirmasi</p>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2">
                                        Dokumen sudah dikonfirmasi diterima pada {{ $documentRequest->completed_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            @endif

                            @if($documentRequest->file_uploaded_at)
                                <p class="text-xs text-gray-500 text-center">
                                    File tersedia sejak: {{ $documentRequest->file_uploaded_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>

                    @elseif($deliveryMethod === 'download' && !$documentRequest->hasFile())
                        <div class="pt-2">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-yellow-900">Dokumen Sedang Diproses</p>
                                        <p class="text-sm text-yellow-800 mt-1">
                                            Admin sedang menyiapkan file dokumen Anda. Silakan cek kembali nanti.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($deliveryMethod === 'pickup' && in_array($documentRequest->status->value, ['ready_for_pickup', 'ready']))
                        <div class="pt-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-blue-900">📍 Dokumen Siap Diambil</p>
                                        <p class="text-sm text-blue-800 mt-2">
                                            <strong>Lokasi:</strong> Tata Usaha STABA Bandung<br>
                                            <strong>Jam:</strong> Senin - Jumat, 08:00 - 15:00 WIB<br>
                                            <strong>Bawa:</strong> KTM/KTP + Kode: {{ $documentRequest->request_code }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($documentRequest->admin_notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">Catatan Admin</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $documentRequest->admin_notes }}</p>
                    </div>
                </div>
            @endif

            @if($documentRequest->status->value === 'rejected' && $documentRequest->rejection_reason)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">Alasan Penolakan</h4>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800 font-semibold">{{ $documentRequest->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>

        @if($documentRequest->current_verification_step > 0 || $documentRequest->verifications->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
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

        @if($documentRequest->documentSignatures->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                </svg>
                Progress Tanda Tangan
            </h3>

            @php
                $totalSignatures = $documentRequest->documentSignatures->count();
                $completedSignatures = $documentRequest->documentSignatures->where('status.value', 'verified')->count();
                $progressPercent = $totalSignatures > 0 ? ($completedSignatures / $totalSignatures) * 100 : 0;
            @endphp

            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Progress Keseluruhan:</span>
                    <span class="text-sm font-bold text-gray-900">{{ $completedSignatures }}/{{ $totalSignatures }} Selesai</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-500"
                         style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($documentRequest->documentSignatures as $signature)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $signature->authority->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $signature->authority->authority_type->label() }}</p>
                            @if($signature->uploaded_at)
                            <p class="text-xs text-gray-400 mt-1">Diupload: {{ $signature->uploaded_at->format('d M Y, H:i') }} WIB</p>
                            @endif
                            @if($signature->verified_at)
                            <p class="text-xs text-gray-400">Diverifikasi: {{ $signature->verified_at->format('d M Y, H:i') }} WIB</p>
                            @endif
                        </div>

                        @php
                            $statusValue = is_object($signature->status) ? $signature->status->value : $signature->status;
                            $statusLabel = ucfirst($statusValue);

                            $badgeClass = match($statusValue) {
                                'verified' => 'bg-green-100 text-green-800',
                                'uploaded' => 'bg-blue-100 text-blue-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'requested' => 'bg-orange-100 text-orange-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp

                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Riwayat Aktivitas</h3>

            @if($documentRequest->activities->count() > 0)
                <x-tracking-timeline :activities="$documentRequest->activities" />
            @else
                <p class="text-center text-gray-500 py-8">Belum ada riwayat aktivitas</p>
            @endif
        </div>
    </div>
</div>
@endsection
