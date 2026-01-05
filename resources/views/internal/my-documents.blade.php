@php
use App\Enums\DocumentStatus;
use App\Enums\DeliveryMethod;
@endphp

@extends('layouts.internal', [
    'title' => 'Pengajuan Saya',
    'subtitle' => 'Kelola dan pantau status pengajuan dokumen Anda'
])

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-2 overflow-x-auto">
        <div class="flex flex-nowrap gap-2">
            <a href="{{ route('internal.my-documents.index') }}"
               class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all whitespace-nowrap flex-shrink-0 {{ !request('status') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                Semua
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ !request('status') ? 'bg-white text-blue-600' : 'bg-gray-200' }}">
                    {{ $stats['total'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('internal.my-documents.index', ['status' => DocumentStatus::PENDING->value]) }}"
               class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all whitespace-nowrap flex-shrink-0 {{ request('status') === DocumentStatus::PENDING->value ? 'bg-yellow-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                Pending
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === DocumentStatus::PENDING->value ? 'bg-white text-yellow-600' : 'bg-gray-200' }}">
                    {{ $stats['pending'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('internal.my-documents.index', ['status' => DocumentStatus::APPROVED->value]) }}"
               class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all whitespace-nowrap flex-shrink-0 {{ request('status') === DocumentStatus::APPROVED->value ? 'bg-green-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                Disetujui
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === DocumentStatus::APPROVED->value ? 'bg-white text-green-600' : 'bg-gray-200' }}">
                    {{ $stats['approved'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('internal.my-documents.index', ['status' => DocumentStatus::READY_FOR_PICKUP->value]) }}"
               class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all whitespace-nowrap flex-shrink-0 {{ request('status') === DocumentStatus::READY_FOR_PICKUP->value ? 'bg-purple-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                Siap Diambil
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === DocumentStatus::READY_FOR_PICKUP->value ? 'bg-white text-purple-600' : 'bg-gray-200' }}">
                    {{ $stats['ready'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('internal.my-documents.index', ['status' => DocumentStatus::COMPLETED->value]) }}"
               class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all whitespace-nowrap flex-shrink-0 {{ request('status') === DocumentStatus::COMPLETED->value ? 'bg-gray-700 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                Selesai
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === DocumentStatus::COMPLETED->value ? 'bg-white text-gray-700' : 'bg-gray-200' }}">
                    {{ $stats['completed'] ?? 0 }}
                </span>
            </a>

            {{-- Tab: Rejected --}}
            <a href="{{ route('internal.my-documents.index', ['status' => DocumentStatus::REJECTED->value]) }}"
               class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all whitespace-nowrap flex-shrink-0 {{ request('status') === DocumentStatus::REJECTED->value ? 'bg-red-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                Ditolak
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === DocumentStatus::REJECTED->value ? 'bg-white text-red-600' : 'bg-gray-200' }}">
                    {{ $stats['rejected'] ?? 0 }}
                </span>
            </a>
        </div>
    </div>

    {{-- Search and Action --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('internal.my-documents.index') }}" method="GET" class="w-full md:w-1/2 lg:w-1/3">
            <div class="relative">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input
                    type="text"
                    name="search"
                    placeholder="Cari berdasarkan Kode atau Jenis Dokumen..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </form>

        <div class="w-full md:w-auto flex-shrink-0">
            @if(request('search'))
                <a
                    href="{{ route('internal.my-documents.index', request()->only('status')) }}"
                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Reset Pencarian
                </a>
            @else
                <a
                    href="{{ route('internal.documents.create') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Buat Pengajuan Baru
                </a>
            @endif
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kode
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jenis Dokumen
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tgl. Pengajuan
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Metode
                        </th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($documents as $document)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                {{ $document->request_code }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $document->documentType->name }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ $document->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-4 text-sm whitespace-nowrap">
                                <x-status-badge :status="$document->status" />
                            </td>
                            <td class="px-5 py-4 text-sm whitespace-nowrap">
                                {{-- ✅ FIXED: Check string directly --}}
                                @if($document->delivery_method === 'download')
                                    <span class="flex items-center gap-1.5 text-green-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-blue-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 4h5m-5 4h5m-5 4h5"></path>
                                        </svg>
                                        Pickup
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- ✅ Tombol Download jika memenuhi syarat --}}
                                    @if($document->isDownloadable())
                                        <a href="{{ route('internal.my-documents.download', $document->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </a>
                                    @endif

                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('internal.my-documents.show', $document->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    @if(request('search'))
                                        <p class="text-lg font-semibold mb-1">Tidak ditemukan</p>
                                        <p class="text-sm">Permohonan dengan kata kunci "{{ request('search') }}" tidak ditemukan.</p>
                                    @else
                                        <p class="text-lg font-semibold mb-1">Belum ada dokumen</p>
                                        <p class="text-sm mb-4">Anda belum memiliki pengajuan dokumen.</p>
                                        <a href="{{ route('internal.documents.create') }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Buat Pengajuan
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
