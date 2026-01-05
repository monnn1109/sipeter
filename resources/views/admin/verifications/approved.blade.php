@extends('layouts.admin')

@section('title', 'Verifikasi Disetujui - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">✅ Verifikasi Disetujui</h1>
            <p class="text-gray-600 mt-2">Daftar dokumen yang telah diverifikasi dan disetujui</p>
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
                    <p class="text-sm text-gray-600 mb-1">Total Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_approved'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <span class="text-3xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['today'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
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
                                Diverifikasi Oleh
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Verifikasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu Proses
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <p class="text-gray-900">{{ $verification->updated_at->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">{{ $verification->updated_at->format('H:i') }} WIB</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $hours = $verification->created_at->diffInHours($verification->updated_at);
                                        $days = floor($hours / 24);
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        @if($days > 0)
                                            {{ $days }} hari {{ $hours % 24 }} jam
                                        @else
                                            {{ $hours }} jam
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">

                                        href="{{ route('admin.documents.show', $verification->document_request_id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        👁️ Detail
                                    </a>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada verifikasi disetujui</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Verifikasi yang disetujui akan muncul di sini.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
