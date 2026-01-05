@extends('layouts.admin', [
    'title' => 'Edit User',
    'subtitle' => 'Update data user ' . $user->name
])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar User
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Data User</h3>

            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nip_nidn" class="block text-sm font-bold text-gray-700 mb-2">
                    NIP/NIDN <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="nip_nidn"
                    id="nip_nidn"
                    value="{{ old('nip_nidn', $user->nip_nidn) }}"
                    required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('nip_nidn') border-red-500 @enderror">
                @error('nip_nidn')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-bold text-gray-700 mb-2">
                    Role/Jabatan <span class="text-red-500">*</span>
                </label>
                <select
                    name="role"
                    id="role"
                    required
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('role') border-red-500 @enderror {{ $user->id === auth()->id() ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role', $user->role->value) === 'admin' ? 'selected' : '' }}>Admin (Full Access)</option>
                    <option value="dosen" {{ old('role', $user->role->value) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="staff" {{ old('role', $user->role->value) === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
                @if($user->id === auth()->id())
                    <p class="mt-2 text-xs text-yellow-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Anda tidak dapat mengubah role akun sendiri
                    </p>
                @endif
                @error('role')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">
                    Nomor WhatsApp <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-semibold">+62</span>
                    </div>
                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        value="{{ old('phone', $user->phone ? ltrim($user->phone, '+62') : '') }}"
                        placeholder="81234567890"
                        pattern="[0-9]{9,13}"
                        class="w-full pl-16 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('phone') border-red-500 @enderror">
                </div>
                @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-2 text-xs text-gray-500">Format: 81234567890 (tanpa 0 atau +62)</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-6 border-t">
                <button
                    type="submit"
                    class="flex-1 flex justify-center items-center gap-2 py-4 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"/>
                    </svg>
                    Update Data User
                </button>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-6 py-4 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-xl transition-all">
                    Batal
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Ganti Password</h3>
                <span class="text-sm text-gray-500">(Opsional)</span>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <p class="text-sm text-yellow-800">
                    Kosongkan field password jika tidak ingin mengubah password user.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                        Password Baru
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        minlength="8"
                        placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @else
                        <p class="mt-2 text-xs text-gray-500">Minimal 8 karakter</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">
                        Konfirmasi Password Baru
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        minlength="8"
                        placeholder="Ulangi password baru"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>
        </div>
    </form>

    <div class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl p-6">
        <h4 class="text-sm font-bold text-gray-700 mb-4">INFORMASI TAMBAHAN</h4>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Dibuat pada:</p>
                <p class="font-semibold text-gray-900">{{ $user->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div>
                <p class="text-gray-600">Terakhir diupdate:</p>
                <p class="font-semibold text-gray-900">{{ $user->updated_at->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('phone')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.startsWith('0')) {
        this.value = this.value.substring(1);
    }
});
</script>
@endpush
@endsection
