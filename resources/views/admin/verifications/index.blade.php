@extends('layouts.admin')

@section('title', 'Verifikasi Dokumen - Overview')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Verifikasi Dokumen (3-Level)</h1>
        <p class="text-gray-600 mt-2">Kelola semua request verifikasi dokumen dengan sistem 3-level sequential</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Total --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Verifikasi</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Menunggu</p>
                    <h3 class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</h3>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.verifications.pending') }}" class="text-xs text-yellow-600 hover:underline mt-2 block">
                Lihat Detail →
            </a>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Disetujui</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved'] }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.verifications.approved') }}" class="text-xs text-green-600 hover:underline mt-2 block">
                Lihat Detail →
            </a>
        </div>

        {{-- Rejected --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ditolak</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</h3>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.verifications.rejected') }}" class="text-xs text-red-600 hover:underline mt-2 block">
                Lihat Detail →
            </a>
        </div>
    </div>

    {{-- ✅ NEW: Level Tabs --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.verifications.index', ['level' => 'all']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level', 'all') === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua Level
                </a>
                <a href="{{ route('admin.verifications.index', ['level' => '1']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '1' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📘 Level 1 (Ketua Akademik)
                </a>
                <a href="{{ route('admin.verifications.index', ['level' => '2']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '2' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📕 Level 2 (Wakil Ketua 3)
                </a>
                <a href="{{ route('admin.verifications.index', ['level' => '3']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '3' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📗 Level 3 (Direktur)
                </a>
            </nav>
        </div>

        {{-- Filter Status --}}
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="flex items-center gap-4">
                @if(request('level'))
                    <input type="hidden" name="level" value="{{ request('level') }}">
                @endif
                <label class="text-sm font-medium text-gray-700">Filter Status:</label>
                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="requested" {{ $status === 'requested' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemohon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verifikator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($verifications as $verification)
                <tr class="hover:bg-gray-50">
                    {{-- ✅ NEW: Level Badge --}}
                    <td class="px-6 py-4">
                        @php
                            $level = $verification->verification_level ?? 1;
                            $levelColors = [
                                1 => 'bg-blue-100 text-blue-800',
                                2 => 'bg-purple-100 text-purple-800',
                                3 => 'bg-green-100 text-green-800',
                            ];
                            $levelLabels = [
                                1 => 'L1',
                                2 => 'L2',
                                3 => 'L3',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $levelColors[$level] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $levelLabels[$level] ?? 'L?' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-medium text-gray-900">
                            {{ $verification->documentRequest->request_code }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">{{ $verification->documentRequest->applicant_name }}</p>
                            <p class="text-gray-500">{{ $verification->documentRequest->applicant_identifier }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $verification->documentRequest->documentType->name }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-900">{{ $verification->authority->name ?? '-' }}</p>
                            @if($verification->authority)
                                <p class="text-xs text-gray-500">{{ $verification->authority->authority_type->label() }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($verification->status === 'requested')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                ⏳ Menunggu
                            </span>
                        @elseif($verification->status === 'approved')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                ✅ Disetujui
                            </span>
                        @elseif($verification->status === 'rejected')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                ❌ Ditolak
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $verification->requested_at?->format('d M Y H:i') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.documents.show', $verification->document_request_id) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="font-medium">Tidak ada data verifikasi</p>
                        @if(request('level'))
                            <p class="text-sm mt-1">untuk Level {{ request('level') }}</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($verifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $verifications->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">ℹ️ Sistem Verifikasi 3-Level:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li><strong>Level 1:</strong> Ketua Akademik (33% progress)</li>
                    <li><strong>Level 2:</strong> Wakil Ketua 3 - Kemahasiswaan (66% progress)</li>
                    <li><strong>Level 3:</strong> Direktur - Final Approval (100% progress)</li>
                    <li>Setiap level memiliki link verifikasi terpisah (berlaku 3 hari)</li>
                    <li>Sistem otomatis lanjut ke level berikutnya jika approved</li>
                    <li>Jika ditolak di level manapun, proses BERHENTI</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
