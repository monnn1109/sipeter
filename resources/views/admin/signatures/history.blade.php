@extends('layouts.admin')

@section('title', 'Riwayat Tanda Tangan - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📚 Riwayat Tanda Tangan</h1>
        <p class="text-gray-600 mt-2">Semua riwayat tanda tangan yang telah diproses</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Riwayat</p>
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
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dokumen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pemohon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pejabat TTD
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Diverifikasi Oleh
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($signatures as $signature)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-mono font-medium text-gray-900">
                                            {{ $signature->documentRequest->document_code }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $signature->documentRequest->documentType->name }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $signature->documentRequest->applicant_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            @if($signature->documentRequest->applicant_type === 'mahasiswa')
                                                👨‍🎓 {{ $signature->documentRequest->nim }}
                                            @else
                                                👨‍🏫 Internal
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $signature->authority->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $signature->authority->position }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-signature-status-badge :status="$signature->status" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <p class="text-gray-900">{{ $signature->verified_at->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">{{ $signature->verified_at->format('H:i') }} WIB</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($signature->verifiedBy)
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $signature->verifiedBy->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">Admin</p>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button
                                            onclick="viewSignature({{ $signature->id }})"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            👁️ Lihat
                                        </button>
                                        <a
                                            href="{{ route('admin.signatures.history', $signature->document_request_id) }}"
                                            class="text-gray-600 hover:text-gray-800 font-medium"
                                        >
                                            📄 Dokumen
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
            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
        >
            📥 Export Excel
        </a>
        <a
            href="{{ route('admin.signatures.history.pdf', request()->query()) }}"
            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
        >
            📄 Export PDF
        </a>
    </div>
    @endif
</div>

<div id="signature-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">✍️ Detail Tanda Tangan</h3>
                <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="signature-content"></div>
        </div>
    </div>
</div>

<script>
    function viewSignature(signatureId) {
        const modal = document.getElementById('signature-modal');
        const content = document.getElementById('signature-content');

        content.innerHTML = '<div class="text-center py-8"><span class="text-2xl">⏳</span><p class="mt-2">Loading...</p></div>';
        modal.classList.remove('hidden');

        fetch(`/admin/signatures/${signatureId}/view`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const sig = data.signature;
                    content.innerHTML = `
                        <div class="space-y-4">
                            <!-- Signature Image -->
                            <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50 text-center">
                                <img src="${sig.signature_url}" alt="Signature" class="max-w-full h-auto max-h-64 mx-auto">
                            </div>

                            <!-- Details -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-600">Dokumen:</span>
                                    <p class="text-gray-900 font-mono">${sig.document_code}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Pejabat:</span>
                                    <p class="text-gray-900">${sig.authority_name}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Upload:</span>
                                    <p class="text-gray-900">${sig.uploaded_at}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Verifikasi:</span>
                                    <p class="text-gray-900">${sig.verified_at}</p>
                                </div>
                            </div>

                            ${sig.rejection_reason ? `
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <p class="text-sm font-medium text-red-900">Alasan Penolakan:</p>
                                    <p class="text-sm text-red-800 mt-1">${sig.rejection_reason}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                } else {
                    content.innerHTML = '<div class="text-center py-8 text-red-600">Gagal memuat data</div>';
                }
            })
            .catch(error => {
                content.innerHTML = '<div class="text-center py-8 text-red-600">Terjadi kesalahan</div>';
            });
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
