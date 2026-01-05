@extends('layouts.internal')

@section('title', 'Dashboard - SIPETER')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- WELCOME BANNER --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-blue-100">{{ auth()->user()->role->label() }} - Kelola pengajuan dokumen Anda dengan mudah</p>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Pengajuan --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pengajuan</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Diproses --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Diproses</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['processing'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Selesai</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- 🆕 NEW: VERIFICATION & SIGNATURE STATS --}}
    @if(isset($stats['waiting_verification']) || isset($stats['waiting_signature']))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Verification Stats --}}
        @if(isset($stats['waiting_verification']))
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Status Verifikasi
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <span class="text-sm font-medium text-gray-700">Menunggu Verifikasi</span>
                    <span class="text-2xl font-bold text-yellow-600">{{ $stats['waiting_verification'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <span class="text-sm font-medium text-gray-700">Terverifikasi</span>
                    <span class="text-2xl font-bold text-green-600">{{ $stats['verified'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Signature Stats --}}
        @if(isset($stats['waiting_signature']))
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                </svg>
                Status Tanda Tangan
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <span class="text-sm font-medium text-gray-700">Menunggu TTD</span>
                    <span class="text-2xl font-bold text-orange-600">{{ $stats['waiting_signature'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <span class="text-sm font-medium text-gray-700">TTD Diupload</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $stats['signature_uploaded'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <span class="text-sm font-medium text-gray-700">TTD Terverifikasi</span>
                    <span class="text-2xl font-bold text-green-600">{{ $stats['signature_verified'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- 🆕 NEW: WAITING SIGNATURE ALERT --}}
    @if(isset($waitingSignatureDocuments) && $waitingSignatureDocuments->count() > 0)
    <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-orange-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-orange-900 mb-2">📝 Dokumen Menunggu Tanda Tangan</h4>
                <div class="space-y-2">
                    @foreach($waitingSignatureDocuments as $doc)
                    <div class="flex items-center justify-between bg-white rounded p-3 shadow-sm">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm">{{ $doc->request_code }}</p>
                            <p class="text-xs text-gray-600">{{ $doc->documentType->name }}</p>
                            <p class="text-xs text-orange-600 mt-1">
                                Menunggu TTD dari:
                                @if($doc->documentSignatures->count() > 0)
                                    {{ $doc->documentSignatures->first()->authority->user->name }}
                                    ({{ $doc->documentSignatures->first()->authority->authority_type->label() }})
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('internal.my-documents.show', $doc->id) }}"
                           class="ml-4 px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded transition">
                            Lihat Detail
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 🆕 NEW: INFO PROSES --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
        <h3 class="font-bold text-gray-900 mb-3">ℹ️ Info Proses Dokumen</h3>
        <p class="text-sm text-gray-700">
            Dokumen Anda akan melalui proses <strong>verifikasi kelayakan</strong> dan <strong>tanda tangan digital</strong>
            oleh pejabat berwenang sebelum dapat diambil. Anda akan mendapat notifikasi WhatsApp di setiap tahap.
        </p>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <svg class="w-5 h-5 text-yellow-500 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
            </svg>
            Aksi Cepat
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('internal.documents.create') }}" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition group">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-200">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Ajukan Dokumen Baru</p>
                    <p class="text-sm text-gray-600">Buat pengajuan dokumen</p>
                </div>
            </a>

            <a href="{{ route('internal.my-documents.index') }}" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition group">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-200">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Lihat Dokumen Saya</p>
                    <p class="text-sm text-gray-600">Semua pengajuan Anda</p>
                </div>
            </a>

            <a href="{{ route('internal.profile.index') }}" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition group">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-purple-200">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Kelola Profil</p>
                    <p class="text-sm text-gray-600">Update informasi Anda</p>
                </div>
            </a>
        </div>
    </div>

    {{-- RECENT DOCUMENTS --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">
                    <svg class="w-5 h-5 text-blue-600 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pengajuan Terbaru
                </h3>
                <a href="{{ route('internal.my-documents.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua →
                </a>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($recentDocuments ?? [] as $document)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold text-gray-800">{{ $document->documentType->name ?? 'Dokumen' }}</h4>
                            <span class="ml-3 text-xs text-gray-500">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                {{ $document->request_code ?? '-' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $document->purpose ?? '-' }}</p>

                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-2">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $document->created_at->format('d M Y') }}
                            </span>

                            @if($document->verification_status)
                            <span class="flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                                @if($document->verification_status->value === 'VERIFICATION_APPROVED')
                                ✓ Terverifikasi
                                @else
                                ⏳ Verifikasi
                                @endif
                            </span>
                            @endif

                            @if($document->signature_status)
                            <span class="flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full">
                                @if($document->signature_status->value === 'ALL_SIGNATURES_COMPLETE')
                                ✓ TTD Lengkap
                                @else
                                ⏳ TTD
                                @endif
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-6 flex items-center space-x-3">
                        @php
                            $statusColors = [
                                'submitted' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-purple-100 text-purple-800',
                                'ready_for_pickup' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                            $statusValue = is_string($document->status) ? $document->status : $document->status->value;
                            $color = $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="{{ $color }} px-3 py-1 rounded-full text-xs font-medium">
                            {{ is_string($document->status) ? ucfirst($document->status) : $document->status->label() }}
                        </span>
                        <a href="{{ route('internal.my-documents.show', $document->id) }}" class="text-blue-600 hover:text-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-lg font-medium mb-2">Belum Ada Pengajuan</p>
                <p class="text-sm mb-4">Mulai ajukan dokumen yang Anda butuhkan</p>
                <a href="{{ route('internal.documents.create') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Ajukan Dokumen
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
