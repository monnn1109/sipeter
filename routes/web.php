<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Guest\{
    HomeController,
    MahasiswaDocumentController,
    DocumentDownloadController as GuestDownloadController,
    VerificationController,
    SignatureUploadController,
    TrackingController
};
use App\Http\Controllers\Internal\{
    DashboardController as InternalDashboardController,
    DocumentRequestController,
    MyDocumentController,
    DocumentDownloadController as InternalDownloadController,
    ProfileController
};
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    DocumentManagementController,
    DocumentUploadController,
    DocumentHistoryController,
    UserManagementController,
    NotificationController,
    DocumentVerificationController,
    SignatureManagementController,
    SignatureVerificationController
};

// ========================================
// GUEST ROUTES
// ========================================

Route::get('/', [HomeController::class, 'index'])->name('home');

// MAHASISWA ROUTES
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/', [MahasiswaDocumentController::class, 'form'])->name('form');
    Route::post('/submit', [MahasiswaDocumentController::class, 'submit'])->name('submit');
    Route::get('/success/{id}', [MahasiswaDocumentController::class, 'success'])->name('success');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/check', [TrackingController::class, 'check'])->name('tracking.check');
    Route::get('/tracking/{code}', [TrackingController::class, 'show'])->name('tracking.detail');
});

// Guest Document Download (untuk DOWNLOAD ONLINE)
Route::prefix('guest/documents')->name('guest.documents.')->group(function () {
    Route::get('/{id}/download', [GuestDownloadController::class, 'download'])->name('download');
    Route::get('/{id}/preview', [GuestDownloadController::class, 'preview'])->name('preview');
    Route::post('/{id}/mark-as-taken', [GuestDownloadController::class, 'markAsTaken'])->name('mark-as-taken');
    Route::get('/{id}/check-readiness', [GuestDownloadController::class, 'checkReadiness'])->name('check-readiness');
});

// Verification Routes
Route::prefix('verification')->name('verification.')->group(function () {
    Route::get('/{token}', [VerificationController::class, 'show'])->name('show');
    Route::post('/{token}/submit', [VerificationController::class, 'submit'])->name('submit');
    Route::post('/{token}/approve', [VerificationController::class, 'approve'])->name('approve');
    Route::post('/{token}/reject', [VerificationController::class, 'reject'])->name('reject');
    Route::get('/error/{message?}', [VerificationController::class, 'error'])->name('error');
});

// Signature Upload Routes (Guest - Pejabat)
Route::prefix('signature')->name('signature.')->group(function () {
    Route::get('/upload/{token}', [SignatureUploadController::class, 'show'])->name('upload.show');
    Route::get('/success/{signatureId}', [SignatureUploadController::class, 'success'])->name('upload.success');
    Route::post('/upload/{token}', [SignatureUploadController::class, 'upload'])->name('upload.submit');
    Route::get('/error/{message?}', [SignatureUploadController::class, 'error'])->name('error');
});

// ========================================
// AUTH ROUTES
// ========================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ========================================
// INTERNAL USER ROUTES
// ========================================

Route::middleware(['auth', 'internal'])->prefix('internal')->name('internal.')->group(function () {

    Route::get('/dashboard', [InternalDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentRequestController::class, 'index'])->name('index');
        Route::get('/create', [DocumentRequestController::class, 'create'])->name('create');
        Route::post('/store', [DocumentRequestController::class, 'store'])->name('store');
    });

    Route::prefix('my-documents')->name('my-documents.')->group(function () {
        Route::get('/', [MyDocumentController::class, 'index'])->name('index');
        Route::get('/{id}', [MyDocumentController::class, 'show'])->name('show');
        Route::get('/{id}/download', [InternalDownloadController::class, 'download'])->name('download');
        Route::get('/{id}/preview', [InternalDownloadController::class, 'preview'])->name('preview');
        Route::post('/{id}/mark-as-taken', [MyDocumentController::class, 'markAsTaken'])->name('mark-as-taken');
        Route::get('/{id}/check-download', [MyDocumentController::class, 'checkDownload'])->name('check-download');
        Route::get('/stats/user', [MyDocumentController::class, 'getStats'])->name('stats');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });
});

// ========================================
// ADMIN ROUTES
// ========================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/quick-actions', [AdminDashboardController::class, 'getQuickActions'])->name('dashboard.quick-actions');

    // Documents Management
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentManagementController::class, 'index'])->name('index');
        Route::get('/pending', [DocumentManagementController::class, 'pending'])->name('pending');
        Route::get('/{id}', [DocumentManagementController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [DocumentManagementController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [DocumentManagementController::class, 'reject'])->name('reject');
        Route::post('/{id}/status', [DocumentManagementController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/note', [DocumentManagementController::class, 'addNote'])->name('add-note');
        Route::get('/{id}/download', [DocumentManagementController::class, 'download'])->name('download');
        Route::get('/{id}/send-signature', [DocumentManagementController::class, 'showSendSignatureForm'])->name('send-signature.form');

        // 🔥 PICKUP FISIK ROUTES - DocumentManagementController (BUKAN DocumentUploadController)
        // Step 1: Tandai "Siap Diambil" (setelah semua TTD verified, embed manual selesai)
        Route::post('/{id}/mark-ready-pickup', [DocumentManagementController::class, 'markAsReadyForPickup'])
            ->name('mark-ready-pickup');

        // Step 2: Tandai "Sudah Diambil" (setelah user datang dan ambil fisik)
        Route::post('/{id}/mark-picked-up', [DocumentManagementController::class, 'markAsPickedUp'])
            ->name('mark-picked-up');

        Route::get('/stats/all', [DocumentManagementController::class, 'getStats'])->name('stats');
    });

    // Verifications Management
    Route::prefix('verifications')->name('verifications.')->group(function () {
        Route::get('/', [DocumentVerificationController::class, 'index'])->name('index');
        Route::get('/pending', [DocumentVerificationController::class, 'pending'])->name('pending');
        Route::get('/approved', [DocumentVerificationController::class, 'approved'])->name('approved');
        Route::get('/rejected', [DocumentVerificationController::class, 'rejected'])->name('rejected');

        Route::get('/{documentId}/send', [DocumentVerificationController::class, 'showSendForm'])->name('send.form');

        Route::post('/{documentId}/send', [DocumentVerificationController::class, 'sendVerification'])->name('send');
        Route::post('/{documentId}/resend', [DocumentVerificationController::class, 'resendVerification'])->name('resend');
        Route::post('/{documentId}/cancel', [DocumentVerificationController::class, 'cancelVerification'])->name('cancel');

        Route::get('/{verificationId}/show', [DocumentVerificationController::class, 'show'])->name('show');

        Route::get('/stats', [DocumentVerificationController::class, 'stats'])->name('stats');
    });

    // Signatures Management
    Route::prefix('signatures')->name('signatures.')->group(function () {

        Route::get('/', [SignatureManagementController::class, 'index'])->name('index');
        Route::get('/pending', [SignatureManagementController::class, 'pending'])->name('pending');
        Route::get('/history', [SignatureManagementController::class, 'history'])->name('history');

        Route::get('/verify', [SignatureVerificationController::class, 'index'])->name('verify.index');
        Route::get('/{id}/verify-form', [SignatureVerificationController::class, 'verifyForm'])->name('verify-form');
        Route::get('/{id}/verify', [SignatureVerificationController::class, 'verifyForm'])->name('verify');
        Route::post('/{id}/verify/process', [SignatureVerificationController::class, 'approve'])->name('verify.process');
        Route::post('/{id}/approve', [SignatureVerificationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [SignatureVerificationController::class, 'reject'])->name('reject');

        Route::get('/authorities', [SignatureManagementController::class, 'authorities'])->name('authorities');
        Route::get('/authorities/create', [SignatureManagementController::class, 'createAuthority'])->name('authorities.create');
        Route::post('/authorities/store', [SignatureManagementController::class, 'storeAuthority'])->name('authorities.store');
        Route::get('/authorities/{id}/edit', [SignatureManagementController::class, 'editAuthority'])->name('authorities.edit');
        Route::put('/authorities/{id}', [SignatureManagementController::class, 'updateAuthority'])->name('authorities.update');
        Route::post('/authorities/{id}/toggle-status', [SignatureManagementController::class, 'toggleStatus'])->name('authorities.toggle-status');
        Route::delete('/authorities/{id}', [SignatureManagementController::class, 'deleteAuthority'])->name('authorities.delete');

        Route::post('/{documentId}/request', [SignatureManagementController::class, 'requestSignature'])->name('request');
        Route::post('/{id}/resend', [SignatureManagementController::class, 'resendSignatureRequest'])->name('resend');
        Route::post('/{documentId}/remind', [SignatureManagementController::class, 'sendReminder'])->name('remind');

        Route::get('/{id}/view', [SignatureManagementController::class, 'viewSignature'])->name('view');
        Route::get('/{id}/details', [SignatureManagementController::class, 'details'])->name('details');
        Route::get('/{id}/download', [SignatureVerificationController::class, 'downloadSignature'])->name('download');
        Route::get('/{id}/preview', [SignatureVerificationController::class, 'preview'])->name('preview');

        Route::get('/history/export', [SignatureManagementController::class, 'exportHistory'])->name('history.export');
        Route::get('/history/pdf', [SignatureManagementController::class, 'exportHistoryPdf'])->name('history.pdf');

        Route::get('/stats', [SignatureManagementController::class, 'getStats'])->name('stats');
    });

    // Document Upload - DocumentUploadController
    Route::prefix('upload')->name('upload.')->group(function () {
        Route::get('/{id}', [DocumentUploadController::class, 'show'])->name('show');
        Route::post('/{id}', [DocumentUploadController::class, 'upload'])->name('submit');

        // 📥 DOWNLOAD ONLINE: Upload dokumen final (sudah ter-embed 3 TTD)
        Route::post('/{id}/final', [DocumentUploadController::class, 'submitFinalDocument'])->name('submit-final');

        Route::delete('/{id}', [DocumentUploadController::class, 'delete'])->name('delete');
    });

    // Document History
    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/', [DocumentHistoryController::class, 'index'])->name('index');
        Route::get('/{id}', [DocumentHistoryController::class, 'show'])->name('show');
        Route::get('/activity/{activityId}', [DocumentHistoryController::class, 'showActivity'])->name('activity');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
    });
});

// ========================================
// 404 FALLBACK
// ========================================

Route::fallback(function () {
    return view('errors.404');
});
