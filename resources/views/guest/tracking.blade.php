@extends('layouts.guest', ['title' => 'Lacak Dokumen', 'active' => 'tracking'])

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-white py-12 px-4">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-3">
                🔍 Lacak Status Pengajuan
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Masukkan <strong>Kode Dokumen, NIM, atau Nama</strong> untuk melacak status pengajuan Anda
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10 mb-8">

            @if($errors->has('search'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <p class="text-red-700 font-semibold">{{ $errors->first('search') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <p class="text-red-700 font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('mahasiswa.tracking.check') }}" method="POST" class="flex flex-col md:flex-row gap-3">
                @csrf
                <input
                    type="text"
                    name="search"
                    value="{{ old('search', $search ?? '') }}"
                    placeholder="Contoh: SKAK-202411-001, NIM, atau Nama"
                    required
                    autofocus
                    class="flex-1 px-6 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                >
                <button
                    type="submit"
                    class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all"
                >
                    🔍 Lacak Sekarang
                </button>
            </form>

            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>💡 Tips:</strong> Masukkan <strong>kode dokumen</strong> (contoh: SKAK-202411-001), <strong>NIM</strong>, atau <strong>nama lengkap</strong> Anda.
                </p>
            </div>
        </div>

        @if(isset($documents) && $documents->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">
                        📊 Hasil Pencarian: <span class="text-blue-600">{{ $documents->count() }}</span> dokumen ditemukan
                    </h2>
                    @if(isset($search))
                        <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-semibold">
                            Keyword: "{{ $search }}"
                        </span>
                    @endif
                </div>

                {{-- MOBILE VIEW --}}
                <div class="md:hidden space-y-3">
                    @foreach($documents as $doc)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5 hover:shadow-xl transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <p class="font-mono font-bold text-gray-900 text-sm">{{ $doc->request_code }}</p>
                                    <p class="text-base font-semibold text-gray-800 mt-1">{{ $doc->applicant_name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $doc->applicant_type->value }}: {{ $doc->applicant_identifier }}</p>
                                </div>
                                <x-status-badge :status="$doc->status" size="sm" />
                            </div>

                            <div class="text-xs text-gray-600 mb-3 space-y-1 bg-gray-50 p-3 rounded-lg">
                                <p><strong>Dokumen:</strong> {{ $doc->documentType->name ?? '-' }}</p>
                                <p><strong>Unit:</strong> {{ $doc->applicant_unit }}</p>
                                <p><strong>Tanggal:</strong> {{ $doc->created_at->format('d M Y, H:i') }}</p>
                            </div>

                            {{-- ✅ FIXED: Correct route --}}
                            <a href="{{ route('mahasiswa.tracking.detail', $doc->request_code) }}"
                               class="block w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-lg text-sm transition text-center shadow-md hover:shadow-lg">
                                📄 Detail
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- DESKTOP TABLE VIEW --}}
                <div class="hidden md:block bg-white rounded-2xl shadow-xl overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                <th class="px-6 py-4 text-left font-bold">Kode Dokumen</th>
                                <th class="px-6 py-4 text-left font-bold">Nama / ID</th>
                                <th class="px-6 py-4 text-left font-bold">Jenis Dokumen</th>
                                <th class="px-6 py-4 text-left font-bold">Tanggal</th>
                                <th class="px-6 py-4 text-center font-bold">Status</th>
                                <th class="px-6 py-4 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($documents as $doc)
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-6 py-4">
                                        <code class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                            {{ $doc->request_code }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $doc->applicant_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $doc->applicant_identifier }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $doc->documentType->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <p>{{ $doc->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $doc->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <x-status-badge :status="$doc->status" size="sm" />
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{-- ✅ FIXED: Correct route --}}
                                        <a href="{{ route('mahasiswa.tracking.detail', $doc->request_code) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition text-sm shadow-md hover:shadow-lg">
                                            📄 Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif(isset($documents) && isset($search))
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Dokumen Tidak Ditemukan</h3>
                <p class="text-gray-600 mb-6">
                    Tidak ada dokumen dengan kata kunci <strong>"{{ $search ?? '' }}"</strong>
                </p>
                <div class="space-y-3">
                    <p class="text-sm text-gray-500">Pastikan Anda memasukkan kode dokumen, NIM, atau nama yang benar.</p>
                </div>
                <a href="{{ route('mahasiswa.tracking') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition mt-6">
                    🔄 Cari Lagi
                </a>
            </div>

        @else
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-12 border-2 border-blue-200 text-center">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">🔍 Cara Melacak Dokumen</h3>
                <p class="text-gray-700 max-w-2xl mx-auto mb-6">
                    Masukkan <strong>Kode Dokumen, NIM, atau Nama</strong> Anda untuk melacak status pengajuan.
                </p>
                <div class="max-w-md mx-auto bg-white rounded-xl p-4 shadow-md">
                    <p class="text-sm font-bold text-blue-600 mb-2">📖 Contoh Pencarian:</p>
                    <p class="text-sm text-gray-600">✓ SKAK-202411-001</p>
                    <p class="text-sm text-gray-600">✓ 12345678 (NIM)</p>
                    <p class="text-sm text-gray-600">✓ Puraniwan (Nama)</p>
                </div>
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold transition-colors">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
