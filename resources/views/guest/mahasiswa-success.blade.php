@extends('layouts.guest', ['title' => 'Pengajuan Berhasil', 'active' => ''])

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-yellow-50 to-white py-12 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">

            <div class="text-center mb-8">
                <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg animate-bounce-slow">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-green-800 mb-3">Pengajuan Berhasil!</h1>
                <p class="text-gray-600 text-lg">Dokumen Anda telah berhasil diajukan dan sedang menunggu persetujuan admin.</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 mb-8 border border-blue-200 text-center">
                <h2 class="text-lg font-semibold text-blue-900 mb-3">Kode Dokumen Anda:</h2>
                <div class="flex items-center justify-center gap-3 bg-white rounded-lg p-3 border border-blue-300 shadow-inner">
                    <span id="trackingCode" class="text-2xl font-bold text-blue-800 tracking-wider">
                        {{ $documentRequest->request_code ?? 'KODE_ERROR' }}
                    </span>
                    <button onclick="copyTrackingCode(this)" title="Salin Kode"
                            class="p-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-all flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM6 11a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM11 15a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"/>
                            <path fill-rule="evenodd" d="M4 3a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V3zm2-1a1 1 0 00-1 1v14a1 1 0 001 1h10a1 1 0 001-1V3a1 1 0 00-1-1H6z" clip-rule="evenodd"/>
                        </svg>
                        Salin
                    </button>
                </div>
                <p class="text-xs text-blue-700 mt-3">
                    Simpan kode ini untuk melacak status pengajuan Anda.
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        @if($documentRequest->isDownloadDelivery())
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 4h5m-5 4h5m-5 4h5"></path>
                            </svg>
                        @endif
                    </div>
                    <h4 class="font-semibold text-gray-800">Metode Pengambilan: {{ $documentRequest->getDeliveryMethodLabel() }}</h4>
                </div>
                @if($documentRequest->isDownloadDelivery())
                    <p class="text-sm text-gray-600">
                        Setelah dokumen Anda siap, Anda dapat mengunduhnya langsung melalui halaman Lacak Dokumen.
                    </p>
                @else
                    <p class="text-sm text-gray-600">
                        Anda dapat mengambil dokumen fisik di Bagian Akademik STABA Bandung setelah statusnya "Siap Diambil".
                    </p>
                @endif
            </div>

            <div class="space-y-6 mb-8">
                <h3 class="text-xl font-bold text-gray-800 text-center">📋 Tahapan Proses Dokumen</h3>

                <div class="relative">
                    <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-200 to-green-200"></div>

                    <div class="relative flex gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center border-4 border-white shadow z-10">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-1">
                            <h4 class="font-bold text-gray-900">✅ Pengajuan Diterima</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Dokumen Anda telah masuk sistem dan menunggu review admin.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Estimasi: Hari ini</p>
                        </div>
                    </div>

                    <div class="relative flex gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center border-4 border-white shadow z-10">
                            <span class="text-xl font-bold text-blue-600">2</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <h4 class="font-bold text-gray-900">📝 Review Admin</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Admin akan memeriksa kelengkapan dan kebenaran data Anda.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Estimasi: 1-2 hari kerja</p>
                        </div>
                    </div>

                    <div class="relative flex gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center border-4 border-white shadow z-10">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-1">
                            <h4 class="font-bold text-gray-900">🔍 Verifikasi Kelayakan</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Ketua Akademik/Kemahasiswaan akan memverifikasi kelayakan Anda menerima dokumen.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Estimasi: 1 hari kerja</p>
                        </div>
                    </div>

                    <div class="relative flex gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center border-4 border-white shadow z-10">
                            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-1">
                            <h4 class="font-bold text-gray-900">✍️ Penandatanganan Digital</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Pejabat berwenang akan menandatangani dokumen Anda secara digital dengan QR Code.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Estimasi: 1-2 hari kerja</p>
                        </div>
                    </div>

                    <div class="relative flex gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center border-4 border-white shadow z-10">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-1">
                            <h4 class="font-bold text-gray-900">📄 Finalisasi Dokumen</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Admin akan memproses, menyatukan TTD digital + QR Code, dan mengupload dokumen final.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Estimasi: 1 hari kerja</p>
                        </div>
                    </div>

                    <div class="relative flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center border-4 border-white shadow z-10">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-1">
                            <h4 class="font-bold text-gray-900">🎉 Siap Diambil!</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Anda akan menerima notifikasi WhatsApp dan dapat {{ $documentRequest->isDownloadDelivery() ? 'mendownload' : 'mengambil' }} dokumen Anda.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Total estimasi: 4-6 hari kerja</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 mb-8 border border-yellow-200">
                <h3 class="text-lg font-bold text-yellow-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    Notifikasi yang Akan Anda Terima
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span><strong>Request Disetujui</strong> - Admin menyetujui pengajuan Anda</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600 font-bold">🔍</span>
                        <span><strong>Sedang Diverifikasi</strong> - Dokumen dalam proses verifikasi kelayakan</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span><strong>Verifikasi Disetujui</strong> - Anda layak menerima dokumen</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600 font-bold">✍️</span>
                        <span><strong>Sedang Ditandatangani</strong> - Pejabat sedang menandatangani dokumen</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-orange-600 font-bold">📝</span>
                        <span><strong>TTD Terverifikasi</strong> - Tanda tangan digital telah diverifikasi</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold text-lg">🎉</span>
                        <span><strong>Dokumen Siap!</strong> - Dokumen sudah siap diambil/download</span>
                    </li>
                </ul>
                <p class="text-xs text-yellow-800 mt-4 bg-yellow-100 rounded p-2">
                    <strong>💡 Tips:</strong> Pastikan nomor WhatsApp Anda aktif untuk menerima notifikasi real-time!
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="{{ route('mahasiswa.tracking') }}"
                   class="flex items-center gap-3 p-4 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="font-bold">Lacak Status</p>
                        <p class="text-xs text-indigo-100">Cek progress dokumen</p>
                    </div>
                </a>

                <a href="{{ route('home') }}"
                   class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="font-bold">Kembali ke Beranda</p>
                        <p class="text-xs text-gray-100">Halaman utama</p>
                    </div>
                </a>
            </div>

            <div class="text-center pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Terima kasih telah menggunakan <strong>SIPETER</strong> 🙏
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow { animation: bounce-slow 2s infinite; }
</style>

@push('scripts')
<script>
function copyTrackingCode(buttonElement) {
    const codeElement = document.getElementById('trackingCode');
    if (!codeElement) return;

    const code = codeElement.innerText;
    const originalHTML = buttonElement.innerHTML;

    navigator.clipboard.writeText(code).then(() => {
        buttonElement.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Disalin!
        `;
        buttonElement.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        buttonElement.classList.add('bg-green-600');
        buttonElement.disabled = true;

        setTimeout(() => {
            buttonElement.innerHTML = originalHTML;
            buttonElement.classList.remove('bg-green-600');
            buttonElement.classList.add('bg-blue-600', 'hover:bg-blue-700');
            buttonElement.disabled = false;
        }, 2000);

    }).catch(err => {
        console.error('Gagal menyalin kode:', err);
    });
}
</script>
@endpush
@endsection
