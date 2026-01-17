@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-gray-800">Riwayat Pengajuan Dokumen</h1>
        <p class="text-gray-600">Semua riwayat pengajuan dokumen dengan filter lengkap</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Filter & Pencarian</h3>

        <form action="{{ route('admin.history.index') }}" method="GET">
            <div class="grid md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pemohon</label>
                    <select name="applicant_type" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-green-500 transition-all">
                        <option value="">Semua</option>
                        <option value="mahasiswa" {{ request('applicant_type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ request('applicant_type') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="staff" {{ request('applicant_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-green-500 transition-all">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Dokumen</label>
                    <select name="document_type_id" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-green-500 transition-all">
                        <option value="">Semua</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-green-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-green-500 transition-all">
                </div>
            </div>

            <div class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 transition-all text-lg"
                       placeholder="Cari berdasarkan kode, nama, atau NIM/NIP/NIK...">
                <button type="submit"
                        class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-700 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Filter</span>
                </button>
                {{-- ✅ FIXED: admin.documents.history → admin.history.index --}}
                <a href="{{ route('admin.history.index') }}"
                   class="bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-300 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    @if($documents->total() > 0)
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-lg p-6 mb-6 text-white">
        <div class="grid md:grid-cols-4 gap-6">
            <div>
                <p class="text-green-100 text-sm mb-1">Total Data</p>
                <p class="text-3xl font-black">{{ $documents->total() }}</p>
            </div>
            <div>
                <p class="text-green-100 text-sm mb-1">Halaman Saat Ini</p>
                <p class="text-3xl font-black">{{ $documents->currentPage() }}</p>
            </div>
            <div>
                <p class="text-green-100 text-sm mb-1">Total Halaman</p>
                <p class="text-3xl font-black">{{ $documents->lastPage() }}</p>
            </div>
            <div>
                <p class="text-green-100 text-sm mb-1">Data Per Halaman</p>
                <p class="text-3xl font-black">{{ $documents->perPage() }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($documents->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pemohon</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jenis Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aktivitas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-gray-800 text-sm">{{ $doc->request_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $doc->applicant_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                            {{ $doc->applicant_type->getLabel() }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $doc->applicant_identifier }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800 font-semibold text-sm">{{ $doc->documentType->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $doc->quantity }} eksemplar</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-white inline-block"
                                      style="background-color: {{ match($doc->status->value) {
                                          'pending' => '#EAB308',
                                          'approved' => '#10B981',
                                          'rejected' => '#EF4444',
                                          'processing' => '#3B82F6',
                                          'ready' => '#8B5CF6',
                                          'picked_up' => '#6366F1',
                                          'completed' => '#6B7280',
                                          default => '#6B7280'
                                      } }}">
                                    {{ $doc->status->getLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-800 font-semibold">{{ $doc->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->created_at->format('H:i') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $doc->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="text-gray-800 font-semibold">{{ $doc->activities->count() }} aktivitas</p>
                                    @if($doc->activities->first())
                                    <p class="text-xs text-gray-500 mt-1">
                                        Terakhir: {{ $doc->activities->first()->activity_type->getLabel() }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $doc->activities->first()->created_at->diffForHumans() }}
                                    </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.documents.show', $doc->id) }}"
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-bold">{{ $documents->firstItem() }}</span> -
                <span class="font-bold">{{ $documents->lastItem() }}</span> dari
                <span class="font-bold">{{ $documents->total() }}</span> data
            </div>
            <div>
                {{ $documents->withQueryString()->links() }}
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-lg mb-4">Export Data</h3>
            <div class="flex flex-wrap gap-4">
                <button onclick="exportExcel()"
                        class="bg-green-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-700 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span>Export ke Excel</span>
                </button>

                <button onclick="exportPDF()"
                        class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span>Export ke PDF</span>
                </button>

                <button onclick="window.print()"
                        class="bg-gray-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-700 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Print</span>
                </button>
            </div>

            <div class="mt-4 p-4 bg-blue-50 rounded-xl">
                <p class="text-sm text-blue-800">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <strong>Catatan:</strong> Export akan mengikuti filter yang Anda terapkan.
                </p>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Tidak Ada Data</h3>
            <p class="text-gray-600 mb-6">Tidak ada riwayat yang sesuai dengan filter yang Anda pilih</p>
            {{-- ✅ FIXED: admin.documents.history → admin.history.index --}}
            <a href="{{ route('admin.history.index') }}"
               class="inline-block bg-green-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-700 transition-all">
                Reset Filter
            </a>
        </div>
    @endif
</div>

<script>
function exportExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    alert('Fitur export Excel akan segera tersedia!\n\nURL Export: /admin/history/export?' + params.toString());
}

function exportPDF() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'pdf');
    alert('Fitur export PDF akan segera tersedia!\n\nURL Export: /admin/history/export?' + params.toString());
}

@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white;
    }
}
</script>

<style>
@media print {
    header, aside, .no-print, button, form {
        display: none !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-footer-group;
    }
}
</style>
@endsection
