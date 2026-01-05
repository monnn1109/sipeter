@extends('layouts.admin')

@section('title', 'Verifikasi Ditolak - SIPETER Admin')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">❌ Verifikasi Ditolak</h1>
            <p class="text-gray-600 mt-2">Daftar dokumen yang ditolak saat verifikasi</p>
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

    {{-- Filter removed karena $authorities tidak ada --}}
    {{-- Jika butuh filter, tambahkan $authorities di controller --}}

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($verifications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dokumen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pemohon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ditolak Oleh
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alasan Penolakan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($verifications as $verification)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-mono font-medium text-gray-900">
                                            {{ $verification->documentRequest->document_code }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $verification->documentRequest->documentType->name }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $verification->documentRequest->applicant_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            @if($verification->documentRequest->applicant_type === 'mahasiswa')
                                                👨‍🎓 {{ $verification->documentRequest->nim }}
                                            @else
                                                👨‍🏫 Internal
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $verification->signatureAuthority->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $verification->signatureAuthority->position }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-900 line-clamp-2">
                                            {{ $verification->rejection_reason ?? '-' }}
                                        </p>
                                        @if($verification->rejection_reason && strlen($verification->rejection_reason) > 50)
                                            <button
                                                onclick="showFullReason({{ $verification->id }})"
                                                class="text-xs text-blue-600 hover:text-blue-800 mt-1"
                                            >
                                                Lihat selengkapnya
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <p class="text-gray-900">{{ $verification->updated_at->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">{{ $verification->updated_at->format('H:i') }} WIB</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">

                                            href="{{ route('admin.documents.detail', $verification->document_request_id) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            👁️ Detail
                                        </a>
                                        <button
                                            onclick="resendVerification({{ $verification->document_request_id }})"
                                            class="text-green-600 hover:text-green-800 font-medium"
                                            title="Kirim Ulang ke Pejabat Lain"
                                        >
                                            🔄 Kirim Ulang
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
        const verification = verificationsData.find(v => v.id === verificationId);
        if (verification) {
            document.getElementById('full-reason').textContent = verification.rejection_reason;
            document.getElementById('reason-modal').classList.remove('hidden');
        }
    }

    function closeReasonModal() {
        document.getElementById('reason-modal').classList.add('hidden');
    }

    function resendVerification(documentId) {
        if (confirm('Kirim ulang permintaan verifikasi ke pejabat lain?\n\nDokumen yang ditolak dapat diverifikasi ulang oleh pejabat berbeda.')) {
            window.location.href = `/admin/documents/${documentId}/send-verification`;
        }
    }

    document.getElementById('reason-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReasonModal();
        }
    });
</script>
@endsection
