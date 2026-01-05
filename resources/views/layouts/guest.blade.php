<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIPETER Dokumen' }} - STABA Bandung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    @include('layouts.navbar', ['active' => $active ?? ''])

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">SIPETER Dokumen</h3>
                    <p class="text-gray-400">Sistem Pengajuan Terpadu untuk pengajuan dokumen Sekolah Tinggi Analis Bakti Asih Bandung</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Kontak</h3>
                    <p class="text-gray-400">📧 akademik@staba.ac.id</p>
                    <p class="text-gray-400">📱 0812-3456-7890</p>
                    <p class="text-gray-400">📍 STABA Bandung</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Jam Operasional</h3>
                    <p class="text-gray-400">Senin - Jumat: 08.00 - 15.00 WIB</p>
                    <p class="text-gray-400">Sabtu: 08.00 - 14.00 WIB</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} STABA Bandung. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
