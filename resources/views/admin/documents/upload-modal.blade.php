<div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target === this) hideUploadModal()">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">📤 Upload Dokumen Final</h3>
            <button onclick="hideUploadModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- INSTRUKSI PENTING -->
        <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-amber-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-amber-800 mb-2">⚠️ PENTING - Baca Sebelum Upload!</h4>
                    <ul class="text-xs text-amber-700 space-y-1 list-disc list-inside">
                        <li><strong>Embed TTD Manual:</strong> Gunakan Adobe Acrobat / Foxit Reader untuk embed 3 TTD + QR Code ke PDF template</li>
                        <li><strong>Pastikan 3 TTD sudah ter-embed:</strong> Level 1 (Pa Riko), Level 2 (Pa Firman), Level 3 (Bu Rani)</li>
                        <li><strong>Format:</strong> Hanya PDF, maksimal 10MB</li>
                        <li><strong>File ini yang akan didownload user</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CHECKLIST VERIFIKASI TTD -->
        @if($documentRequest->signatures && $documentRequest->signatures->count() >= 3)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Status TTD yang Tersedia:
            </h4>
            <div class="space-y-2 text-xs">
                @foreach($documentRequest->signatures->sortBy('authority.authority_type') as $sig)
                    @if($sig->status === 'verified')
                        <div class="flex items-center gap-2 text-green-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">✓ {{ $sig->authority->name }}</span>
                            <span class="text-gray-500">({{ ucfirst(str_replace('_', ' ', $sig->authority->authority_type)) }})</span>
                            - Verified {{ $sig->verified_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                @endforeach
            </div>
            <p class="mt-3 text-xs text-green-700 font-medium">
                ✅ Semua TTD sudah terverifikasi. Silakan embed ke PDF.
            </p>
        </div>
        @endif

        <!-- LANGKAH-LANGKAH EMBED -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="text-sm font-semibold text-blue-800 mb-3">📝 Langkah Embed TTD Manual:</h4>
            <ol class="text-xs text-blue-700 space-y-2 list-decimal list-inside">
                <li>Buka template PDF kosong di Adobe Acrobat / Foxit Reader</li>
                <li>Isi data pemohon (nama, NIM, keperluan, dll) ke template</li>
                <li>Download 3 TTD + QR Code dari sistem (sudah tersimpan otomatis)</li>
                <li>Insert/embed gambar TTD Level 1, 2, 3 + QR Code ke posisi yang sesuai</li>
                <li>Save as PDF dengan nama: <code class="bg-blue-100 px-1 rounded">{{ $documentRequest->request_code }}_Final.pdf</code></li>
                <li>Upload file PDF final di form ini</li>
            </ol>
        </div>

        <form action="{{ route('admin.upload.submit', $documentRequest->id) }}"
              method="POST"
              enctype="multipart/form-data"
              id="uploadForm">
            @csrf

            <!-- FILE UPLOAD ZONE -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    File Dokumen Final (PDF) <span class="text-red-500">*</span>
                </label>

                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-blue-400 transition-colors bg-gray-50">
                    <input type="file"
                           name="document_file"
                           id="document_file"
                           accept=".pdf,application/pdf"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           onchange="handleFileSelect(event)"
                           required>

                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-base text-gray-600 mb-1">
                            <span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag & drop
                        </p>
                        <p class="text-xs text-gray-500">Format: PDF | Maksimal: 10MB</p>
                        <p class="text-xs text-amber-600 mt-2 font-medium">
                            ⚠️ Pastikan 3 TTD sudah ter-embed di file PDF
                        </p>
                    </div>
                </div>

                <!-- FILE PREVIEW -->
                <div id="filePreview" class="hidden mt-4 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-10 h-10 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 truncate" id="fileName"></p>
                            <p class="text-sm text-gray-600" id="fileSize"></p>
                            <p class="text-xs text-green-600 font-medium mt-1">✅ File siap diupload</p>
                        </div>
                        <button type="button"
                                onclick="clearFile()"
                                class="text-red-600 hover:text-red-800 p-2 hover:bg-red-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                @error('document_file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- CATATAN ADMIN -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Admin (Opsional)
                </label>
                <textarea name="notes"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Contoh: File sudah di-embed dengan 3 TTD digital sesuai prosedur..."></textarea>
                <p class="mt-1 text-xs text-gray-500">Catatan ini akan muncul di riwayat aktivitas</p>
            </div>

            <!-- FINAL CONFIRMATION -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-300 rounded-lg">
                <label class="flex items-start">
                    <input type="checkbox"
                           id="confirmCheckbox"
                           class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                           required>
                    <span class="ml-3 text-sm text-gray-700">
                        Saya menyatakan bahwa PDF yang diupload sudah mengandung <strong>3 TTD digital terverifikasi</strong>
                        (Pa Riko, Pa Firman, Bu Rani) dan <strong>siap untuk didownload oleh user</strong>.
                    </span>
                </label>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex gap-3 justify-end">
                <button type="button"
                        onclick="hideUploadModal()"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        id="uploadButton"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span id="uploadButtonText">Upload Dokumen Final</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function handleFileSelect(event) {
    const file = event.target.files[0];

    if (!file) {
        document.getElementById('filePreview').classList.add('hidden');
        return;
    }

    // Validate PDF
    if (file.type !== 'application/pdf') {
        alert('❌ File harus berformat PDF!');
        clearFile();
        return;
    }

    // Validate size (10MB)
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('❌ Ukuran file maksimal 10MB!\n\nFile Anda: ' + formatFileSize(file.size));
        clearFile();
        return;
    }

    // Show preview
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('filePreview').classList.remove('hidden');
}

function clearFile() {
    document.getElementById('document_file').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Handle form submission
document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
    const file = document.getElementById('document_file').files[0];
    const checkbox = document.getElementById('confirmCheckbox');

    if (!file) {
        e.preventDefault();
        alert('❌ Silakan pilih file PDF terlebih dahulu!');
        return;
    }

    if (!checkbox.checked) {
        e.preventDefault();
        alert('⚠️ Silakan centang konfirmasi bahwa TTD sudah ter-embed!');
        return;
    }

    // Disable button & show loading
    const button = document.getElementById('uploadButton');
    const buttonText = document.getElementById('uploadButtonText');

    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    buttonText.textContent = '⏳ Mengupload...';

    // Show progress message
    setTimeout(() => {
        buttonText.textContent = '⏳ Memproses file...';
    }, 1000);
});

function hideUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    clearFile();
    document.getElementById('uploadForm').reset();
    document.getElementById('confirmCheckbox').checked = false;

    // Reset button state
    const button = document.getElementById('uploadButton');
    const buttonText = document.getElementById('uploadButtonText');
    button.disabled = false;
    button.classList.remove('opacity-50', 'cursor-not-allowed');
    buttonText.textContent = 'Upload Dokumen Final';
}
</script>
@endpush
