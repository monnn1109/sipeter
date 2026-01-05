/**
 * SIPETER - Signature Upload JavaScript
 * Handle upload TTD digital untuk Pejabat
 */

document.addEventListener('DOMContentLoaded', function() {

    const uploadForm = document.getElementById('uploadForm');
    const signatureInput = document.getElementById('signature_file');
    const qrInput = document.getElementById('qr_code_file');
    const maxSize = parseInt(uploadForm?.dataset.maxSize || 2048) * 1024; // Convert KB to bytes

    // ========================================
    // PREVIEW SIGNATURE FILE
    // ========================================
    window.previewSignature = function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('signature_preview');
        const img = document.getElementById('signature_img');

        if (!file) {
            preview.classList.add('hidden');
            return;
        }

        // Validate file type
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                title: 'Format File Salah!',
                text: 'Hanya file PNG, JPG, atau JPEG yang diperbolehkan',
                icon: 'error',
                confirmButtonColor: '#ef4444',
            });
            event.target.value = '';
            preview.classList.add('hidden');
            return;
        }

        // Validate file size
        if (file.size > maxSize) {
            const maxSizeKB = Math.round(maxSize / 1024);
            Swal.fire({
                title: 'File Terlalu Besar!',
                text: `Ukuran file maksimal ${maxSizeKB} KB`,
                icon: 'error',
                confirmButtonColor: '#ef4444',
            });
            event.target.value = '';
            preview.classList.add('hidden');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');

            // Add fade-in animation
            preview.style.opacity = '0';
            setTimeout(() => {
                preview.style.transition = 'opacity 0.3s';
                preview.style.opacity = '1';
            }, 10);
        }
        reader.readAsDataURL(file);
    }

    // ========================================
    // PREVIEW QR CODE FILE
    // ========================================
    window.previewQR = function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('qr_preview');
        const img = document.getElementById('qr_img');

        if (!file) {
            preview.classList.add('hidden');
            return;
        }

        // Validate file type
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                title: 'Format File Salah!',
                text: 'Hanya file PNG, JPG, atau JPEG yang diperbolehkan',
                icon: 'error',
                confirmButtonColor: '#ef4444',
            });
            event.target.value = '';
            preview.classList.add('hidden');
            return;
        }

        // Validate file size
        if (file.size > maxSize) {
            const maxSizeKB = Math.round(maxSize / 1024);
            Swal.fire({
                title: 'File Terlalu Besar!',
                text: `Ukuran file maksimal ${maxSizeKB} KB`,
                icon: 'error',
                confirmButtonColor: '#ef4444',
            });
            event.target.value = '';
            preview.classList.add('hidden');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');

            // Add fade-in animation
            preview.style.opacity = '0';
            setTimeout(() => {
                preview.style.transition = 'opacity 0.3s';
                preview.style.opacity = '1';
            }, 10);
        }
        reader.readAsDataURL(file);
    }

    // ========================================
    // FORM VALIDATION ON SUBMIT
    // ========================================
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const signatureFile = signatureInput?.files[0];
            const qrFile = qrInput?.files[0];

            // Validate both files are selected
            if (!signatureFile || !qrFile) {
                Swal.fire({
                    title: 'File Belum Lengkap!',
                    text: 'Kedua file (Tanda Tangan dan QR Code) wajib diupload',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b',
                });
                return;
            }

            // Validate file sizes
            const maxSizeKB = Math.round(maxSize / 1024);
            if (signatureFile.size > maxSize) {
                Swal.fire({
                    title: 'File TTD Terlalu Besar!',
                    text: `Ukuran file tanda tangan maksimal ${maxSizeKB} KB`,
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                });
                return;
            }

            if (qrFile.size > maxSize) {
                Swal.fire({
                    title: 'File QR Terlalu Besar!',
                    text: `Ukuran file QR Code maksimal ${maxSizeKB} KB`,
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                });
                return;
            }

            // Show confirmation
            Swal.fire({
                title: 'Konfirmasi Upload',
                html: `
                    Apakah Anda yakin file yang diupload sudah benar?<br><br>
                    <div class="text-left text-sm">
                        <strong>Tanda Tangan:</strong> ${signatureFile.name} (${formatFileSize(signatureFile.size)})<br>
                        <strong>QR Code:</strong> ${qrFile.name} (${formatFileSize(qrFile.size)})
                    </div>
                    <br>
                    <small class="text-gray-600">Tanda tangan dan QR Code akan diverifikasi oleh Admin TU</small>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Upload',
                cancelButtonText: 'Cek Lagi',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show upload progress
                    Swal.fire({
                        title: 'Mengupload...',
                        html: `
                            <div class="mb-4">Mohon tunggu, sedang mengupload file...</div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="uploadProgress" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                            <div id="uploadPercent" class="text-sm text-gray-600 mt-2">0%</div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            // Simulate upload progress
                            let progress = 0;
                            const interval = setInterval(() => {
                                progress += Math.random() * 15;
                                if (progress > 90) progress = 90;

                                document.getElementById('uploadProgress').style.width = progress + '%';
                                document.getElementById('uploadPercent').textContent = Math.round(progress) + '%';
                            }, 200);

                            // Store interval ID to clear later
                            uploadForm._progressInterval = interval;
                        }
                    });

                    // Submit form
                    uploadForm.submit();
                }
            });
        });
    }

    // ========================================
    // DRAG & DROP HANDLERS
    // ========================================
    const dropZones = document.querySelectorAll('.border-dashed');

    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');

            const input = this.querySelector('input[type="file"]');
            if (input && e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // ========================================
    // FILE SIZE FORMATTER
    // ========================================
    window.formatFileSize = function(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // ========================================
    // IMAGE QUALITY CHECK
    // ========================================
    function checkImageQuality(file, callback) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const quality = {
                    width: this.width,
                    height: this.height,
                    aspectRatio: this.width / this.height,
                    isGood: this.width >= 200 && this.height >= 100
                };
                callback(quality);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }

    // ========================================
    // CLEAR PREVIEW BUTTONS
    // ========================================
    const clearButtons = document.querySelectorAll('[data-action="clear-preview"]');
    clearButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const previewId = this.dataset.target;
            const preview = document.getElementById(previewId);
            const input = this.closest('.border-dashed').querySelector('input[type="file"]');

            if (preview) preview.classList.add('hidden');
            if (input) input.value = '';
        });
    });

    console.log('✅ Signature-upload.js loaded');
});

// ========================================
// HELPER: Format file size for display
// ========================================
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
