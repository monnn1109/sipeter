@extends('layouts.admin')

@section('title', 'Kelola Pejabat TTD - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">👔 Kelola Pejabat Penandatangan</h1>
                <p class="text-gray-600 mt-2">Manajemen pejabat yang berwenang menandatangani dokumen</p>
            </div>
            <button
                onclick="openAddModal()"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
            >
                ➕ Tambah Pejabat
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pejabat</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $authorities->total() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <span class="text-3xl">👔</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aktif</p>
                    <p class="text-3xl font-bold text-green-600">{{ $activeCount }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <span class="text-3xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Non-Aktif</p>
                    <p class="text-3xl font-bold text-red-600">{{ $inactiveCount }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <span class="text-3xl">❌</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total TTD Bulan Ini</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $monthSignatureCount }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <span class="text-3xl">✍️</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form action="{{ route('admin.signatures.authorities') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Nama/Jabatan/Email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="rektor" {{ request('type') == 'rektor' ? 'selected' : '' }}>Rektor</option>
                    <option value="dekan" {{ request('type') == 'dekan' ? 'selected' : '' }}>Dekan</option>
                    <option value="kaprodi" {{ request('type') == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                    <option value="sekretaris" {{ request('type') == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.signatures.authorities') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($authorities->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama & Jabatan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kontak
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total TTD
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($authorities as $authority)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $authority->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $authority->position }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <p class="text-gray-900">📧 {{ $authority->email }}</p>
                                        <p class="text-gray-500">📱 {{ $authority->whatsapp_number }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeConfig = match($authority->authority_type) {
                                            'rektor' => ['text' => 'Rektor', 'color' => 'purple'],
                                            'dekan' => ['text' => 'Dekan', 'color' => 'blue'],
                                            'kaprodi' => ['text' => 'Kaprodi', 'color' => 'green'],
                                            'sekretaris' => ['text' => 'Sekretaris', 'color' => 'yellow'],
                                            default => ['text' => 'Lainnya', 'color' => 'gray']
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium bg-{{ $typeConfig['color'] }}-100 text-{{ $typeConfig['color'] }}-800 rounded-full">
                                        {{ $typeConfig['text'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($authority->is_active)
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            ✓ Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            ✗ Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium">{{ $authority->signatures_count ?? 0 }}</span>
                                        <button
                                            onclick="viewSignatureHistory({{ $authority->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-xs"
                                        >
                                            📊 Lihat
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button
                                            onclick="editAuthority({{ $authority->id }})"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            ✏️ Edit
                                        </button>
                                        <button
                                            onclick="toggleStatus({{ $authority->id }}, {{ $authority->is_active ? 'false' : 'true' }})"
                                            class="text-{{ $authority->is_active ? 'red' : 'green' }}-600 hover:text-{{ $authority->is_active ? 'red' : 'green' }}-800 font-medium"
                                        >
                                            {{ $authority->is_active ? '🚫 Non-Aktifkan' : '✓ Aktifkan' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4">
                {{ $authorities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada pejabat</h3>
                <p class="mt-2 text-sm text-gray-500">Tambahkan pejabat penandatangan untuk memulai.</p>
            </div>
        @endif
    </div>
</div>

<div id="authority-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900">➕ Tambah Pejabat Baru</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="authority-form" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" id="authority-id" name="authority_id">
                <input type="hidden" id="form-method" name="_method">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Dr. Ahmad Subhan, M.Si"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="position"
                            name="position"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Rektor STABA"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="email@staba.ac.id"
                        >
                    </div>

                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                            No. WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="whatsapp_number"
                            name="whatsapp_number"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="628123456789"
                        >
                    </div>

                    <div>
                        <label for="authority_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Pejabat <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="authority_type"
                            name="authority_type"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">-- Pilih Tipe --</option>
                            <option value="ketua-akademik">Ketua Akademik</option>
                            <option value="kemahasiswaan">Kemahasiswaan</option>
                        </select>
                    </div>

                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select
                            id="is_active"
                            name="is_active"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                    >
                        💾 Simpan
                    </button>
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modal-title').textContent = '➕ Tambah Pejabat Baru';
        document.getElementById('authority-form').action = '{{ route("admin.signatures.authorities.store") }}';
        document.getElementById('form-method').value = '';
        document.getElementById('authority-form').reset();
        document.getElementById('authority-modal').classList.remove('hidden');
    }

    function editAuthority(id) {
        fetch(`/admin/signatures/authorities/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modal-title').textContent = '✏️ Edit Pejabat';
                document.getElementById('authority-form').action = `/admin/signatures/authorities/${id}`;
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('name').value = data.name;
                document.getElementById('position').value = data.position;
                document.getElementById('email').value = data.email;
                document.getElementById('whatsapp_number').value = data.whatsapp_number;
                document.getElementById('authority_type').value = data.authority_type;
                document.getElementById('is_active').value = data.is_active ? '1' : '0';
                document.getElementById('authority-modal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('authority-modal').classList.add('hidden');
    }

    function toggleStatus(id, newStatus) {
        const action = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';
        if (confirm(`Apakah Anda yakin ingin ${action} pejabat ini?`)) {
            fetch(`/admin/signatures/authorities/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_active: newStatus === 'true' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status');
                }
            });
        }
    }

    function viewSignatureHistory(id) {
        window.location.href = `/admin/signatures/history?authority=${id}`;
    }

    document.getElementById('authority-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
