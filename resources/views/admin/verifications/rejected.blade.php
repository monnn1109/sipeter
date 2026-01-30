@extends('layouts.admin')

@section('title', 'Verifikasi Ditolak - SIPETER Admin')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">❌ Verifikasi Ditolak</h1>
            <p class="text-gray-600 mt-2">Daftar dokumen yang ditolak saat verifikasi (3-Level Sequential)</p>
        </div>
        <a href="{{ route('admin.verifications.index') }}"
           class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Ditolak</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['total_rejected'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <span class="text-3xl">❌</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hari Ini</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['today'] }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <span class="text-3xl">📅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Minggu Ini</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['this_week'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <span class="text-3xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($verifications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                Dokumen
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-44">
                                Pemohon
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                Ditolak Di Level
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Alasan Penolakan
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-36">
                                Tanggal
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Group by document to avoid duplicates
                            $groupedVerifications = $verifications->groupBy('document_request_id');
                        @endphp

                        @foreach($groupedVerifications as $documentId => $docVerifications)
                            @php
                                // Get the rejected verification (should be only one per document)
                                $rejectedVerification = $docVerifications->firstWhere('status', 'rejected');
                                if (!$rejectedVerification) continue;

                                $document = $rejectedVerification->documentRequest;
                                $level = $rejectedVerification->verification_level ?? 1;

                                $levelConfig = [
                                    1 => ['color' => 'bg-blue-100 text-blue-800', 'label' => 'Level 1', 'name' => 'Ketua Akademik'],
                                    2 => ['color' => 'bg-purple-100 text-purple-800', 'label' => 'Level 2', 'name' => 'Wakil Ketua 3'],
                                    3 => ['color' => 'bg-green-100 text-green-800', 'label' => 'Level 3', 'name' => 'Direktur'],
                                ];
                                $config = $levelConfig[$level] ?? $levelConfig[1];
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors">
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
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-gray-900 leading-tight">
                                            {{ Str::limit($document->applicant_name, 25) }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            {{ $document->applicant_identifier }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-1.5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $config['color'] }} whitespace-nowrap">
                                            ❌ {{ $config['label'] }}
                                        </span>
                                        @if($rejectedVerification->authority)
                                            <p class="text-xs text-gray-800 font-semibold leading-tight">
                                                {{ Str::limit($rejectedVerification->authority->name, 28) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $config['name'] }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="max-w-md">
                                        @if($rejectedVerification->notes)
                                            <div class="bg-red-50 border border-red-200 rounded px-3 py-2">
                                                <p class="text-xs text-gray-900 leading-relaxed line-clamp-3">
                                                    {{ $rejectedVerification->notes }}
                                                </p>
                                            </div>
                                            @if(strlen($rejectedVerification->notes) > 120)
                                                <button
                                                    onclick="showFullReason({{ $rejectedVerification->id }})"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium mt-1.5 inline-flex items-center gap-1"
                                                >
                                                    Lihat lengkap
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada alasan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @if($rejectedVerification->verified_at)
                                        <div class="space-y-0.5">
                                            <p class="text-sm text-gray-900 font-semibold">{{ $rejectedVerification->verified_at->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-600">{{ $rejectedVerification->verified_at->format('H:i') }} WIB</p>
                                            <p class="text-xs text-blue-600">{{ $rejectedVerification->verified_at->diffForHumans() }}</p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top text-center">
                                    <div class="flex flex-col gap-2 items-center">
                                        <a
                                            href="{{ route('admin.documents.show', $document->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-xs font-semibold transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </a>
                                        <button
                                            onclick="resendVerification({{ $document->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-semibold transition-colors"
                                            title="Kirim Ulang Verifikasi"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Kirim Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4">
                {{ $verifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada verifikasi ditolak</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Verifikasi yang ditolak akan muncul di sini.
                </p>
            </div>
        @endif
    </div>
</div>

{{-- Modal for full rejection reason --}}
<div id="reason-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📋 Alasan Penolakan Lengkap</h3>
                <button onclick="closeReasonModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p id="full-reason" class="text-sm text-gray-900 whitespace-pre-wrap"></p>
            </div>
            <div class="mt-4 flex justify-end">
                <button
                    onclick="closeReasonModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const verificationsData = @json($verifications->items());

    function showFullReason(verificationId) {
        const allVerifications = verificationsData.flatMap(item =>
            item.document_request_id ? [item] : []
        );
        const verification = allVerifications.find(v => v.id === verificationId);

        if (verification && verification.notes) {
            document.getElementById('full-reason').textContent = verification.notes;
            document.getElementById('reason-modal').classList.remove('hidden');
        }
    }

    function closeReasonModal() {
        document.getElementById('reason-modal').classList.add('hidden');
    }

    function resendVerification(documentId) {
        if (confirm('Kirim ulang permintaan verifikasi?\n\nDokumen yang ditolak dapat diverifikasi ulang.')) {
            window.location.href = `/admin/documents/${documentId}/send-verification`;
        }
    }

    document.getElementById('reason-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReasonModal();
        }
    });
</script>
@endsection
