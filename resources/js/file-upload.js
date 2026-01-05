const MAX_FILE_SIZE = 5 * 1024 * 1024;

const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx'];

function validateFile(file) {
    if (!file) {
        return { valid: false, error: 'Tidak ada file yang dipilih' };
    }

    if (file.size > MAX_FILE_SIZE) {
        const maxSizeMB = MAX_FILE_SIZE / (1024 * 1024);
        return { valid: false, error: `Ukuran file terlalu besar. Maksimal ${maxSizeMB}MB` };
    }

    const extension = file.name.split('.').pop().toLowerCase();
    if (!ALLOWED_EXTENSIONS.includes(extension)) {
        return { valid: false, error: `Format file tidak didukung. Gunakan: ${ALLOWED_EXTENSIONS.join(', ')}` };
    }

    return { valid: true, error: null };
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();

    const icons = {
        'pdf': '📕',
        'doc': '📘',
        'docx': '📘'
    };

    return icons[extension] || '📄';
}

async function checkSignatureCompletion(documentId) {
    if (!documentId) {
        return { completed: true, message: null };
    }

    try {
        const response = await fetch(`/admin/documents/${documentId}/signature-status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to check signature status');
        }

        const data = await response.json();
        return data;

    } catch (error) {
        console.error('Error checking signature:', error);
        return { completed: false, message: 'Gagal memeriksa status tanda tangan' };
    }
}

function showSignatureWarning(message) {
    const warningDiv = document.getElementById('signature-warning') || createWarningDiv();

    warningDiv.innerHTML = `
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">
                        ⚠️ Upload Belum Tersedia
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    warningDiv.classList.remove('hidden');
}

function createWarningDiv() {
    const warningDiv = document.createElement('div');
    warningDiv.id = 'signature-warning';
    warningDiv.className = 'mb-4';

    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.insertBefore(warningDiv, uploadForm.firstChild);
    }

    return warningDiv;
}

function hideSignatureWarning() {
    const warningDiv = document.getElementById('signature-warning');
    if (warningDiv) {
        warningDiv.classList.add('hidden');
    }
}

function disableUploadButton(message) {
    const uploadButton = document.getElementById('upload-button');
    const fileInput = document.getElementById('file-upload');

    if (uploadButton) {
        uploadButton.disabled = true;
        uploadButton.classList.add('opacity-50', 'cursor-not-allowed');
        uploadButton.title = message;
    }

    if (fileInput) {
        fileInput.disabled = true;
    }
}

function enableUploadButton() {
    const uploadButton = document.getElementById('upload-button');
    const fileInput = document.getElementById('file-upload');

    if (uploadButton) {
        uploadButton.disabled = false;
        uploadButton.classList.remove('opacity-50', 'cursor-not-allowed');
        uploadButton.title = '';
    }

    if (fileInput) {
        fileInput.disabled = false;
    }
}

function initializeFileUpload(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const dropZone = input.closest('.border-dashed');
    if (!dropZone) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        }, false);
    });

    dropZone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            input.files = files;
            handleFileSelect(input);
        }
    }, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}


function handleFileSelect(input) {
    const file = input.files[0];

    if (!file) return;

    const validation = validateFile(file);

    if (!validation.valid) {
        alert(validation.error);
        input.value = '';
        return;
    }

    displayFileInfo(file);
}

function displayFileInfo(file) {
    const fileNameDiv = document.getElementById('file-name');
    if (!fileNameDiv) return;

    const icon = getFileIcon(file.name);
    const size = formatFileSize(file.size);

    fileNameDiv.innerHTML = `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-2">
                <span class="text-2xl">${icon}</span>
                <div>
                    <p class="font-medium text-gray-900">${file.name}</p>
                    <p class="text-xs text-gray-500">${size}</p>
                </div>
            </div>
            <button type="button" onclick="clearFileSelection()" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    fileNameDiv.classList.remove('hidden');
}

function clearFileSelection() {
    const input = document.getElementById('file-upload');
    if (input) {
        input.value = '';
    }

    const fileNameDiv = document.getElementById('file-name');
    if (fileNameDiv) {
        fileNameDiv.classList.add('hidden');
        fileNameDiv.innerHTML = '';
    }
}

function showUploadProgress(percent) {
    const progressBar = document.getElementById('upload-progress');
    if (!progressBar) return;

    progressBar.style.width = percent + '%';
    progressBar.textContent = Math.round(percent) + '%';
}

async function validateFormSubmission(e) {
    const form = e.target;
    const documentId = form.dataset.documentId;

    if (!documentId) {
        return true;
    }

    const signatureStatus = await checkSignatureCompletion(documentId);

    if (!signatureStatus.completed) {
        e.preventDefault();
        showSignatureWarning(
            signatureStatus.message ||
            'Dokumen ini memerlukan tanda tangan yang belum lengkap. Pastikan semua tanda tangan telah diverifikasi sebelum upload dokumen final.'
        );
        return false;
    }

    return true;
}


async function initializeSignatureCheck() {
    const uploadForm = document.getElementById('upload-form');
    if (!uploadForm) return;

    const documentId = uploadForm.dataset.documentId;
    if (!documentId) return;

    const signatureStatus = await checkSignatureCompletion(documentId);

    if (!signatureStatus.completed) {
        disableUploadButton(signatureStatus.message || 'Menunggu tanda tangan');
        showSignatureWarning(
            signatureStatus.message ||
            'Upload dokumen final belum tersedia. Mohon tunggu hingga semua tanda tangan selesai diverifikasi.'
        );
    } else {
        enableUploadButton();
        hideSignatureWarning();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initializeFileUpload('file-upload');
    initializeSignatureCheck();

    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', validateFormSubmission);
    }
});
