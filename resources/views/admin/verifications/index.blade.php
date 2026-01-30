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
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Dokumen</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

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

    {{-- Level Tabs & Filter --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.verifications.index', ['level' => 'all']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level', 'all') === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua Level
                </a>
                <a href="{{ route('admin.verifications.index', ['level' => '1']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '1' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📘 Level 1
                </a>
                <a href="{{ route('admin.verifications.index', ['level' => '2']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '2' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📕 Level 2
                </a>
                <a href="{{ route('admin.verifications.index', ['level' => '3']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '3' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📗 Level 3
                </a>
            </nav>
        </div>

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemohon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Verifikasi 3-Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($verifications->groupBy('document_request_id') as $documentId => $docVerifications)
                    @php
                        $firstVerification = $docVerifications->first();
                        $document = $firstVerification->documentRequest;

                        // Get all 3 levels
                        $level1 = $docVerifications->firstWhere('verification_level', 1);
                        $level2 = $docVerifications->firstWhere('verification_level', 2);
                        $level3 = $docVerifications->firstWhere('verification_level', 3);

                        // Determine latest date
                        $latestDate = $docVerifications->max('verified_at') ?? $docVerifications->max('sent_at') ?? $docVerifications->max('created_at');
                    @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-medium text-gray-900">
                            {{ $document->request_code }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">{{ $document->applicant_name }}</p>
                            <p class="text-gray-500">{{ $document->applicant_identifier }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $document->documentType->name }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="space-y-1.5">
                            {{-- Level 1 --}}
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">
                                    L1
                                </span>
                                @if($level1)
                                    @if($level1->status === 'approved')
                                        <span class="text-xs text-green-600 font-medium">✓</span>
                                    @elseif($level1->status === 'rejected')
                                        <span class="text-xs text-red-600 font-medium">✗</span>
                                    @else
                                        <span class="text-xs text-yellow-600 font-medium">⏳</span>
                                    @endif
                                    <span class="text-xs text-gray-600">{{ $level1->authority->name ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>

                            {{-- Level 2 --}}
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800">
                                    L2
                                </span>
                                @if($level2)
                                    @if($level2->status === 'approved')
                                        <span class="text-xs text-green-600 font-medium">✓</span>
                                    @elseif($level2->status === 'rejected')
                                        <span class="text-xs text-red-600 font-medium">✗</span>
                                    @else
                                        <span class="text-xs text-yellow-600 font-medium">⏳</span>
                                    @endif
                                    <span class="text-xs text-gray-600">{{ $level2->authority->name ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>

                            {{-- Level 3 --}}
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                    L3
                                </span>
                                @if($level3)
                                    @if($level3->status === 'approved')
                                        <span class="text-xs text-green-600 font-medium">✓</span>
                                    @elseif($level3->status === 'rejected')
                                        <span class="text-xs text-red-600 font-medium">✗</span>
                                    @else
                                        <span class="text-xs text-yellow-600 font-medium">⏳</span>
                                    @endif
                                    <span class="text-xs text-gray-600">{{ $level3->authority->name ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($latestDate)
                            {{ $latestDate->format('d M Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.documents.show', $document->id) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
                <p class="font-medium mb-1">ℹ️ Sistem Verifikasi 3-Level Sequential:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li><strong>Level 1 (L1):</strong> Ketua Akademik verifikasi pertama</li>
                    <li><strong>Level 2 (L2):</strong> Wakil Ketua 3 - Kemahasiswaan verifikasi kedua</li>
                    <li><strong>Level 3 (L3):</strong> Direktur - Final Approval</li>
                    <li>Setiap level memiliki link verifikasi terpisah (berlaku 3 hari)</li>
                    <li>Sistem otomatis lanjut ke level berikutnya jika approved (✓)</li>
                    <li>Jika ditolak (✗) di level manapun, proses BERHENTI</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
