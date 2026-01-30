@extends('layouts.admin')

@section('title', 'Pending Signatures - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">⏳ Pending Signatures</h1>
                <p class="text-gray-600 mt-2">Daftar tanda tangan yang menunggu untuk diverifikasi</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.signatures.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Belum Upload</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $requestedCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu pejabat</p>
                </div>
                <div class="text-orange-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Menunggu Verifikasi</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $uploadedCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sudah diupload</p>
                </div>
                <div class="text-blue-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Terlambat</p>
                    <p class="text-3xl font-bold text-red-600">{{ $overdueCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">> 3 hari</p>
                </div>
                <div class="text-red-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Minggu Ini</p>
                    <p class="text-3xl font-bold text-green-600">{{ $weekCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">TTD masuk</p>
                </div>
                <div class="text-green-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.signatures.pending') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🔍 Cari Dokumen</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Kode dokumen atau nama..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">👤 Pejabat</label>
                <select
                    name="authority"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Pejabat</option>
                    @foreach($authorities as $auth)
                        <option value="{{ $auth->id }}" {{ request('authority') == $auth->id ? 'selected' : '' }}>
                            {{ $auth->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📅 Dari Tanggal</label>
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    🔍 Filter
                </button>
                @if(request()->hasAny(['search', 'authority', 'date_from', 'date_to']))
                    <a
                        href="{{ route('admin.signatures.pending') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($signatures->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pejabat TTD</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Upload</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($signatures as $index => $sig)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signatures->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 font-mono">
                                        {{ $sig->documentRequest->request_code }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ Str::limit($sig->documentRequest->documentType->name, 30) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $sig->documentRequest->applicant_name }}</div>
                                {{-- ✅ FIXED: applicant_type enum to string --}}
                                <div class="text-xs text-gray-500">
                                    {{ $sig->documentRequest->applicant_type->label() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $sig->signatureAuthority->name }}</div>
                                <div class="text-xs text-gray-500">{{ $sig->signatureAuthority->position }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-signature-status-badge :status="$sig->status" />
                            </td>
                            {{-- ✅ FIXED: uploaded_at dengan null check --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sig->uploaded_at)
                                    <div class="text-sm text-gray-900">{{ $sig->uploaded_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $sig->uploaded_at->format('H:i') }} WIB</div>
                                    <div class="text-xs text-blue-600">{{ $sig->uploaded_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a
                                        href="{{ route('admin.signatures.verify-form', $sig->id) }}"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition font-medium"
                                    >
                                        ✅ Verifikasi
                                    </a>

                                    <a
                                        href="{{ route('admin.documents.show', $sig->document_request_id) }}"
                                        class="px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition"
                                        title="Lihat Dokumen"
                                    >
                                        👁️
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $signatures->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">✅ Tidak Ada Signature Pending</h3>
                <p class="text-gray-600">
                    @if(request()->hasAny(['search', 'authority', 'date_from', 'date_to']))
                        Tidak ada hasil yang cocok dengan filter Anda.
                    @else
                        Semua signature sudah diverifikasi atau belum ada yang diupload.
                    @endif
                </p>
                @if($requestedCount > 0)
                    <div class="mt-4">
                        <p class="text-sm text-orange-600 font-medium">
                            ⚠️ Ada {{ $requestedCount }} signature yang masih menunggu upload dari pejabat
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="text-blue-600 mr-3">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-blue-900 mb-1">ℹ️ Informasi Status:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li><strong>Belum Upload ({{ $requestedCount }}):</strong> Menunggu pejabat upload tanda tangan</li>
                    <li><strong>Menunggu Verifikasi ({{ $uploadedCount }}):</strong> Tanda tangan sudah diupload, siap untuk diverifikasi admin</li>
                    <li><strong>Terlambat ({{ $overdueCount }}):</strong> Upload sudah lebih dari 3 hari, perlu segera diverifikasi</li>
                    <li><strong>Minggu Ini ({{ $weekCount }}):</strong> Total TTD yang masuk minggu ini</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
