@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-800">Semua Pengajuan</h1>
            <p class="text-gray-600">Kelola semua pengajuan dokumen</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form action="{{ route('admin.documents.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cari (Kode, Nama, NIP/NIM)</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Masukkan kata kunci..."
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Dokumen</label>
                    <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="ready_for_pickup" {{ request('status') == 'ready_for_pickup' ? 'selected' : '' }}>Siap Diambil</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                {{-- ✅ NEW: Filter Metode Pengambilan --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pengambilan</label>
                    <select name="delivery_method" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all">
                        <option value="">Semua Metode</option>
                        <option value="download" {{ request('delivery_method') == 'download' ? 'selected' : '' }}>📥 Download</option>
                        <option value="pickup" {{ request('delivery_method') == 'pickup' ? 'selected' : '' }}>🏢 Pickup</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Verifikasi</label>
                    <select name="verification_status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        <option value="">Semua</option>
                        <option value="verification_requested" {{ request('verification_status') == 'verification_requested' ? 'selected' : '' }}>Diminta</option>
                        <option value="verification_approved" {{ request('verification_status') == 'verification_approved' ? 'selected' : '' }}>Approved</option>
                        <option value="verification_rejected" {{ request('verification_status') == 'verification_rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="md:col-span-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan</label>
                            <select name="signature_status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                                <option value="">Semua</option>
                                <option value="signature_requested" {{ request('signature_status') == 'signature_requested' ? 'selected' : '' }}>Diminta</option>
                                <option value="signature_uploaded" {{ request('signature_status') == 'signature_uploaded' ? 'selected' : '' }}>Uploaded</option>
                                <option value="signature_verified" {{ request('signature_status') == 'signature_verified' ? 'selected' : '' }}>Verified</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pemohon</label>
                            <select name="applicant_type" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                                <option value="">Semua Jenis</option>
                                <option value="mahasiswa" {{ request('applicant_type') == 'mahasiswa' ? 'selected' : '' }}>👨‍🎓 Mahasiswa</option>
                                <option value="dosen" {{ request('applicant_type') == 'dosen' ? 'selected' : '' }}>👨‍🏫 Dosen</option>
                                <option value="staff" {{ request('applicant_type') == 'staff' ? 'selected' : '' }}>👔 Staff</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-12">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('admin.documents.index') }}" class="flex-1 md:flex-none px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ✅ NEW: Stats Summary berdasarkan filter aktif --}}
    @if(request()->hasAny(['delivery_method', 'status', 'applicant_type', 'verification_status', 'signature_status', 'search']))
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm mb-1">Hasil Filter</p>
                    <p class="text-3xl font-black">{{ $documents->total() }} Dokumen</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    @if(request('delivery_method'))
                        <span class="px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                            {{ request('delivery_method') == 'download' ? '📥 Download' : '🏢 Pickup' }}
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                            Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                        </span>
                    @endif
                    @if(request('applicant_type'))
                        <span class="px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                            {{ ucfirst(request('applicant_type')) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($documents->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pemohon</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl. Diajukan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Verifikasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">TTD</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                         style="background: linear-gradient(135deg, {{ $doc->applicant_type->value === 'mahasiswa' ? '#3B82F6, #1D4ED8' : ($doc->applicant_type->value === 'dosen' ? '#10B981, #059669' : '#F59E0B, #D97706') }})">
                                        {{ substr($doc->applicant_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $doc->applicant_name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $doc->applicant_identifier }}
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold ml-1"
                                                  style="background-color: {{ $doc->applicant_type->value === 'mahasiswa' ? '#DBEAFE' : ($doc->applicant_type->value === 'dosen' ? '#D1FAE5' : '#FEF3C7') }};
                                                         color: {{ $doc->applicant_type->value === 'mahasiswa' ? '#1E40AF' : ($doc->applicant_type->value === 'dosen' ? '#065F46' : '#92400E') }}">
                                                {{ $doc->applicant_type->getLabel() }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-800">{{ $doc->documentType->name }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $doc->request_code }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-800">{{ $doc->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->created_at->format('H:i') }} WIB</p>
                                <p class="text-xs text-gray-400">{{ $doc->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($doc->delivery_method->value === 'download')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg font-bold border border-green-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-bold border border-blue-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Pickup
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$doc->status" size="sm" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($doc->current_verification_step > 0)
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-bold text-gray-600">Level {{ $doc->current_verification_step }}/3</span>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 3; $i++)
                                                <div class="w-2 h-2 rounded-full {{ $i <= $doc->current_verification_step ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                            @endfor
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Belum dimulai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($doc->signatures->count() > 0)
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-bold text-purple-600">{{ $doc->signatures->where('status', 'verified')->count() }}/3</span>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 3; $i++)
                                                @php
                                                    $sig = $doc->signatures->where('signature_level', $i)->first();
                                                    $color = $sig && $sig->status->value === 'verified' ? 'bg-purple-500' : ($sig ? 'bg-yellow-400' : 'bg-gray-300');
                                                @endphp
                                                <div class="w-2 h-2 rounded-full {{ $color }}"></div>
                                            @endfor
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Belum dimulai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.documents.show', $doc->id) }}"
                                   class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $documents->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-700">Tidak Ada Dokumen</h3>
            <p class="text-gray-500 mt-2">
                @if(request()->hasAny(['delivery_method', 'status', 'applicant_type', 'verification_status', 'signature_status', 'search']))
                    Tidak ada dokumen yang sesuai dengan filter pencarian Anda.
                @else
                    Belum ada dokumen yang diajukan.
                @endif
            </p>
            @if(request()->hasAny(['delivery_method', 'status', 'applicant_type', 'verification_status', 'signature_status', 'search']))
                <a href="{{ route('admin.documents.index') }}"
                   class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-green-700 transition-all">
                    Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
