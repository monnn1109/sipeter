@extends('layouts.admin')

@section('title', 'TTD Digital - Overview')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">TTD Digital (3-Level Sequential)</h1>
        <p class="text-gray-600 mt-2">Kelola semua tanda tangan digital dokumen</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Dokumen TTD</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Menunggu Upload</p>
                    <h3 class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['requested'] }}</h3>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.signatures.pending') }}" class="text-xs text-yellow-600 hover:underline mt-2 block">
                Lihat Detail →
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Menunggu Verifikasi</p>
                    <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['uploaded'] }}</h3>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.signatures.verify.index') }}" class="text-xs text-purple-600 hover:underline mt-2 block">
                Verifikasi Sekarang →
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Terverifikasi</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">{{ $stats['verified'] }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <a href="{{ route('admin.signatures.pending') }}" class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">TTD Pending</h3>
                    <p class="text-sm opacity-90 mt-1">{{ $stats['pending'] }} dokumen menunggu</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('admin.signatures.verify.index') }}" class="bg-gradient-to-r from-purple-400 to-purple-500 text-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Verifikasi TTD</h3>
                    <p class="text-sm opacity-90 mt-1">{{ $stats['uploaded'] }} perlu diverifikasi</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('admin.signatures.authorities') }}" class="bg-gradient-to-r from-blue-400 to-blue-500 text-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Kelola Pejabat</h3>
                    <p class="text-sm opacity-90 mt-1">{{ $authorities->count() }} pejabat aktif</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>

    {{-- Alert for Overdue Signatures --}}
    @if($stats['overdue'] > 0)
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h4 class="text-red-800 font-semibold">Perhatian!</h4>
                <p class="text-red-700 text-sm">Ada {{ $stats['overdue'] }} TTD yang belum diupload lebih dari 24 jam.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Filter Status:</label>
            <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="requested" {{ $status === 'requested' ? 'selected' : '' }}>Menunggu Upload</option>
                <option value="uploaded" {{ $status === 'uploaded' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </form>
    </div>

    {{-- Signatures Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status TTD 3-Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Request</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($signatures->groupBy('document_request_id') as $documentId => $docSignatures)
                    @php
                        $firstSig = $docSignatures->first();
                        $document = $firstSig->documentRequest;

                        // Get all 3 levels
                        $sigLevel1 = $docSignatures->firstWhere('signature_level', 1);
                        $sigLevel2 = $docSignatures->firstWhere('signature_level', 2);
                        $sigLevel3 = $docSignatures->firstWhere('signature_level', 3);
                    @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-medium text-gray-900">
                            {{ $document->request_code }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $document->documentType->name }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="space-y-1.5">
                            {{-- Level 1 TTD --}}
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">
                                    L1
                                </span>
                                @if($sigLevel1)
                                    @if($sigLevel1->status->value === 'verified')
                                        <span class="text-xs text-green-600 font-medium">✓</span>
                                    @elseif($sigLevel1->status->value === 'uploaded')
                                        <span class="text-xs text-purple-600 font-medium">⟳</span>
                                    @elseif($sigLevel1->status->value === 'requested')
                                        <span class="text-xs text-yellow-600 font-medium">⏳</span>
                                    @else
                                        <span class="text-xs text-red-600 font-medium">✗</span>
                                    @endif
                                    <span class="text-xs text-gray-600">{{ $sigLevel1->signatureAuthority->name ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>

                            {{-- Level 2 TTD --}}
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800">
                                    L2
                                </span>
                                @if($sigLevel2)
                                    @if($sigLevel2->status->value === 'verified')
                                        <span class="text-xs text-green-600 font-medium">✓</span>
                                    @elseif($sigLevel2->status->value === 'uploaded')
                                        <span class="text-xs text-purple-600 font-medium">⟳</span>
                                    @elseif($sigLevel2->status->value === 'requested')
                                        <span class="text-xs text-yellow-600 font-medium">⏳</span>
                                    @else
                                        <span class="text-xs text-red-600 font-medium">✗</span>
                                    @endif
                                    <span class="text-xs text-gray-600">{{ $sigLevel2->signatureAuthority->name ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>

                            {{-- Level 3 TTD --}}
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                    L3
                                </span>
                                @if($sigLevel3)
                                    @if($sigLevel3->status->value === 'verified')
                                        <span class="text-xs text-green-600 font-medium">✓</span>
                                    @elseif($sigLevel3->status->value === 'uploaded')
                                        <span class="text-xs text-purple-600 font-medium">⟳</span>
                                    @elseif($sigLevel3->status->value === 'requested')
                                        <span class="text-xs text-yellow-600 font-medium">⏳</span>
                                    @else
                                        <span class="text-xs text-red-600 font-medium">✗</span>
                                    @endif
                                    <span class="text-xs text-gray-600">{{ $sigLevel3->signatureAuthority->name ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($firstSig->requested_at)
                            {{ $firstSig->requested_at->format('d M Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.documents.show', $document->id) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                📄 Dokumen
                            </a>

                            {{-- Link to verify if any uploaded --}}
                            @if($docSignatures->where('status.value', 'uploaded')->count() > 0)
                                <a href="{{ route('admin.signatures.verify.index') }}"
                                   class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                                    ✅ Verifikasi
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-medium">Tidak ada data tanda tangan digital</p>
                        <p class="text-sm mt-1">Data akan muncul setelah ada request tanda tangan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($signatures->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $signatures->links() }}
        </div>
        @endif
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-2">ℹ️ Status Icon:</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span class="text-xs">Verified</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-purple-600 font-bold">⟳</span>
                        <span class="text-xs">Uploaded (Pending Verify)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-yellow-600 font-bold">⏳</span>
                        <span class="text-xs">Requested (Waiting Upload)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-red-600 font-bold">✗</span>
                        <span class="text-xs">Rejected</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
