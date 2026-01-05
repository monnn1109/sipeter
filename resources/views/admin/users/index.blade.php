@extends('layouts.admin', [
    'title' => 'Kelola User',
    'subtitle' => 'Manajemen user Dosen & Staff'
])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-900">Daftar User</h2>
            <p class="text-gray-600 mt-1">Total {{ $users->total() }} user terdaftar</p>
        </div>
        <a
            href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
            </svg>
            Tambah User Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid md:grid-cols-3 gap-4">
            <div>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, email, atau NIP/NIDN..."
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>

            <select name="role" class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
            </select>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                    Cari
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @if($users && $users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NIP/NIDN</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Bergabung</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                            @if($user->phone)
                                                <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $user->email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $user->nip_nidn }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-red-100 text-red-800 border-red-200',
                                            'dosen' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'staff' => 'bg-green-100 text-green-800 border-green-200',
                                        ];
                                    @endphp
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border-2 {{ $roleColors[$user->role->value] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        {{ $user->role->getLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.users.edit', $user->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <button
                                                onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')"
                                                class="inline-flex items-center gap-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        @else
                                            <span class="px-3 py-2 bg-gray-200 text-gray-500 rounded-lg text-sm cursor-not-allowed">
                                                Anda sendiri
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-700 mb-2">
                    @if(request()->hasAny(['search', 'role']))
                        Tidak ada user yang ditemukan
                    @else
                        Belum ada user
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->hasAny(['search', 'role']))
                        Coba kata kunci lain atau hapus filter
                    @else
                        Tambahkan user baru untuk memulai
                    @endif
                </p>
                @if(request()->hasAny(['search', 'role']))
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-xl transition-all">
                        Reset Filter
                    </a>
                @else
                    <a
                        href="{{ route('admin.users.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                        </svg>
                        Tambah User Baru
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@foreach($users as $user)
    @if($user->id !== auth()->id())
        <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endforeach

@push('scripts')
<script>
function deleteUser(id, name) {
    if (confirm(`Apakah Anda yakin ingin MENGHAPUS user "${name}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endpush
@endsection
