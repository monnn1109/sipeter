@extends('layouts.admin')

@section('title', 'Kirim Request Verifikasi 3 Level')

@section('content')
<div class="container mx-auto px-4 py-8">
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a></li>
            <li>/</li>
            <li><a href="{{ route('admin.documents.index') }}" class="hover:text-blue-600">Documents</a></li>
            <li>/</li>
            <li><a href="{{ route('admin.documents.show', $document->id) }}" class="hover:text-blue-600">{{ $document->request_code }}</a></li>
            <li>/</li>
            <li class="text-gray-800 font-medium">Send Verification</li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">📋 Request Verifikasi 3 Level</h1>
        <p class="text-gray-600">Sistem akan mengirim request verifikasi secara otomatis ke 3 level pejabat</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">📄 Informasi Dokumen</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-600">No. Dokumen</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $document->request_code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Jenis Dokumen</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $document->documentType->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Pemohon</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $document->applicant_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">{{ $document->applicant_type->value === 'mahasiswa' ? 'NIM' : 'NIP' }}</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $document->applicant_identifier }}</p>
                </div>
                @if($document->applicant_unit)
                <div>
                    <label class="text-sm text-gray-600">Program Studi / Unit</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $document->applicant_unit }}</p>
                </div>
                @endif
                <div>
                    <label class="text-sm text-gray-600">Tanggal Request</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $document->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">🔄 Alur Verifikasi 3 Level</h2>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-blue-900 mb-2">ℹ️ Sistem Otomatis:</p>
                        <p class="text-blue-800 text-sm">
                            Sistem akan <strong>otomatis mengirim WhatsApp</strong> ke 3 level pejabat secara berurutan.
                            Setelah Level 1 approve, otomatis lanjut ke Level 2, dst.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                            1
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold">LEVEL 1</p>
                            <p class="text-sm font-bold text-gray-800">Ketua Akademik</p>
                        </div>
                    </div>
                    <div class="bg-white rounded p-3 text-sm">
                        <p class="font-semibold text-gray-800 mb-1">{{ $ketuaAkademik->name }}</p>
                        <p class="text-xs text-gray-600">{{ $ketuaAkademik->position }}</p>
                        <p class="text-xs text-gray-500 mt-1">📱 {{ $ketuaAkademik->phone }}</p>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="text-xs bg-blue-600 text-white px-3 py-1 rounded-full font-semibold">
                            Progress: 33%
                        </span>
                    </div>
                </div>

                <div class="hidden md:flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-purple-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                            2
                        </div>
                        <div>
                            <p class="text-xs text-purple-600 font-semibold">LEVEL 2</p>
                            <p class="text-sm font-bold text-gray-800">Wakil Ketua 3</p>
                        </div>
                    </div>
                    <div class="bg-white rounded p-3 text-sm">
                        <p class="text-xs text-gray-500 italic">Otomatis setelah Level 1 ✅</p>
                        <p class="text-xs text-gray-400 mt-1">Kemahasiswaan</p>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="text-xs bg-purple-600 text-white px-3 py-1 rounded-full font-semibold">
                            Progress: 66%
                        </span>
                    </div>
                </div>

                <div class="hidden md:flex items-center justify-center md:col-start-2">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-green-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                            3
                        </div>
                        <div>
                            <p class="text-xs text-green-600 font-semibold">LEVEL 3 - FINAL</p>
                            <p class="text-sm font-bold text-gray-800">Direktur</p>
                        </div>
                    </div>
                    <div class="bg-white rounded p-3 text-sm">
                        <p class="text-xs text-gray-500 italic">Otomatis setelah Level 2 ✅</p>
                        <p class="text-xs text-gray-400 mt-1">Final Approval</p>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="text-xs bg-green-600 text-white px-3 py-1 rounded-full font-semibold">
                            Progress: 100%
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-medium text-yellow-900 mb-1">⏱️ Estimasi Waktu:</p>
                        <ul class="text-yellow-800 space-y-1">
                            <li>• Setiap level: <strong>1-24 jam</strong> (tergantung pejabat)</li>
                            <li>• Link verifikasi berlaku: <strong>3 hari per level</strong></li>
                            <li>• Total estimasi: <strong>1-3 hari kerja</strong> untuk semua level</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">✅ Konfirmasi Pengiriman</h2>
        </div>

        <form action="{{ route('admin.verifications.send', $document->id) }}" method="POST" class="p-6">
            @csrf

            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-blue-200 rounded-lg p-5 mb-6">
                <div class="flex items-start">
                    <div class="bg-blue-600 text-white p-2 rounded-lg mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-lg mb-2">🚀 Yang Akan Terjadi:</p>
                        <ol class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="bg-blue-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                                <span>Sistem akan <strong>otomatis kirim WhatsApp</strong> ke <strong>{{ $ketuaAkademik->name }}</strong> (Level 1 - Ketua Akademik)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-blue-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                                <span>Ketua Akademik membuka link dan <strong>Approve/Reject</strong> via web</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-purple-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                                <span>Jika <strong>Approved</strong>, sistem <strong>otomatis kirim</strong> ke Level 2 (Wakil Ketua 3)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-purple-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">4</span>
                                <span>Setelah Level 2 approved, <strong>otomatis lanjut</strong> ke Level 3 (Direktur)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-green-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">5</span>
                                <span><strong>Selesai!</strong> Setelah Level 3 approved, lanjut ke <strong>Request TTD</strong></span>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-medium text-red-900 mb-1">⚠️ Penting:</p>
                        <ul class="text-red-800 space-y-1">
                            <li>• Jika <strong>ditolak di level manapun</strong>, proses akan <strong>BERHENTI</strong></li>
                            <li>• Pastikan semua data dokumen sudah benar sebelum kirim</li>
                            <li>• Anda akan menerima notifikasi di setiap progress level</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('admin.documents.show', $document->id) }}"
                   class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-center">
                    ❌ Batal
                </a>
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-semibold transition shadow-lg">
                    🚀 Mulai Verifikasi 3 Level
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
