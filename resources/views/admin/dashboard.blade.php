@extends('layouts.admin', [
    'title' => 'Dashboard Admin',
    'subtitle' => 'Monitoring sistem pengajuan dokumen'
])

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border-l-4 border-blue-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total'] }}</h3>
            <p class="text-sm text-gray-600 font-medium">Total Dokumen</p>
        </div>

        <div class="bg-white rounded-xl p-6 border-l-4 border-yellow-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['pending'] }}</h3>
            <p class="text-sm text-gray-600 font-medium">Menunggu Review</p>
        </div>

        <div class="bg-white rounded-xl p-6 border-l-4 border-cyan-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-cyan-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['waiting_verification'] ?? 0 }}</h3>
            <p class="text-sm text-gray-600 font-medium">Menunggu Verifikasi</p>
        </div>

        <div class="bg-white rounded-xl p-6 border-l-4 border-purple-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['waiting_signature'] ?? 0 }}</h3>
            <p class="text-sm text-gray-600 font-medium">Menunggu TTD</p>
        </div>

        <div class="bg-white rounded-xl p-6 border-l-4 border-green-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['approved'] }}</h3>
            <p class="text-sm text-gray-600 font-medium">Disetujui</p>
        </div>

        <div class="bg-white rounded-xl p-6 border-l-4 border-orange-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['signature_uploaded'] ?? 0 }}</h3>
            <p class="text-sm text-gray-600 font-medium">TTD Masuk (Perlu Verify)</p>
        </div>

        <div class="bg-white rounded-xl p-6 border-l-4 border-indigo-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['ready'] }}</h3>
            <p class="text-sm text-gray-600 font-medium">Siap Diambil</p>
        </div>

        {{-- Selesai --}}
        <div class="bg-white rounded-xl p-6 border-l-4 border-gray-500 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['completed'] }}</h3>
            <p class="text-sm text-gray-600 font-medium">Selesai</p>
        </div>
    </div>

    {{-- Quick Actions - UPDATED --}}
    <div class="grid md:grid-cols-4 gap-4">
        <a href="{{ route('admin.documents.pending') }}" class="group bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Review Dokumen</h4>
                    <p class="text-sm text-gray-500">{{ $stats['pending'] }} menunggu</p>
                </div>
            </div>
        </a>

        {{-- 🆕 NEW: Verifikasi Pending --}}
        <a href="{{ route('admin.verifications.pending') }}" class="group bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-cyan-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Verifikasi</h4>
                    <p class="text-sm text-gray-500">{{ $stats['waiting_verification'] ?? 0 }} menunggu</p>
                </div>
            </div>
        </a>

        {{-- 🆕 NEW: TTD Pending --}}
        <a href="{{ route('admin.signatures.pending') }}" class="group bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Tanda Tangan</h4>
                    <p class="text-sm text-gray-500">{{ $stats['signature_uploaded'] ?? 0 }} perlu verify</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.signatures.history') }}" class="group bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Lihat Riwayat</h4>
                    <p class="text-sm text-gray-500">Semua aktivitas</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Recent Documents Table (sama seperti sebelumnya) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Pengajuan Terbaru
            </h3>
            <a href="{{ route('admin.documents.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold hover:underline transition">
                Lihat Semua →
            </a>
        </div>

        @if($recentDocuments && $recentDocuments->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($recentDocuments as $doc)
                    @php
                        // 🔥 FIX: Check if document has valid status
                        $hasValidStatus = false;
                        $docStatus = null;

                        try {
                            if ($doc && isset($doc->status)) {
                                // Try to access status - will throw ValueError if invalid
                                $docStatus = $doc->status;
                                $hasValidStatus = true;
                            }
                        } catch (\ValueError $e) {
                            // Invalid status - log and skip showing badge
                            \Log::warning('Invalid document status in dashboard', [
                                'document_id' => $doc->id,
                                'request_code' => $doc->request_code,
                                'status_raw' => $doc->getRawOriginal('status') ?? 'NULL'
                            ]);
                            $hasValidStatus = false;
                        }
                    @endphp

                    @if($hasValidStatus)
                        <a href="{{ route('admin.documents.show', $doc->id) }}" class="block p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        {{ strtoupper(substr($doc->documentType->name ?? 'DOC', 0, 2)) }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 mb-0.5">{{ $doc->request_code }}</h4>
                                        <p class="text-sm text-gray-600">{{ $doc->applicant_name }} - {{ $doc->documentType->name ?? 'Dokumen' }}</p>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $doc->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($docStatus)
                                        <x-status-badge :status="$docStatus" size="sm" />
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full border-2 bg-gray-100 text-gray-600 border-gray-300">
                                            <span class="text-base leading-none">⚠️</span>
                                            <span>Status Invalid</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-semibold text-gray-700">Belum ada pengajuan</p>
                <p class="text-sm text-gray-500 mt-1">Pengajuan baru akan muncul di sini</p>
            </div>
        @endif
    </div>

    {{-- Statistics Charts (tetap sama) --}}
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                Jenis Dokumen Populer
            </h3>
            @if(isset($documentTypeStats) && count($documentTypeStats) > 0)
                <div class="space-y-4">
                    @foreach($documentTypeStats as $stat)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">{{ $stat->name }}</span>
                                <span class="text-sm font-bold text-indigo-600">{{ $stat->count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $stats['total'] > 0 ? ($stat->count / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">Belum ada data</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                Statistik Pemohon
            </h3>
            @if(isset($applicantTypeStats) && count($applicantTypeStats) > 0)
                <div class="space-y-4">
                    @foreach($applicantTypeStats as $stat)
                        @php
                            $colors = [
                                'mahasiswa' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600'],
                                'dosen' => ['bg' => 'bg-green-500', 'text' => 'text-green-600'],
                                'staff' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600'],
                            ];
                            $applicantType = is_object($stat->applicant_type) ? $stat->applicant_type->value : $stat->applicant_type;
                            $color = $colors[$applicantType] ?? ['bg' => 'bg-gray-500', 'text' => 'text-gray-600'];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700 capitalize">{{ ucfirst($applicantType) }}</span>
                                <span class="text-sm font-bold {{ $color['text'] }}">{{ $stat->count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="{{ $color['bg'] }} h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $stats['total'] > 0 ? ($stat->count / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">Belum ada data</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-lg mb-1">Informasi Sistem</h3>
                <p class="text-indigo-100 text-sm leading-relaxed">
                    Dashboard ini menampilkan ringkasan data pengajuan dokumen termasuk status verifikasi dan tanda tangan. Gunakan menu di sidebar untuk mengakses fitur lengkap sistem SIPETER.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
