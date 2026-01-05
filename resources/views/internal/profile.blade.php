@extends('layouts.internal', [
    'title' => 'Profile',
    'subtitle' => 'Kelola informasi akun Anda'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Profile Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 md:p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                <span class="text-4xl md:text-5xl font-black text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div>
                <h2 class="text-2xl md:text-3xl font-black mb-2">{{ auth()->user()->name }}</h2>
                <p class="text-blue-100 text-base md:text-lg mb-1">{{ auth()->user()->role->getLabel() }}</p>
                <p class="text-blue-200 text-sm">Bergabung sejak {{ auth()->user()->created_at->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 md:px-8 py-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                Informasi Akun
            </h3>
        </div>

        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-500 mb-2">Nama Lengkap</label>
                    <p class="text-base md:text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-500 mb-2">NIP/NIDN</label>
                    <p class="text-base md:text-lg font-semibold text-gray-900">{{ auth()->user()->nip_nidn }}</p>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-500 mb-2">Email</label>
                    <p class="text-base md:text-lg font-semibold text-gray-900 break-all">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-500 mb-2">Jabatan/Role</label>
                    <span class="inline-block px-4 py-2 bg-blue-100 text-blue-800 font-bold rounded-xl text-sm">
                        {{ auth()->user()->role->getLabel() }}
                    </span>
                </div>
                @if(auth()->user()->phone_number)
                    <div>
                        <label class="block text-xs md:text-sm font-bold text-gray-500 mb-2">Nomor WhatsApp</label>
                        <p class="text-base md:text-lg font-semibold text-gray-900">{{ auth()->user()->phone_number }}</p>
                    </div>
                @endif
                @if(auth()->user()->unit)
                    <div>
                        <label class="block text-xs md:text-sm font-bold text-gray-500 mb-2">Unit/Departemen</label>
                        <p class="text-base md:text-lg font-semibold text-gray-900">{{ auth()->user()->unit }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Update Phone Number --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 md:px-8 py-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                Update Nomor WhatsApp
            </h3>
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('internal.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-blue-800">
                            Nomor WhatsApp digunakan untuk menerima notifikasi status pengajuan dokumen Anda.
                        </p>
                    </div>
                </div>

                <div>
                    <label for="phone_number" class="block text-xs md:text-sm font-bold text-gray-700 mb-2">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-semibold">+62</span>
                        </div>
                        <input
                            type="text"
                            name="phone_number"
                            id="phone_number"
                            value="{{ old('phone_number', auth()->user()->phone_number ? ltrim(auth()->user()->phone_number, '+620') : '') }}"
                            required
                            placeholder="81234567890"
                            pattern="[0-9]{9,13}"
                            class="w-full pl-16 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('phone_number') border-red-500 @enderror">
                    </div>
                    @error('phone_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">Format: 81234567890 (tanpa 0 atau +62)</p>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <button
                        type="submit"
                        class="flex-1 flex justify-center items-center gap-2 py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 md:px-8 py-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Ganti Password
            </h3>
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('internal.profile.password') }}" method="POST" class="space-y-6" x-data="{ showCurrentPassword: false, showNewPassword: false, showConfirmPassword: false }">
                @csrf
                @method('POST')

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-yellow-800">
                            Pastikan password baru Anda kuat. Gunakan kombinasi huruf, angka, dan simbol.
                        </p>
                    </div>
                </div>

                {{-- Current Password --}}
                <div>
                    <label for="current_password" class="block text-xs md:text-sm font-bold text-gray-700 mb-2">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            :type="showCurrentPassword ? 'text' : 'password'"
                            name="current_password"
                            id="current_password"
                            required
                            class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('current_password') border-red-500 @enderror">
                        <button
                            type="button"
                            @click="showCurrentPassword = !showCurrentPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showCurrentPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showCurrentPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label for="password" class="block text-xs md:text-sm font-bold text-gray-700 mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            :type="showNewPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            required
                            minlength="8"
                            class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('password') border-red-500 @enderror">
                        <button
                            type="button"
                            @click="showNewPassword = !showNewPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showNewPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showNewPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @else
                        <p class="mt-2 text-xs text-gray-500">Minimal 8 karakter</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs md:text-sm font-bold text-gray-700 mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            :type="showConfirmPassword ? 'text' : 'password'"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            minlength="8"
                            class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <button
                        type="submit"
                        class="flex-1 flex justify-center items-center gap-2 py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('phone_number').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.startsWith('0')) {
            this.value = this.value.substring(1);
        }
    });
</script>
@endpush
@endsection
