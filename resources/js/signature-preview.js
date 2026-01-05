/**
 * SIPETER - Signature Preview JavaScript
 * Handle preview & verify TTD digital untuk Admin
 */

document.addEventListener('DOMContentLoaded', function() {

    // ========================================
    // IMAGE ZOOM/ENLARGE
    // ========================================
    window.enlargeImage = function(imageSrc, title = 'Preview') {
        Swal.fire({
            title: title,
            imageUrl: imageSrc,
            imageAlt: title,
            showCloseButton: true,
            showConfirmButton: false,
            width: '90%',
            padding: '2em',
            background: '#fff',
            backdrop: `
                rgba(0,0,0,0.8)
                left top
                no-repeat
            `
        });
    }

    // Make all signature images clickable
    const signatureImages = document.querySelectorAll('[data-signature-preview]');
    signatureImages.forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            const title = this.dataset.title || 'Tanda Tangan Digital';
            enlargeImage(this.src, title);
        });
    });

    // Make all QR code images clickable
    const qrImages = document.querySelectorAll('[data-qr-preview]');
    qrImages.forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            const title = this.dataset.title || 'QR Code Verifikasi';
            enlargeImage(this.src, title);
        });
    });

    // ========================================
    // DOWNLOAD FILE
    // ========================================
    window.downloadFile = function(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Show toast
        showToast('File berhasil diunduh!', 'success');
    }

    // ========================================
    // COMPARE IMAGES (Side by Side)
    // ========================================
    window.compareImages = function(img1Src, img2Src, title1 = 'Image 1', title2 = 'Image 2') {
        Swal.fire({
            title: 'Bandingkan Gambar',
            html: `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium mb-2">${title1}</p>
                        <img src="${img1Src}" class="w-full border rounded-lg shadow-sm">
                    </div>
                    <div>
                        <p class="text-sm font-medium mb-2">${title2}</p>
                        <img src="${img2Src}" class="w-full border rounded-lg shadow-sm">
                    </div>
                </div>
            `,
            width: '90%',
            showCloseButton: true,
            showConfirmButton: false,
        });
    }

    // ========================================
    // VERIFY SIGNATURE CONFIRMATION
    // ========================================
    const verifyButtons = document.querySelectorAll('[data-action="verify-signature"]');
    verifyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const documentCode = this.dataset.documentCode || '';
            const authorityName = this.dataset.authorityName || '';

            Swal.fire({
                title: 'Verifikasi Tanda Tangan?',
                html: `
                    Apakah Anda yakin tanda tangan ini <strong class="text-green-600">VALID</strong>?<br><br>
                    <div class="text-left text-sm bg-gray-50 p-4 rounded-lg">
                        <strong>Dokumen:</strong> ${documentCode}<br>
                        <strong>Pejabat:</strong> ${authorityName}
                    </div>
                    <br>
                    <small class="text-gray-600">Setelah diverifikasi, dokumen akan siap untuk difinalisasi</small>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Verifikasi',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        });
    });

    // ========================================
    // REJECT SIGNATURE CONFIRMATION
    // ========================================
    const rejectButtons = document.querySelectorAll('[data-action="reject-signature"]');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const documentCode = this.dataset.documentCode || '';

            Swal.fire({
                title: 'Tolak Tanda Tangan?',
                html: `
                    <p class="mb-4">Dokumen: <strong>${documentCode}</strong></p>
                    <textarea
                        id="rejection_reason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                        rows="4"
                        placeholder="Alasan penolakan (wajib)..."
                        required
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        Contoh: TTD tidak jelas, QR Code rusak, dll
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                preConfirm: () => {
                    const reason = document.getElementById('rejection_reason').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add rejection reason to form
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'rejection_reason';
                    reasonInput.value = result.value;
                    form.appendChild(reasonInput);

                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        });
    });

    // ========================================
    // IMAGE ROTATION
    // ========================================
    let rotation = 0;
    window.rotateImage = function(imageId) {
        rotation = (rotation + 90) % 360;
        const img = document.getElementById(imageId);
        if (img) {
            img.style.transform = `rotate(${rotation}deg)`;
            img.style.transition = 'transform 0.3s ease';
        }
    }

    // ========================================
    // PRINT PREVIEW
    // ========================================
    window.printPreview = function() {
        window.print();
    }

    // ========================================
    // COPY LINK
    // ========================================
    window.copyLink = function(url) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link berhasil disalin!', 'success');
        }).catch(err => {
            showToast('Gagal menyalin link', 'error');
        });
    }

    // ========================================
    // SHOW TOAST NOTIFICATION
    // ========================================
    window.showToast = function(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 animate-slide-in`;
        toast.innerHTML = `
            <span class="text-xl">${icons[type]}</span>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slide-out 0.3s ease-out';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // ========================================
    // IMAGE LIGHTBOX GALLERY
    // ========================================
    const galleryImages = document.querySelectorAll('[data-gallery]');
    if (galleryImages.length > 0) {
        let currentIndex = 0;
        const images = Array.from(galleryImages).map(img => ({
            src: img.src,
            title: img.dataset.title || 'Image'
        }));

        galleryImages.forEach((img, index) => {
            img.addEventListener('click', function() {
                currentIndex = index;
                showGallery();
            });
        });

        function showGallery() {
            const current = images[currentIndex];
            const total = images.length;

            Swal.fire({
                title: current.title,
                html: `
                    <div class="relative">
                        <img src="${current.src}" class="w-full rounded-lg">
                        <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                            ${currentIndex + 1} / ${total}
                        </div>
                    </div>
                    ${total > 1 ? `
                        <div class="flex justify-between mt-4">
                            <button onclick="galleryPrev()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">
                                ← Prev
                            </button>
                            <button onclick="galleryNext()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">
                                Next →
                            </button>
                        </div>
                    ` : ''}
                `,
                width: '90%',
                showCloseButton: true,
                showConfirmButton: false,
            });
        }

        window.galleryPrev = function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showGallery();
        }

        window.galleryNext = function() {
            currentIndex = (currentIndex + 1) % images.length;
            showGallery();
        }
    }

    console.log('✅ Signature-preview.js loaded');
});

// ========================================
// CSS ANIMATIONS (Add to page)
// ========================================
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slide-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
`;
document.head.appendChild(style);
