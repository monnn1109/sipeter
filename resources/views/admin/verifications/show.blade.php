@extends('layouts.admin')

@section('title', 'Detail Verifikasi - ' . $verification->documentRequest->request_code)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a></li>
            <li>/</li>
            <li><a href="{{ route('admin.verifications.index') }}" class="hover:text-blue-600">Verifications</a></li>
            <li>/</li>
            <li class="text-gray-800 font-medium">Detail</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Verifikasi</h1>
            <p class="text-gray-600 mt-1">{{ $verification->documentRequest->request_code }}</p>
        </div>
        <a href="{{ route('admin.verifications.index') }}"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Status Banner --}}
    <div class="mb-6 p-4 rounded-lg
        {{ $verification->status === 'approved' ? 'bg-green-50 border border-green-200' :
           ($verification->status === 'rejected' ? 'bg-red-50 border border-red-200' :
           'bg-yellow-50 border border-yellow-200') }}">
        <div class="flex items-start gap-3">
            @if($verification->status === 'approved')
                <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @elseif($verification->status === 'rejected')
                <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @else
                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @endif
            <div class="flex-1">
                <h4 class="font-semibold
                    {{ $verification->status === 'approved' ? 'text-green-800' :
                       ($verification->status === 'rejected' ? 'text-red-800' : 'text-yellow-800') }}">
                    Status: {{ ucfirst($verification->status) }}
                </h4>
                <p class="text-sm mt-1
                    {{ $verification->status === 'approved' ? 'text-green-700' :
                       ($verification->status === 'rejected' ? 'text-red-700' : 'text-yellow-700') }}">
                    @if($verification->status === 'requested')
                        Menunggu verifikasi dari pejabat
                    @elseif($verification->status === 'approved')
                        Diverifikasi dan disetujui pada {{ $verification->verified_at->format('d M Y, H:i') }} WIB
                    @else
                        Ditolak pada {{ $verification->verified_at->format('d M Y, H:i') }} WIB
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Informasi Dokumen --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">📄 Informasi Dokumen</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm text-gray-600">No. Dokumen</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $verification->documentRequest->request_code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Jenis Dokumen</label>
                    <p class="text-gray-800">{{ $verification->documentRequest->documentType->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Pemohon</label>
                    <p class="text-gray-800">{{ $verification->documentRequest->applicant_name }}</p>
                    <p class="text-sm text-gray-600">{{ $verification->documentRequest->applicant_identifier }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Tanggal Request Dokumen</label>
                    <p class="text-gray-800">{{ $verification->documentRequest->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div class="pt-4 border-t">
                    <a href="{{ route('admin.documents.show', $verification->documentRequest->id) }}"
                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Detail Dokumen
                    </a>
                </div>
            </div>
        </div>

        {{-- Informasi Verifikasi --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">🔍 Informasi Verifikasi</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm text-gray-600">Verifikator</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $verification->authority->name }}</p>
                    <p class="text-sm text-gray-600">{{ $verification->authority->position }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Tipe Pejabat</label>
                    <p>
                        @if($verification->authority->authority_type === 'academic')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                🎓 Akademik
                            </span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                👥 Kemahasiswaan
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <p class="text-gray-800">{{ $verification->authority->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">WhatsApp</label>
                    <p class="text-gray-800">📱 {{ $verification->authority->phone }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Request Dikirim</label>
                    <p class="text-gray-800">{{ $verification->sent_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Link Expires</label>
                    <p class="text-gray-800">{{ $verification->expires_at->format('d M Y, H:i') }} WIB</p>
                    @if($verification->expires_at < now())
                        <span class="text-xs text-red-600 font-semibold">(Expired)</span>
                    @else
                        <span class="text-xs text-green-600 font-semibold">(Masih aktif)</span>
                    @endif
                </div>

                @if($verification->verified_at)
                <div class="pt-4 border-t">
                    <label class="text-sm text-gray-600">Tanggal Respon</label>
                    <p class="text-gray-800">{{ $verification->verified_at->format('d M Y, H:i') }} WIB</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Notes/Reason --}}
    @if($verification->notes)
    <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">
                {{ $verification->status === 'rejected' ? '❌ Alasan Penolakan' : '📝 Catatan' }}
            </h2>
        </div>
        <div class="p-6">
            <p class="text-gray-800 whitespace-pre-line
                {{ $verification->status === 'rejected' ? 'bg-red-50 p-4 rounded-lg border border-red-200' : 'bg-gray-50 p-4 rounded-lg' }}">
                {{ $verification->notes }}
            </p>
        </div>
    </div>
    @endif

    {{-- Actions for Pending --}}
    @if($verification->status === 'requested')
    <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">⚡ Tindakan</h2>
        </div>
        <div class="p-6 flex gap-4">
            <form action="{{ route('admin.verifications.resend', $verification->documentRequest->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow">
                    📤 Kirim Ulang WhatsApp
                </button>
            </form>

            <form action="{{ route('admin.verifications.cancel', $verification->documentRequest->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin membatalkan verifikasi?')">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition shadow">
                    ❌ Batalkan Verifikasi
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
