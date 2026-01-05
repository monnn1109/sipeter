/**
 * SIPETER - Verification JavaScript
 * Handle form verifikasi untuk Staff Akademik
 */

document.addEventListener('DOMContentLoaded', function() {

    // ========================================
    // APPROVE CONFIRMATION
    // ========================================
    const approveButtons = document.querySelectorAll('[data-action="approve"]');
    approveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const documentCode = this.dataset.documentCode || 'dokumen ini';

            Swal.fire({
                title: 'Setujui Verifikasi?',
                html: `
                    Apakah Anda yakin ingin <strong>MENYETUJUI</strong> verifikasi untuk:<br>
                    <span class="text-blue-600 font-semibold">${documentCode}</span><br><br>
                    Dokumen akan diproses ke tahap selanjutnya.
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Setujui',
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
    // REJECT CONFIRMATION
    // ========================================
    const rejectButtons = document.querySelectorAll('[data-action="reject"]');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const reasonInput = form.querySelector('[name="rejection_reason"]');
            const documentCode = this.dataset.documentCode || 'dokumen ini';

            // Validate reason
            if (!reasonInput || !reasonInput.value.trim()) {
                Swal.fire({
                    title: 'Alasan Wajib Diisi!',
                    text: 'Mohon isi alasan penolakan terlebih dahulu',
                    icon: 'warning',
                    confirmButtonColor: '#ef4444',
                });

                if (reasonInput) {
                    reasonInput.focus();
                    reasonInput.classList.add('border-red-500');
                }
                return;
            }

            Swal.fire({
                title: 'Tolak Verifikasi?',
                html: `
                    Apakah Anda yakin ingin <strong class="text-red-600">MENOLAK</strong> verifikasi untuk:<br>
                    <span class="text-blue-600 font-semibold">${documentCode}</span><br><br>
                    <strong>Alasan:</strong><br>
                    <span class="text-gray-700">${reasonInput.value}</span><br><br>
                    Pemohon akan menerima notifikasi penolakan.
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tolak',
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
    // REJECTION REASON VALIDATION
    // ========================================
    const rejectionReasonInputs = document.querySelectorAll('[name="rejection_reason"]');
    rejectionReasonInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    });

    // ========================================
    // CHARACTER COUNTER
    // ========================================
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');

        // Create counter element
        const counter = document.createElement('div');
        counter.className = 'text-xs text-gray-500 mt-1 text-right';
        counter.textContent = `0 / ${maxLength}`;

        textarea.parentNode.appendChild(counter);

        // Update counter
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            counter.textContent = `${currentLength} / ${maxLength}`;

            if (currentLength > maxLength * 0.9) {
                counter.classList.add('text-yellow-600', 'font-semibold');
            } else {
                counter.classList.remove('text-yellow-600', 'font-semibold');
            }
        });
    });

    // ========================================
    // FORM AUTO-SAVE (Optional)
    // ========================================
    const forms = document.querySelectorAll('form[data-autosave]');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            // Load saved data
            const savedValue = localStorage.getItem(`verification_${input.name}`);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }

            // Save on change
            input.addEventListener('change', function() {
                localStorage.setItem(`verification_${this.name}`, this.value);
            });
        });

        // Clear on submit
        form.addEventListener('submit', function() {
            inputs.forEach(input => {
                localStorage.removeItem(`verification_${input.name}`);
            });
        });
    });

    // ========================================
    // PRINT VERIFICATION
    // ========================================
    const printButtons = document.querySelectorAll('[data-action="print"]');
    printButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });

    // ========================================
    // COPY DOCUMENT CODE
    // ========================================
    const copyButtons = document.querySelectorAll('[data-action="copy-code"]');
    copyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const code = this.dataset.code;

            navigator.clipboard.writeText(code).then(() => {
                // Show toast
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                toast.textContent = `Kode ${code} disalin!`;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 2000);
            });
        });
    });

    // ========================================
    // TOOLTIP INITIALIZATION
    // ========================================
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-gray-900 text-white text-xs rounded py-1 px-2 z-50';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.bottom = '100%';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            tooltip.style.marginBottom = '5px';

            this.style.position = 'relative';
            this.appendChild(tooltip);

            this._tooltip = tooltip;
        });

        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                delete this._tooltip;
            }
        });
    });

    console.log('✅ Verification.js loaded');
});

// ========================================
// GLOBAL FUNCTIONS
// ========================================

/**
 * Confirm approve action
 */
window.confirmApprove = function() {
    const form = document.getElementById('approveForm');
    const button = document.querySelector('[data-action="approve"]');

    if (button) {
        button.click();
    } else {
        // Fallback
        if (confirm('Apakah Anda yakin ingin MENYETUJUI verifikasi dokumen ini?\n\nDokumen akan diproses ke tahap selanjutnya.')) {
            form.submit();
        }
    }
}

/**
 * Confirm reject action
 */
window.confirmReject = function() {
    const form = document.getElementById('rejectForm');
    const reasonInput = document.getElementById('rejection_reason');
    const button = document.querySelector('[data-action="reject"]');

    if (!reasonInput.value.trim()) {
        alert('Alasan penolakan wajib diisi!');
        reasonInput.focus();
        return;
    }

    if (button) {
        button.click();
    } else {
        // Fallback
        if (confirm('Apakah Anda yakin ingin MENOLAK verifikasi dokumen ini?\n\nPemohon akan menerima notifikasi penolakan.')) {
            form.submit();
        }
    }
}
