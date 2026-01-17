@extends('layouts.guest')

@section('title', 'Form Permohonan Dokumen')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Permohonan Dokumen Mahasiswa</h2>
                <p class="mt-2 text-gray-600">Silakan lengkapi formulir di bawah ini untuk mengajukan permohonan dokumen</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold text-lg">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold text-lg mb-1">Oops! Terjadi Kesalahan</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-bold text-lg mb-2">Ada Kesalahan Validasi</p>
                            <ul class="list-disc list-inside space-y-1 pl-2">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('mahasiswa.submit') }}" method="POST" class="space-y-6" id="mahasiswaForm">
                @csrf

                <div>
                    <label for="document_type_id" class="block text-sm font-medium text-gray-700">
                        Pilih Jenis Dokumen <span class="text-red-500">*</span>
                    </label>
                    <select id="document_type_id" name="document_type_id"
                            class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('document_type_id') border-red-500 @else border-gray-300 @enderror"
                            required>
                        <option value="">-- Pilih salah satu --</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_type_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="applicant_name" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="applicant_name" name="applicant_name"
                               value="{{ old('applicant_name') }}"
                               class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('applicant_name') border-red-500 @else border-gray-300 @enderror"
                               placeholder="Nama lengkap Anda" required>
                        @error('applicant_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="applicant_nim" class="block text-sm font-medium text-gray-700">
                            NIM <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="applicant_nim" name="applicant_nim"
                               value="{{ old('applicant_nim') }}"
                               class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('applicant_nim') border-red-500 @else border-gray-300 @enderror"
                               placeholder="NIM Anda" required>
                        @error('applicant_nim')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="applicant_unit" class="block text-sm font-medium text-gray-700">
                            Program Studi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="applicant_unit" name="applicant_unit"
                               value="{{ old('applicant_unit') }}"
                               class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('applicant_unit') border-red-500 @else border-gray-300 @enderror"
                               placeholder="Contoh: S1 Akuntansi" required>
                        @error('applicant_unit')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="applicant_phone" class="block text-sm font-medium text-gray-700">
                            No. HP/WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex rounded-lg shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-600 sm:text-sm font-medium">
                                +62
                            </span>
                            <input type="tel" id="applicant_phone" name="applicant_phone"
                                   value="{{ old('applicant_phone') }}"
                                   class="block w-full flex-1 px-3 py-2 border rounded-none rounded-r-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('applicant_phone') border-red-500 @else border-gray-300 @enderror"
                                   placeholder="812345678"
                                   pattern="[0-9]{9,12}"
                                   required>
                        </div>
                        @error('applicant_phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-1 text-xs text-gray-500">Contoh: 812345678 (tanpa 0 di depan)</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="applicant_email" class="block text-sm font-medium text-gray-700">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="applicant_email" name="applicant_email"
                           value="{{ old('applicant_email') }}"
                           class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('applicant_email') border-red-500 @else border-gray-300 @enderror"
                           placeholder="email@student.staba.ac.id" required>
                    @error('applicant_email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="applicant_address" class="block text-sm font-medium text-gray-700">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea id="applicant_address" name="applicant_address" rows="3"
                              class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('applicant_address') border-red-500 @else border-gray-300 @enderror"
                              placeholder="Alamat lengkap Anda" required>{{ old('applicant_address') }}</textarea>
                    @error('applicant_address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">
                        Keperluan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="purpose" name="purpose" rows="3"
                              class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('purpose') border-red-500 @else border-gray-300 @enderror"
                              placeholder="Contoh: Untuk mengajukan beasiswa" required>{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Metode Pengambilan Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- ❌ PICKUP (BUKAN DEFAULT LAGI) --}}
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none @error('delivery_method') border-red-500 @else border-gray-300 @enderror hover:border-blue-400 transition-colors">
                            <input type="radio" name="delivery_method" value="pickup"
                                class="sr-only"
                                {{ old('delivery_method') == 'pickup' ? 'checked' : '' }}
                                required>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="block text-sm font-bold text-gray-900">Ambil di Tempat</span>
                                    </span>
                                    <span class="mt-2 flex items-center text-xs text-gray-500">
                                        📦 Dokumen akan diambil langsung di kampus
                                    </span>
                                </span>
                            </span>
                            <svg class="h-5 w-5 text-blue-600 hidden [input:checked~span~&]:block" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </label>

                        {{-- ✅ DOWNLOAD (NO DEFAULT) --}}
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none @error('delivery_method') border-red-500 @else border-gray-300 @enderror hover:border-blue-400 transition-colors">
                            <input type="radio" name="delivery_method" value="download"
                                class="sr-only"
                                {{ old('delivery_method') == 'download' ? 'checked' : '' }}
                                required>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                        </svg>
                                        <span class="block text-sm font-bold text-gray-900">Download Online</span>
                                    </span>
                                    <span class="mt-2 flex items-center text-xs text-gray-500">
                                        📥 Dokumen akan dikirim via email/download
                                    </span>
                                </span>
                            </span>
                            <svg class="h-5 w-5 text-blue-600 hidden [input:checked~span~&]:block" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </label>
                    </div>
                    @error('delivery_method')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea id="notes" name="notes" rows="2"
                              class="mt-1 block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @else border-gray-300 @enderror"
                              placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div> -->

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium inline-flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitBtn">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="submitBtnText">Ajukan Permohonan</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mahasiswaForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtnText.textContent = 'Memproses...';

            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Ajukan Permohonan';
            }, 3000);
        });
    }

    const radioButtons = document.querySelectorAll('input[name="delivery_method"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('label.border').forEach(label => {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-300');
            });

            if (this.checked) {
                const label = this.closest('label');
                label.classList.remove('border-gray-300');
                label.classList.add('border-blue-500', 'bg-blue-50');
            }
        });
    });

    const checkedRadio = document.querySelector('input[name="delivery_method"]:checked');
    if (checkedRadio) {
        const label = checkedRadio.closest('label');
        label.classList.remove('border-gray-300');
        label.classList.add('border-blue-500', 'bg-blue-50');
    }
});
</script>
@endsection
