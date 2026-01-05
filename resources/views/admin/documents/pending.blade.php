@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-800">Pending Approval</h1>
            <p class="text-gray-600">Pengajuan yang menunggu persetujuan Anda</p>
        </div>
        <div class="bg-yellow-100 px-4 py-2 rounded-xl">
            <span class="font-bold text-yellow-800">{{ $documents->total() }} Pending</span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl">
            <p class="text-green-700 font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl">
            <p class="text-red-700 font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    @if($documents->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pemohon</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jenis Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-gray-800">{{ $doc->request_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $doc->applicant_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $doc->applicant_type->getLabel() }} - {{ $doc->applicant_identifier }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800 font-semibold">{{ $doc->documentType->name }}</p>
                                <p class="text-sm text-gray-500">{{ $doc->quantity }} eksemplar</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-800">{{ $doc->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.documents.show', $doc->id) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak Ada Pending</h3>
            <p class="text-gray-600">Semua pengajuan sudah diproses</p>
        </div>
    @endif
</div>
@endsection
