@extends('layouts.admin')

@section('title', 'Verifikasi Tanda Tangan - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">

{{-- ✅ KONDISI 1: Tampilkan LIST (dari index()) --}}
@if(isset($showList) && $showList === true)

    {{-- Header List --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">✅ Verifikasi Tanda Tangan</h1>
                <p class="text-gray-600 mt-2">Kelola dan verifikasi tanda tangan yang telah diunggah pejabat</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.signatures.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    ← Kembali
                </a>
                <a href="{{ route('admin.signatures.pending') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    📋 Semua Pending
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Menunggu</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $uploadedCount }}</p>
                </div>
                <div class="text-blue-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600">{{ $todayCount }}</p>
                </div>
                <div class="text-green-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Minggu Ini</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $weekCount }}</p>
                </div>
                <div class="text-purple-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.signatures.verify.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🔍 Cari Dokumen</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Kode dokumen atau nama pemohon..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">👤 Filter Pejabat</label>
                <select
                    name="authority"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Pejabat</option>
                    @foreach($authorities as $auth)
                        <option value="{{ $auth->id }}" {{ request('authority') == $auth->id ? 'selected' : '' }}>
                            {{ $auth->name }} - {{ $auth->position }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    🔍 Filter
                </button>
                @if(request()->hasAny(['search', 'authority']))
                    <a
                        href="{{ route('admin.signatures.verify.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($signatures->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemohon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pejabat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upload</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($signatures as $sig)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 font-mono">{{ $sig->documentRequest->request_code }}</div>
                            <div class="text-xs text-gray-500">{{ $sig->documentRequest->documentType->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $sig->documentRequest->applicant_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $sig->signatureAuthority->name }}</div>
                            <div class="text-xs text-gray-500">{{ $sig->signatureAuthority->position }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $sig->uploaded_at->format('d/m/Y H:i') }}</div>
                            <div class="text-xs text-blue-600">{{ $sig->uploaded_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <img
                                src="{{ Storage::url($sig->signature_file) }}"
                                alt="TTD"
                                class="h-12 w-auto max-w-24 object-contain border rounded"
                            >
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a
                                href="{{ route('admin.signatures.verify-form', $sig->id) }}"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition"
                            >
                                ✅ Verifikasi
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 bg-gray-50">
                {{ $signatures->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-600">Tidak ada tanda tangan yang menunggu verifikasi</p>
            </div>
        @endif
    </div>

{{-- ✅ KONDISI 2: Tampilkan FORM DETAIL (dari verifyForm()) --}}
@else

    {{-- Header Form --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">✅ Verifikasi Tanda Tangan</h1>
                <p class="text-gray-600 mt-2">Periksa dan verifikasi tanda tangan yang telah diunggah</p>
            </div>
            <a href="{{ route('admin.signatures.pending') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📄 Info Dokumen</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="font-medium text-gray-600">Kode Dokumen</label>
                            <p class="text-gray-900 font-mono mt-1">{{ $signature->documentRequest->request_code }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Jenis Dokumen</label>
                            <p class="text-gray-900 mt-1">{{ $signature->documentRequest->documentType->name }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Pemohon</label>
                            <p class="text-gray-900 mt-1">{{ $signature->documentRequest->applicant_name }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Status Dokumen</label>
                            <div class="mt-1">
                                <x-status-badge :status="$signature->documentRequest->status" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Info Pejabat</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="font-medium text-gray-600">Nama Pejabat</label>
                            <p class="text-gray-900 mt-1">{{ $signature->signatureAuthority->name }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Jabatan</label>
                            <p class="text-gray-900 mt-1">{{ $signature->signatureAuthority->position }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Email</label>
                            <p class="text-gray-900 mt-1">{{ $signature->signatureAuthority->email }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">WhatsApp</label>
                            <p class="text-gray-900 mt-1">{{ $signature->signatureAuthority->whatsapp_number }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Status TTD</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="font-medium text-gray-600">Status Saat Ini</label>
                            <div class="mt-1">
                                <x-signature-status-badge :status="$signature->status" />
                            </div>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Waktu Upload</label>
                            <p class="text-gray-900 mt-1">{{ $signature->uploaded_at->format('d/m/Y H:i') }} WIB</p>
                        </div>
                        @if($signature->verification_notes)
                        <div>
                            <label class="font-medium text-gray-600">Catatan Pejabat</label>
                            <p class="text-gray-900 mt-1">{{ $signature->verification_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="border-t pt-4">
                    <a
                        href="{{ route('admin.documents.show', $signature->document_request_id) }}"
                        class="block w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-center font-medium"
                    >
                        👁️ Lihat Detail Dokumen
                    </a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">✏️ Tanda Tangan yang Diunggah</h3>

                <div class="mb-6">
                    <x-signature-preview :signature="$signature" :showDetails="true" />
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">📌 Panduan Verifikasi:</h4>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Pastikan tanda tangan sesuai dengan pejabat yang berwenang</li>
                        <li>Periksa kejelasan dan kualitas gambar tanda tangan</li>
                        <li>Verifikasi keaslian tanda tangan</li>
                        <li>Pastikan format file sesuai</li>
                        <li>Tolak jika tanda tangan tidak jelas</li>
                    </ul>
                </div>

                @if($signature->status->value === 'uploaded')
                    <form action="{{ route('admin.signatures.verify.process', $signature->id) }}" method="POST" id="verification-form">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Keputusan Verifikasi <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <button
                                        type="button"
                                        onclick="setAction('approve')"
                                        class="flex items-center justify-center px-6 py-4 border-2 border-green-500 rounded-lg text-green-600 hover:bg-green-50 font-medium transition"
                                        id="approve-btn"
                                    >
                                        <span class="text-2xl mr-2">✅</span>
                                        <span>Setujui Tanda Tangan</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="setAction('reject')"
                                        class="flex items-center justify-center px-6 py-4 border-2 border-red-500 rounded-lg text-red-600 hover:bg-red-50 font-medium transition"
                                        id="reject-btn"
                                    >
                                        <span class="text-2xl mr-2">❌</span>
                                        <span>Tolak Tanda Tangan</span>
                                    </button>
                                </div>
                                <input type="hidden" name="action" id="action" required>
                            </div>

                            <div id="notes-container" class="hidden">
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Admin (Opsional)
                                </label>
                                <textarea
                                    name="admin_notes"
                                    id="admin_notes"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Tambahkan catatan..."
                                ></textarea>
                            </div>

                            <div id="rejection-container" class="hidden">
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    name="rejection_reason"
                                    id="rejection_reason"
                                    rows="4"
                                    class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                    placeholder="Jelaskan alasan penolakan..."
                                ></textarea>
                            </div>

                            <div id="confirm-message" class="hidden"></div>

                            <div class="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium disabled:opacity-50"
                                    id="submit-button"
                                    disabled
                                >
                                    Proses Verifikasi
                                </button>
                                <a
                                    href="{{ route('admin.signatures.pending') }}"
                                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                                >
                                    Batal
                                </a>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-center py-6">
                        @if($signature->status->value === 'verified')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tanda Tangan Sudah Diverifikasi</h3>
                                <p class="text-gray-600">
                                    Diverifikasi pada {{ $signature->verified_at->format('d/m/Y H:i') }} WIB
                                </p>
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tanda Tangan Ditolak</h3>
                                @if($signature->rejection_reason)
                                    <p class="text-sm text-red-800 mt-4">{{ $signature->rejection_reason }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if(isset($previousSignatures) && $previousSignatures->count() > 0)
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📚 Tanda Tangan Sebelumnya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($previousSignatures as $prev)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <img
                                src="{{ Storage::url($prev->signature_file) }}"
                                alt="Previous"
                                class="max-w-full h-32 object-contain mx-auto mb-2"
                            >
                            <p class="text-xs text-gray-600 text-center">
                                {{ $prev->documentRequest->request_code }}<br>
                                {{ $prev->verified_at->format('d/m/Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

@endif

</div>

<script>
    let selectedAction = null;

    function setAction(action) {
        selectedAction = action;
        document.getElementById('action').value = action;

        const approveBtn = document.getElementById('approve-btn');
        const rejectBtn = document.getElementById('reject-btn');
        const submitBtn = document.getElementById('submit-button');
        const notesContainer = document.getElementById('notes-container');
        const rejectionContainer = document.getElementById('rejection-container');
        const confirmMessage = document.getElementById('confirm-message');

        approveBtn.classList.remove('bg-green-100', 'border-green-600');
        rejectBtn.classList.remove('bg-red-100', 'border-red-600');

        if (action === 'approve') {
            approveBtn.classList.add('bg-green-100', 'border-green-600');
            notesContainer.classList.remove('hidden');
            rejectionContainer.classList.add('hidden');
            document.getElementById('rejection_reason').removeAttribute('required');

            confirmMessage.className = 'bg-green-50 border border-green-200 rounded-lg p-4';
            confirmMessage.innerHTML = '<p class="text-sm text-green-800"><strong>✓ Persetujuan:</strong> Tanda tangan akan diverifikasi.</p>';
            confirmMessage.classList.remove('hidden');

            submitBtn.disabled = false;
            submitBtn.innerHTML = '✅ Setujui & Verifikasi';
            submitBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
            submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');

        } else if (action === 'reject') {
            rejectBtn.classList.add('bg-red-100', 'border-red-600');
            notesContainer.classList.remove('hidden');
            rejectionContainer.classList.remove('hidden');
            document.getElementById('rejection_reason').setAttribute('required', 'required');

            confirmMessage.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
            confirmMessage.innerHTML = '<p class="text-sm text-red-800"><strong>✗ Penolakan:</strong> Pejabat akan diminta upload ulang.</p>';
            confirmMessage.classList.remove('hidden');

            submitBtn.disabled = false;
            submitBtn.innerHTML = '❌ Tolak Tanda Tangan';
            submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        }
    }

    document.getElementById('verification-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!selectedAction) {
            alert('Silakan pilih keputusan verifikasi!');
            return;
        }

        if (selectedAction === 'reject' && !document.getElementById('rejection_reason').value.trim()) {
            alert('Alasan penolakan harus diisi!');
            return;
        }

        const message = selectedAction === 'approve'
            ? 'Yakin menyetujui tanda tangan ini?'
            : 'Yakin menolak tanda tangan ini?';

        if (confirm(message)) {
            document.getElementById('submit-button').disabled = true;
            this.submit();
        }
    });
</script>
@endsection
