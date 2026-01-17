@extends('layouts.admin')

@section('title', 'Request Verifikasi 3 Level')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a></li>
            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
            <li><a href="{{ route('admin.documents.index') }}" class="hover:text-blue-600 transition">Documents</a></li>
            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
            <li><a href="{{ route('admin.documents.show', $document->id) }}" class="hover:text-blue-600 transition">{{ $document->request_code }}</a></li>
            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
            <li class="text-gray-900 font-semibold">Verifikasi</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request Verifikasi 3 Level</h1>
                <p class="text-gray-600 mt-1">Sistem akan mengirim ke 3 pejabat secara berurutan</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT COLUMN: Document Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Document Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-5 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Dokumen
                    </h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Dokumen</label>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $document->request_code }}</p>
                    </div>
                    <div class="border-t pt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Dokumen</label>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $document->documentType->name }}</p>
                    </div>
                    <div class="border-t pt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemohon</label>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $document->applicant_name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $document->applicant_type->value === 'mahasiswa' ? 'NIM' : 'NIP' }}: {{ $document->applicant_identifier }}</p>
                    </div>
                    @if($document->applicant_unit)
                    <div class="border-t pt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Program Studi/Unit</label>
                        <p class="text-base text-gray-800 mt-1">{{ $document->applicant_unit }}</p>
                    </div>
                    @endif
                    <div class="border-t pt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Request</label>
                        <p class="text-base text-gray-800 mt-1">{{ $document->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>

            {{-- Timeline Estimasi --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-sm border-2 border-amber-200 p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-amber-900 mb-3">⏱️ Estimasi Waktu</h3>
                        <div class="space-y-2 text-sm text-amber-800">
                            <div class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span><strong>1-24 jam</strong> per level</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span>Link berlaku <strong>3 hari</strong></span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span>Total: <strong>1-3 hari kerja</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Verification Flow --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Banner --}}
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold mb-2">🔄 Sistem Otomatis & Berurutan</h3>
                        <p class="text-blue-100 text-sm leading-relaxed">
                            Sistem akan kirim WhatsApp ke Level 1. Setelah approved, <strong>otomatis lanjut</strong> ke Level 2, kemudian Level 3. Anda tidak perlu intervensi manual.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Verification Flow Visual --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Alur Verifikasi
                    </h2>
                </div>

                <div class="p-6">
                    {{-- Level 1 --}}
                    <div class="relative">
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <span class="text-white text-xl font-bold">1</span>
                                </div>
                                <div class="w-0.5 h-24 bg-gradient-to-b from-blue-400 to-purple-400 mt-3"></div>
                            </div>
                            <div class="flex-1 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full mb-2">LEVEL 1</span>
                                        <h3 class="text-lg font-bold text-gray-900">Ketua Akademik</h3>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-blue-600">33%</span>
                                        <p class="text-xs text-gray-500">Progress</p>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="font-bold text-gray-900 mb-1">{{ $ketuaAkademik->name }}</p>
                                    <p class="text-sm text-gray-600 mb-2">{{ $ketuaAkademik->position }}</p>
                                    <p class="text-sm text-gray-500 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                        </svg>
                                        {{ $ketuaAkademik->phone }}
                                    </p>
                                </div>
                                <div class="mt-3 flex items-center gap-2 text-sm text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="font-semibold">Akan menerima WhatsApp otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Level 2 --}}
                    <div class="relative">
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <span class="text-white text-xl font-bold">2</span>
                                </div>
                                <div class="w-0.5 h-24 bg-gradient-to-b from-purple-400 to-green-400 mt-3"></div>
                            </div>
                            <div class="flex-1 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border-2 border-purple-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="inline-block px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-full mb-2">LEVEL 2</span>
                                        <h3 class="text-lg font-bold text-gray-900">Wakil Ketua 3</h3>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-purple-600">66%</span>
                                        <p class="text-xs text-gray-500">Progress</p>
                                    </div>
                                </div>
                                <div class="bg-white/80 backdrop-blur rounded-lg p-4 border border-purple-200">
                                    <p class="text-sm text-gray-600 italic mb-2">🔄 Otomatis setelah Level 1 approved</p>
                                    <p class="text-xs text-gray-500">Bidang Kemahasiswaan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Level 3 --}}
                    <div class="relative">
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <span class="text-white text-xl font-bold">3</span>
                                </div>
                            </div>
                            <div class="flex-1 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-5 border-2 border-green-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="inline-block px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-full mb-2">LEVEL 3 - FINAL</span>
                                        <h3 class="text-lg font-bold text-gray-900">Direktur</h3>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-green-600">100%</span>
                                        <p class="text-xs text-gray-500">Complete</p>
                                    </div>
                                </div>
                                <div class="bg-white/80 backdrop-blur rounded-lg p-4 border border-green-200">
                                    <p class="text-sm text-gray-600 italic mb-2">🔄 Otomatis setelah Level 2 approved</p>
                                    <p class="text-xs text-gray-500">Final Approval → Lanjut TTD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Flow --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Yang Akan Terjadi
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 group">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">
                                <span class="text-blue-600 font-bold">1</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800"><strong class="text-gray-900">WhatsApp otomatis</strong> ke {{ $ketuaAkademik->name }}</p>
                                <p class="text-sm text-gray-500 mt-1">Level 1 - Ketua Akademik</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 group">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">
                                <span class="text-blue-600 font-bold">2</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800">Pejabat buka link → <strong class="text-gray-900">Approve/Reject</strong> via web</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 group">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">
                                <span class="text-purple-600 font-bold">3</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800">Jika approved → <strong class="text-gray-900">Auto lanjut Level 2</strong></p>
                                <p class="text-sm text-gray-500 mt-1">Tanpa intervensi manual</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 group">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">
                                <span class="text-purple-600 font-bold">4</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800">Level 2 approved → <strong class="text-gray-900">Auto lanjut Level 3</strong></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 group">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">
                                <span class="text-green-600 font-bold">✓</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800"><strong class="text-green-600">Selesai!</strong> Lanjut ke <strong class="text-gray-900">Request TTD</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Warning --}}
            <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl border-2 border-red-200 p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-900 mb-2">⚠️ Perhatian Penting!</h3>
                        <ul class="space-y-1.5 text-sm text-red-800">
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 font-bold mt-0.5">•</span>
                                <span>Jika <strong>ditolak di level manapun</strong>, proses <strong>BERHENTI</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 font-bold mt-0.5">•</span>
                                <span>Pastikan data dokumen sudah benar</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 font-bold mt-0.5">•</span>
                                <span>Anda akan dapat notifikasi di setiap progress</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <form action="{{ route('admin.verifications.send', $document->id) }}" method="POST">
                @csrf
                <div class="flex gap-4">
                    <a href="{{ route('admin.documents.show', $document->id) }}"
                       class="flex-1 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition text-center flex items-center justify-center gap-2 border-2 border-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-bold transition shadow-lg hover:shadow-xl text-center flex items-center justify-center gap-2 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Mulai Verifikasi 3 Level
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
