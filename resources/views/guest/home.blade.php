@extends('layouts.guest', ['title' => 'Beranda', 'active' => 'home'])

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-yellow-50 to-white">

    <section class="py-20 px-6">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center">
                <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-bold mb-6">
                    🎓 Sistem Pengajuan Dokumen Online
                </div>
                <h1 class="text-5xl md:text-6xl font-black text-green-800 mb-4">
                    SIPETER Dokumen
                </h1>
                <p class="text-2xl text-yellow-600 font-bold mb-2">
                    Sistem Pengajuan Terpadu
                </p>
                <p class="text-xl text-gray-700 mb-8">
                    STABA Bandung
                </p>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-12">
                    Ajukan berbagai jenis dokumen secara online dengan mudah dan cepat.
                    Pantau status pengajuan Anda kapan saja via WhatsApp!
                </p>

                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="{{ route('mahasiswa.form') }}"
                       class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-4 rounded-xl font-bold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Ajukan Dokumen</span>
                    </a>
                    <a href="{{ route('mahasiswa.tracking') }}"
                       class="bg-white text-green-600 border-2 border-green-600 px-8 py-4 rounded-xl font-bold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Lacak Status</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 px-6 bg-white">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl font-black text-green-800 text-center mb-12">
                Jenis Dokumen Yang Dapat Diajukan
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $documents = [
                        ['title' => 'Surat Keterangan Aktif Kuliah', 'days' => 2],
                        ['title' => 'Surat Pengantar PKL', 'days' => 3],
                        ['title' => 'Surat Pengantar Skripsi', 'days' => 3],
                        ['title' => 'Transkrip Nilai Sementara', 'days' => 2],
                        ['title' => 'Legalisir Ijazah/Transkrip', 'days' => 2],
                        ['title' => 'Surat Keterangan Lulus', 'days' => 3],
                        ['title' => 'Surat Keterangan Kelakuan Baik', 'days' => 3],
                    ];
                @endphp

                @foreach($documents as $doc)
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all hover:scale-105">
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-600 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-blue-900 mb-2">{{ $doc['title'] }}</h3>
                            <p class="text-sm text-blue-700">⏱️ {{ $doc['days'] }} hari kerja</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 px-6 bg-gradient-to-br from-green-50 to-yellow-50">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl font-black text-green-800 text-center mb-12">
                Cara Mengajukan Dokumen
            </h2>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-lg">
                        1
                    </div>
                    <h3 class="font-bold text-lg mb-2">Isi Formulir</h3>
                    <p class="text-gray-600">Lengkapi data diri dan pilih jenis dokumen yang dibutuhkan</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-lg">
                        2
                    </div>
                    <h3 class="font-bold text-lg mb-2">Dapatkan Kode</h3>
                    <p class="text-gray-600">Simpan kode tracking & dapatkan notifikasi WhatsApp</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-lg">
                        3
                    </div>
                    <h3 class="font-bold text-lg mb-2">Pantau Status</h3>
                    <p class="text-gray-600">Lacak progress via WhatsApp atau website</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-lg">
                        4
                    </div>
                    <h3 class="font-bold text-lg mb-2">Ambil Dokumen</h3>
                    <p class="text-gray-600">Ambil dokumen jadi di bagian akademik</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 px-6 bg-gradient-to-r from-green-600 to-green-700 text-white">
        <div class="container mx-auto max-w-4xl text-center">
            <h2 class="text-3xl font-black mb-4">Siap Mengajukan Dokumen?</h2>
            <p class="text-xl mb-8">Proses cepat, mudah, dan terpantau real-time!</p>
            <a href="{{ route('mahasiswa.form') }}"
               class="bg-white text-green-700 px-10 py-4 rounded-xl font-bold text-lg hover:shadow-2xl transform hover:scale-105 transition-all inline-block">
                Mulai Pengajuan Sekarang →
            </a>
        </div>
    </section>
</div>
@endsection
