@extends('layouts.admin')

@section('title', 'Riwayat Tanda Tangan - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📚 Riwayat Tanda Tangan</h1>
        <p class="text-gray-600 mt-2">Semua riwayat tanda tangan yang telah diproses (3-Level Sequential)</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Dokumen</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalCount }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <span class="text-3xl">📊</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Diverifikasi</p>
                    <p class="text-3xl font-bold text-green-600">{{ $verifiedCount }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <span class="text-3xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ditolak</p>
                    <p class="text-3xl font-bold text-red-600">{{ $rejectedCount }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <span class="text-3xl">❌</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Bulan Ini</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $monthCount }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <span class="text-3xl">📈</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form action="{{ route('admin.signatures.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Kode/Nama..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>✅ Diverifikasi</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>

            <div>
                <label for="authority" class="block text-sm font-medium text-gray-700 mb-2">Pejabat</label>
                <select name="authority" id="authority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Pejabat</option>
                    @foreach($authorities as $authority)
                        <option value="{{ $authority->id }}" {{ request('authority') == $authority->id ? 'selected' : '' }}>
                            {{ $authority->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                <input
                    type="date"
                    name="date_from"
                    id="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Sampai</label>
                <input
                    type="date"
                    name="date_to"
                    id="date_to"
                    value="{{ request('date_to') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.signatures.history') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($signatures->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                Dokumen
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-40">
                                Pemohon
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status TTD 3-Level
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-36">
                                Tanggal Selesai
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-40">
                                Waktu Proses
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Group by document to show 1 row per document
                            $groupedSignatures = $signatures->groupBy('document_request_id');
                        @endphp

                        @foreach($groupedSignatures as $documentId => $docSignatures)
                            @php
                                $firstSig = $docSignatures->first();
                                $document = $firstSig->documentRequest;

                                // Get all 3 levels
                                $sigLevel1 = $docSignatures->firstWhere('signature_level', 1);
                                $sigLevel2 = $docSignatures->firstWhere('signature_level', 2);
                                $sigLevel3 = $docSignatures->firstWhere('signature_level', 3);

                                // Calculate process time
                                $allSigs = $docSignatures->sortBy('requested_at');
                                $firstRequest = $allSigs->first()->requested_at;
                                $lastVerified = $allSigs->sortByDesc('verified_at')->first()->verified_at;

                                if ($firstRequest && $lastVerified) {
                                    $totalSeconds = $firstRequest->diffInSeconds($lastVerified);
                                    $totalMinutes = $firstRequest->diffInMinutes($lastVerified);
                                    $totalHours = $firstRequest->diffInHours($lastVerified);
                                    $totalDays = $firstRequest->diffInDays($lastVerified);

                                    if ($totalHours < 2) {
                                        $speedColor = 'bg-green-100 text-green-800';
                                        $speedIcon = '⚡';
                                    } elseif ($totalHours < 24) {
                                        $speedColor = 'bg-blue-100 text-blue-800';
                                        $speedIcon = '✓';
                                    } elseif ($totalDays <= 3) {
                                        $speedColor = 'bg-yellow-100 text-yellow-800';
                                        $speedIcon = '○';
                                    } else {
                                        $speedColor = 'bg-orange-100 text-orange-800';
                                        $speedIcon = '⊗';
                                    }
                                }
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Document Info --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-1">
                                        <p class="text-sm font-mono font-bold text-gray-900">
                                            {{ $document->document_code }}
                                        </p>
                                        <p class="text-xs text-gray-600 leading-tight">
                                            {{ Str::limit($document->documentType->name, 35) }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Applicant Info --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-gray-900 leading-tight">
                                            {{ Str::limit($document->applicant_name, 22) }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            {{ $document->applicant_identifier }}
                                        </p>
                                    </div>
                                </td>

                                {{-- 3-Level Signature Status --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-1.5">
                                        {{-- Level 1 --}}
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">
                                                L1
                                            </span>
                                            @if($sigLevel1)
                                                @if($sigLevel1->status->value === 'verified')
                                                    <span class="text-xs text-green-600 font-bold">✓</span>
                                                @elseif($sigLevel1->status->value === 'rejected')
                                                    <span class="text-xs text-red-600 font-bold">✗</span>
                                                @else
                                                    <span class="text-xs text-yellow-600 font-bold">⏳</span>
                                                @endif
                                                <span class="text-xs text-gray-700">{{ Str::limit($sigLevel1->signatureAuthority->name ?? '-', 18) }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>

                                        {{-- Level 2 --}}
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800">
                                                L2
                                            </span>
                                            @if($sigLevel2)
                                                @if($sigLevel2->status->value === 'verified')
                                                    <span class="text-xs text-green-600 font-bold">✓</span>
                                                @elseif($sigLevel2->status->value === 'rejected')
                                                    <span class="text-xs text-red-600 font-bold">✗</span>
                                                @else
                                                    <span class="text-xs text-yellow-600 font-bold">⏳</span>
                                                @endif
                                                <span class="text-xs text-gray-700">{{ Str::limit($sigLevel2->signatureAuthority->name ?? '-', 18) }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>

                                        {{-- Level 3 --}}
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                                L3
                                            </span>
                                            @if($sigLevel3)
                                                @if($sigLevel3->status->value === 'verified')
                                                    <span class="text-xs text-green-600 font-bold">✓</span>
                                                @elseif($sigLevel3->status->value === 'rejected')
                                                    <span class="text-xs text-red-600 font-bold">✗</span>
                                                @else
                                                    <span class="text-xs text-yellow-600 font-bold">⏳</span>
                                                @endif
                                                <span class="text-xs text-gray-700">{{ Str::limit($sigLevel3->signatureAuthority->name ?? '-', 18) }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Completion Date --}}
                                <td class="px-4 py-4 align-top">
                                    @if($lastVerified)
                                        <div class="space-y-0.5">
                                            <p class="text-sm text-gray-900 font-semibold">{{ $lastVerified->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-600">{{ $lastVerified->format('H:i') }} WIB</p>
                                            <p class="text-xs text-blue-600">{{ $lastVerified->diffForHumans() }}</p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Process Time --}}
                                <td class="px-4 py-4 align-top">
                                    @if(isset($speedColor))
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold {{ $speedColor }} rounded-full whitespace-nowrap">
                                                {{ $speedIcon }}
                                                @if($totalSeconds < 60)
                                                    {{ round($totalSeconds) }} detik
                                                @elseif($totalMinutes < 60)
                                                    {{ round($totalMinutes) }} menit
                                                @elseif($totalHours < 24)
                                                    {{ floor($totalHours) }} jam
                                                @elseif($totalDays < 7)
                                                    {{ $totalDays }} hari
                                                @else
                                                    {{ $totalDays }} hari
                                                @endif
                                            </span>
                                            <p class="text-xs text-gray-500">
                                                Mulai: {{ $firstRequest->format('d/m H:i') }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-4 align-top text-center">
                                    <div class="flex flex-col gap-2 items-center">
                                        <button
                                            onclick="viewAllSignatures({{ $documentId }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-xs font-semibold transition-colors w-full justify-center"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat TTD
                                        </button>
                                        <a
                                            href="{{ route('admin.documents.show', $document->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-semibold transition-colors w-full justify-center"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Dokumen
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4">
                {{ $signatures->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada riwayat</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'status', 'authority', 'date_from', 'date_to']))
                        Tidak ada data yang sesuai dengan filter. Coba ubah kriteria pencarian.
                    @else
                        Riwayat tanda tangan akan muncul di sini setelah diverifikasi.
                    @endif
                </p>
            </div>
        @endif
    </div>

    @if($signatures->count() > 0)
    <div class="mt-6 flex justify-end gap-3">
        <a
            href="{{ route('admin.signatures.history.export', request()->query()) }}"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
        <a
            href="{{ route('admin.signatures.history.pdf', request()->query()) }}"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Export PDF
        </a>
    </div>
    @endif
</div>

{{-- Modal for viewing all 3 signatures --}}
<div id="signature-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">✍️ Detail Tanda Tangan 3-Level</h3>
                <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="signature-content" class="space-y-4"></div>
        </div>
    </div>
</div>

<script>
    const allSignatures = @json($signatures->items());

    function viewAllSignatures(documentId) {
        const modal = document.getElementById('signature-modal');
        const content = document.getElementById('signature-content');

        content.innerHTML = '<div class="text-center py-8"><span class="text-2xl">⏳</span><p class="mt-2">Loading...</p></div>';
        modal.classList.remove('hidden');

        // Get all signatures for this document
        const docSigs = allSignatures.filter(sig => sig.document_request_id === documentId);

        if (docSigs.length === 0) {
            content.innerHTML = '<div class="text-center py-8 text-red-600">Tidak ada data</div>';
            return;
        }

        const levels = [
            { level: 1, label: 'Level 1: Ketua Akademik', color: 'blue' },
            { level: 2, label: 'Level 2: Wakil Ketua 3', color: 'purple' },
            { level: 3, label: 'Level 3: Direktur', color: 'green' }
        ];

        let html = '';
        levels.forEach(levelInfo => {
            const sig = docSigs.find(s => s.signature_level === levelInfo.level);

            html += `
                <div class="border border-${levelInfo.color}-200 rounded-lg p-4 bg-${levelInfo.color}-50">
                    <h4 class="font-semibold text-${levelInfo.color}-900 mb-3">${levelInfo.label}</h4>
                    ${sig ? `
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 bg-white p-3 rounded border">
                                <img src="${sig.signature_url || '/placeholder-signature.png'}"
                                     alt="TTD ${levelInfo.label}"
                                     class="max-w-full h-auto max-h-32 mx-auto"
                                     onerror="this.src='/placeholder-signature.png'">
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-600">Pejabat:</span>
                                <p class="text-gray-900">${sig.signature_authority?.name || '-'}</p>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-600">Status:</span>
                                <p class="text-gray-900">
                                    ${sig.status?.value === 'verified' ? '✓ Verified' :
                                      sig.status?.value === 'rejected' ? '✗ Rejected' :
                                      '⏳ Pending'}
                                </p>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-600">Upload:</span>
                                <p class="text-gray-900">${sig.uploaded_at ? new Date(sig.uploaded_at).toLocaleString('id-ID') : '-'}</p>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-600">Verifikasi:</span>
                                <p class="text-gray-900">${sig.verified_at ? new Date(sig.verified_at).toLocaleString('id-ID') : '-'}</p>
                            </div>
                        </div>
                    ` : `
                        <p class="text-gray-500 text-sm italic">Belum ada tanda tangan untuk level ini</p>
                    `}
                </div>
            `;
        });

        content.innerHTML = html;
    }

    function closeSignatureModal() {
        document.getElementById('signature-modal').classList.add('hidden');
    }

    document.getElementById('signature-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSignatureModal();
        }
    });
</script>
@endsection
