@extends('layouts.guest')

@section('title', 'Upload Tanda Tangan - SIPETER')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">✍️ Upload Tanda Tangan</h1>
            <p class="text-gray-600">Upload tanda tangan digital untuk dokumen</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Kode Dokumen</p>
                        <p class="text-2xl font-bold">{{ $documentRequest->request_code }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Jenis Dokumen</p>
                        <p class="text-lg font-semibold">{{ $documentRequest->documentType->name }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg p-4 text-center">
                    <p class="text-sm font-medium mb-1">Level Tanda Tangan</p>
                    <p class="text-3xl font-bold">{{ $signature->signature_level ?? 1 }} / 3</p>
                    <p class="text-xs mt-1 opacity-90">
                        @if(($signature->signature_level ?? 1) == 1)
                            Ketua Akademik
                        @elseif(($signature->signature_level ?? 1) == 2)
                            Wakil Direktur 3
                        @else
                            Direktur (Final)
                        @endif
                    </p>
                </div>

                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">👤 Informasi Pejabat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama</label>
                            <p class="text-gray-900 mt-1">{{ $authority->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Jabatan</label>
                            <p class="text-gray-900 mt-1">{{ $authority->position }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-900 mt-1">{{ $authority->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Status</label>
                            <div class="mt-1">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($signature->status->value === 'requested') bg-yellow-100 text-yellow-800
                                    @elseif($signature->status->value === 'uploaded') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $signature->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">📋 Informasi Pemohon</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Pemohon</label>
                            <p class="text-gray-900 mt-1">{{ $documentRequest->applicant_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tipe Pemohon</label>
                            <p class="text-gray-900 mt-1">
                                @if($documentRequest->applicant_type->value === 'mahasiswa')
                                    👨‍🎓 Mahasiswa
                                @else
                                    👨‍🏫 Internal (Dosen/Staff)
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Terdapat kesalahan pada form:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($signature->status === \App\Enums\SignatureStatus::REQUESTED)
                    <form action="{{ route('signature.upload.submit', $token) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="signature-upload-form">
                        @csrf

                        <div>
                            <label for="signature_file" class="block text-sm font-medium text-gray-700 mb-2">
                                File Tanda Tangan <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition" id="upload-area">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="signature_file" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                            <span>Upload file</span>
                                            {{-- 🔥 FIXED: Input name harus PERSIS 'signature_file' --}}
                                            <input id="signature_file"
                                                   name="signature_file"
                                                   type="file"
                                                   class="sr-only"
                                                   accept="image/png,image/jpeg,image/jpg/pdf"
                                                   required>
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG dan PDF hingga 2MB</p>
                                </div>
                            </div>

                            <div id="preview-area" class="hidden mt-4">
                                <div class="relative border-2 border-purple-300 rounded-lg p-4 bg-purple-50">
                                    <button type="button" id="remove-file" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <img id="signature-preview" src="" alt="Preview" class="max-w-full h-auto max-h-64 mx-auto">
                                    <p id="file-name" class="text-sm text-gray-600 text-center mt-2"></p>
                                    <p id="file-info" class="text-xs text-gray-500 text-center mt-1"></p>
                                </div>
                            </div>

                            @error('signature_file')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea
                                name="notes"
                                id="notes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Tambahkan catatan jika diperlukan..."
                            >{{ old('notes') }}</textarea>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">📌 Panduan Upload:</h4>
                            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                <li>Gunakan tanda tangan digital yang valid dan resmi</li>
                                <li><strong>Format file: PNG, JPG, JPEG atau PDF SAJA</strong></li>
                                <li>Ukuran maksimal: 2MB</li>
                                <li>Pastikan tanda tangan terlihat jelas</li>
                                <li>Background sebaiknya transparan atau putih</li>
                            </ul>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="submit"
                                class="flex-1 flex items-center justify-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submit-button"
                                disabled>
                                <span class="mr-2">✍️</span>
                                <span id="button-text">Upload Tanda Tangan Level {{ $signature->signature_level ?? 1 }}</span>
                            </button>
                        </div>
                    </form>
                @elseif($signature->status === \App\Enums\SignatureStatus::UPLOADED)
                    <div class="text-center py-6">
                        <div class="text-green-600 mb-4">
                            <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">✅ Tanda Tangan Sudah Diunggah</h3>
                        <p class="text-gray-600 mb-6">Tanda tangan telah berhasil diunggah pada {{ $signature->uploaded_at ? $signature->uploaded_at->format('d/m/Y H:i') : '-' }}</p>

                        @if($signature->signature_file)
                            <div class="mt-4 border-2 border-green-300 rounded-lg p-4 bg-green-50">
                                <p class="text-sm text-gray-600 mb-2">Preview Tanda Tangan Level {{ $signature->signature_level ?? 1 }}:</p>
                                <img src="{{ asset('storage/' . $signature->signature_file) }}" alt="Signature" class="max-w-full h-auto max-h-48 mx-auto">
                            </div>
                        @endif

                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                @if(($signature->signature_level ?? 1) < 3)
                                    ⏳ Menunggu Level {{ ($signature->signature_level ?? 1) + 1 }} upload tanda tangan...
                                @else
                                    🎉 Semua tanda tangan sudah lengkap! Menunggu verifikasi admin.
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="text-gray-600 mb-4">
                            <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">ℹ️ Status Tidak Valid</h3>
                        <p class="text-gray-600">Status saat ini: {{ $signature->status->label() }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                ← Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</div>

{{-- 🔥 IMPROVED JAVASCRIPT --}}
@if($signature->status === \App\Enums\SignatureStatus::REQUESTED)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('signature_file');
        const uploadArea = document.getElementById('upload-area');
        const previewArea = document.getElementById('preview-area');
        const previewImg = document.getElementById('signature-preview');
        const fileName = document.getElementById('file-name');
        const fileInfo = document.getElementById('file-info');
        const removeBtn = document.getElementById('remove-file');
        const submitBtn = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const form = document.getElementById('signature-upload-form');

        // File input change
        fileInput.addEventListener('change', function(e) {
            handleFile(e.target.files[0]);
        });

        // Drag & Drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('border-purple-500', 'bg-purple-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-purple-500', 'bg-purple-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-purple-500', 'bg-purple-50');

            const file = e.dataTransfer.files[0];
            if (file && file.type.match('image.*')) {
                fileInput.files = e.dataTransfer.files;
                handleFile(file);
            } else {
                alert('❌ File harus berupa gambar (PNG, JPG, JPEG)');
            }
        });

        // Remove file
        removeBtn.addEventListener('click', function() {
            fileInput.value = '';
            previewArea.classList.add('hidden');
            uploadArea.classList.remove('hidden');
            submitBtn.disabled = true;
        });

        // Handle file upload
        function handleFile(file) {
            if (!file) return;

            // 🔥 VALIDASI: Harus gambar
            if (!file.type.match('image.*')) {
                alert('❌ File harus berupa gambar (PNG, JPG, JPEG)\n\nFile yang Anda pilih: ' + file.type);
                fileInput.value = '';
                return;
            }

            // 🔥 VALIDASI: Cek ekstensi file
            const allowedExtensions = ['png', 'jpg', 'jpeg'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                alert('❌ Format file tidak didukung!\n\nYang diperbolehkan: PNG, JPG, JPEG\nFile Anda: .' + fileExtension.toUpperCase());
                fileInput.value = '';
                return;
            }

            // 🔥 VALIDASI: Ukuran file max 2MB
            if (file.size > 2 * 1024 * 1024) {
                alert('❌ Ukuran file terlalu besar!\n\nMaksimal: 2MB\nFile Anda: ' + formatFileSize(file.size));
                fileInput.value = '';
                return;
            }

            // 🔥 VALIDASI: Ukuran file min 1KB
            if (file.size < 1024) {
                alert('❌ Ukuran file terlalu kecil!\n\nMinimal: 1KB\nFile Anda: ' + file.size + ' bytes');
                fileInput.value = '';
                return;
            }

            // Read & preview file
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                fileName.textContent = '📄 ' + file.name;
                fileInfo.textContent = 'Ukuran: ' + formatFileSize(file.size) + ' | Tipe: ' + file.type;
                uploadArea.classList.add('hidden');
                previewArea.classList.remove('hidden');
                submitBtn.disabled = false;

                console.log('✅ File loaded:', {
                    name: file.name,
                    size: file.size,
                    type: file.type
                });
            };
            reader.readAsDataURL(file);
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        // Form submit
        form.addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('❌ Silakan pilih file tanda tangan terlebih dahulu!');
                return false;
            }

            const file = fileInput.files[0];

            // 🔥 FINAL VALIDATION sebelum submit
            if (!file.type.match('image.*')) {
                e.preventDefault();
                alert('❌ File bukan gambar yang valid!');
                return false;
            }

            if (file.size > 2 * 1024 * 1024) {
                e.preventDefault();
                alert('❌ Ukuran file melebihi 2MB!');
                return false;
            }

            if (!confirm('Apakah Anda yakin ingin mengupload tanda tangan Level {{ $signature->signature_level ?? 1 }} ini?\n\nFile: ' + file.name + '\nUkuran: ' + formatFileSize(file.size))) {
                e.preventDefault();
                return false;
            }

            // Disable button & show loading
            submitBtn.disabled = true;
            buttonText.innerHTML = '⏳ Uploading... Mohon tunggu';

            console.log('🔥 SUBMITTING FORM with file:', {
                name: file.name,
                size: file.size,
                type: file.type
            });
        });
        submitBtn.disabled = true;
    });
</script>
@endif
@endsection
