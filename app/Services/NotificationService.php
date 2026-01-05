<?php

namespace App\Services;

use App\Models\{User, DocumentRequest, AdminNotification, SignatureAuthority, DocumentVerification};
use App\Enums\{UserRole, VerificationLevel};

class NotificationService
{
    public function notifyAdminNewDocumentRequest(DocumentRequest $request): void
    {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'document_request_new',
                'title' => 'Pengajuan Dokumen Baru',
                'message' => "{$request->applicant_name} ({$request->applicant_type->label()}) mengajukan {$request->documentType->name}",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'file-text',
                'color' => 'blue',
            ]);
        }
    }

    public function getUnreadCount(User $user): int
    {
        return AdminNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    public function getRecentNotifications(User $user, int $limit = 5)
    {
        return AdminNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = AdminNotification::find($notificationId);

        if (!$notification) {
            return false;
        }

        $notification->update(['is_read' => true]);
        return true;
    }

    public function markAllAsRead(User $user): int
    {
        return AdminNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteNotification(int $notificationId): bool
    {
        $notification = AdminNotification::find($notificationId);

        if (!$notification) {
            return false;
        }

        return $notification->delete();
    }

    public function notifyUserDocumentApproved(DocumentRequest $request): void
    {
        if (!$request->user_id) {
            return;
        }

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'document_approved',
            'title' => 'Dokumen Disetujui',
            'message' => "Pengajuan {$request->documentType->name} Anda telah disetujui dan sedang diproses.",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'check-circle',
            'color' => 'green',
        ]);
    }

    public function notifyUserDocumentRejected(DocumentRequest $request): void
    {
        if (!$request->user_id) {
            return;
        }

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'document_rejected',
            'title' => 'Dokumen Ditolak',
            'message' => "Pengajuan {$request->documentType->name} Anda ditolak. Alasan: {$request->rejection_reason}",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'x-circle',
            'color' => 'red',
        ]);
    }

    public function notifyUserDocumentReady(DocumentRequest $request): void
    {
        if (!$request->user_id) {
            return;
        }

        $message = $request->isDownloadDelivery()
            ? "Dokumen {$request->documentType->name} sudah siap untuk didownload!"
            : "Dokumen {$request->documentType->name} sudah siap diambil!";

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'document_ready',
            'title' => 'Dokumen Siap',
            'message' => $message,
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'package',
            'color' => 'purple',
        ]);
    }

    public function notifyUserDocumentCompleted(DocumentRequest $request): void
    {
        if (!$request->user_id) {
            return;
        }

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'document_completed',
            'title' => 'Dokumen Selesai',
            'message' => "Proses pengajuan {$request->documentType->name} telah selesai.",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'check-square',
            'color' => 'gray',
        ]);
    }

    public function notifyApplicantDocumentApproved(DocumentRequest $request): void
    {
        if ($request->user_id) {
            return;
        }

    }

    public function notifyApplicantDocumentRejected(DocumentRequest $request, ?string $reason = null): void
    {
        if ($request->user_id) {
            return;
        }

    }

    public function notifyApplicantStatusChange(DocumentRequest $request): void
    {
        if ($request->user_id) {
            return;
        }

    }

    public function notifyAdminVerificationLevelRequested(
        DocumentRequest $request,
        DocumentVerification $verification,
        int $level
    ): void {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        $levelLabels = [
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3)',
            3 => 'Level 3 (Direktur - Final)'
        ];

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'verification_level_requested',
                'title' => "Verifikasi {$levelLabels[$level]} Diminta",
                'message' => "Dokumen {$request->request_code} ({$request->documentType->name}) dikirim ke {$verification->authority->name} untuk verifikasi {$levelLabels[$level]}",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'file-check',
                'color' => 'blue',
            ]);
        }
    }

    public function notifyAdminVerificationLevelApproved(
        DocumentRequest $request,
        DocumentVerification $verification,
        int $level
    ): void {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        $progressMap = [1 => 33, 2 => 66, 3 => 100];
        $progress = $progressMap[$level] ?? 0;

        $levelLabels = [
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3)',
            3 => 'Level 3 (Direktur - Final)'
        ];

        $nextStepMap = [
            1 => 'Lanjut ke Level 2',
            2 => 'Lanjut ke Level 3 (Final)',
            3 => 'Semua verifikasi selesai! Lanjut ke TTD'
        ];

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'verification_level_approved',
                'title' => "✅ {$levelLabels[$level]} Approved",
                'message' => "Dokumen {$request->request_code} disetujui oleh {$verification->authority->name}. Progress: {$progress}%. {$nextStepMap[$level]}",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'check-circle',
                'color' => $level === 3 ? 'green' : 'cyan',
            ]);
        }
    }

    public function notifyAdminVerificationLevelRejected(
        DocumentRequest $request,
        DocumentVerification $verification,
        int $level,
        ?string $reason = null
    ): void {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        $levelLabels = [
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3)',
            3 => 'Level 3 (Direktur - Final)'
        ];

        $reasonText = $reason ? " Alasan: {$reason}" : '';

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'verification_level_rejected',
                'title' => "❌ Verifikasi Ditolak di {$levelLabels[$level]}",
                'message' => "Dokumen {$request->request_code} ditolak oleh {$verification->authority->name}.{$reasonText} PROSES BERHENTI.",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'x-circle',
                'color' => 'red',
            ]);
        }
    }

    public function notifyUserVerificationLevelStarted(
        DocumentRequest $request,
        int $level
    ): void {
        if (!$request->user_id) {
            return;
        }

        $levelLabels = [
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3)',
            3 => 'Level 3 (Direktur - Final)'
        ];

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'verification_level_started',
            'title' => "ℹ️ Verifikasi {$levelLabels[$level]} Dimulai",
            'message' => "Dokumen {$request->documentType->name} Anda sedang dalam proses verifikasi {$levelLabels[$level]}.",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'clock',
            'color' => 'blue',
        ]);
    }

    public function notifyUserVerificationLevelApproved(
        DocumentRequest $request,
        int $level
    ): void {
        if (!$request->user_id) {
            return;
        }

        $progressMap = [1 => 33, 2 => 66, 3 => 100];
        $progress = $progressMap[$level] ?? 0;

        $levelLabels = [
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3)',
            3 => 'Level 3 (Direktur - Final)'
        ];

        $title = $level === 3
            ? '🎉 Semua Verifikasi Selesai!'
            : "✅ Verifikasi {$levelLabels[$level]} Selesai";

        $message = $level === 3
            ? "Dokumen {$request->documentType->name} Anda telah diverifikasi oleh semua pihak (100%). Dokumen akan segera ditandatangani."
            : "Verifikasi {$levelLabels[$level]} untuk dokumen {$request->documentType->name} telah disetujui. Progress: {$progress}% ({$level}/3).";

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'verification_level_approved',
            'title' => $title,
            'message' => $message,
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => $level === 3 ? 'award' : 'check-circle',
            'color' => $level === 3 ? 'green' : 'cyan',
        ]);
    }

    public function notifyUserVerificationLevelRejected(
        DocumentRequest $request,
        int $level,
        ?string $reason = null
    ): void {
        if (!$request->user_id) {
            return;
        }

        $levelLabels = [
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3)',
            3 => 'Level 3 (Direktur - Final)'
        ];

        $reasonText = $reason ? " Alasan: {$reason}" : '';

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'verification_level_rejected',
            'title' => "❌ Verifikasi Ditolak",
            'message' => "Dokumen {$request->documentType->name} Anda ditolak pada {$levelLabels[$level]}.{$reasonText}",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'x-circle',
            'color' => 'red',
        ]);
    }

    public function notifyAdminSignatureRequested(DocumentRequest $request): void
    {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'signature_requested',
                'title' => 'TTD Digital Diminta',
                'message' => "TTD digital diminta untuk dokumen {$request->request_code} ({$request->documentType->name})",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'edit-3',
                'color' => 'purple',
            ]);
        }
    }

    public function notifyAdminSignatureUploaded(
        DocumentRequest $request,
        SignatureAuthority $authority
    ): void {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'signature_uploaded',
                'title' => 'TTD Digital Diupload',
                'message' => "{$authority->name} telah mengupload TTD digital untuk dokumen {$request->request_code}",
                'document_request_id' => $request->id,
                'action_url' => route('admin.signatures.verify', $request->id),
                'icon' => 'upload',
                'color' => 'indigo',
            ]);
        }
    }

    public function notifyAdminSignatureCompleted(DocumentRequest $request): void
    {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'signature_completed',
                'title' => 'Semua TTD Digital Selesai',
                'message' => "Semua TTD digital untuk dokumen {$request->request_code} telah diverifikasi. Siap upload dokumen final.",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'award',
                'color' => 'green',
            ]);
        }
    }

    public function notifyUserSignatureInProgress(DocumentRequest $request): void
    {
        if (!$request->user_id) {
            return;
        }

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'signature_in_progress',
            'title' => 'TTD Digital Sedang Diproses',
            'message' => "Dokumen {$request->documentType->name} Anda sedang dalam proses tanda tangan digital.",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'pen-tool',
            'color' => 'indigo',
        ]);
    }

    public function notifyUserSignatureCompleted(DocumentRequest $request): void
    {
        if (!$request->user_id) {
            return;
        }

        AdminNotification::create([
            'user_id' => $request->user_id,
            'type' => 'signature_completed',
            'title' => 'TTD Digital Selesai',
            'message' => "Tanda tangan digital untuk dokumen {$request->documentType->name} Anda telah selesai. Dokumen akan segera diupload.",
            'document_request_id' => $request->id,
            'action_url' => route('internal.my-documents.show', $request->id),
            'icon' => 'check-square',
            'color' => 'cyan',
        ]);
    }

    public function notifyAdminDocumentMarkedAsTaken(
        DocumentRequest $request,
        string $markedBy
    ): void {
        $adminUsers = User::where('role', UserRole::ADMIN)->get();

        foreach ($adminUsers as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'document_marked_taken',
                'title' => 'Dokumen Ditandai Sudah Diambil',
                'message' => "Dokumen {$request->request_code} ditandai sudah diambil oleh {$markedBy}",
                'document_request_id' => $request->id,
                'action_url' => route('admin.documents.show', $request->id),
                'icon' => 'check-circle',
                'color' => 'teal',
            ]);
        }
    }

    public function notifyMultipleUsers(
        array $userIds,
        string $type,
        string $title,
        string $message,
        ?int $documentRequestId = null,
        ?string $actionUrl = null,
        string $icon = 'bell',
        string $color = 'blue'
    ): int {
        $count = 0;

        foreach ($userIds as $userId) {
            AdminNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'document_request_id' => $documentRequestId,
                'action_url' => $actionUrl,
                'icon' => $icon,
                'color' => $color,
            ]);
            $count++;
        }

        return $count;
    }

    public function getSignatureNotificationsCount(User $user): int
    {
        return AdminNotification::where('user_id', $user->id)
            ->whereIn('type', [
                'signature_requested',
                'signature_uploaded',
                'signature_completed',
            ])
            ->where('is_read', false)
            ->count();
    }

    public function getVerificationNotificationsCount(User $user): int
    {
        return AdminNotification::where('user_id', $user->id)
            ->whereIn('type', [
                'verification_level_requested',
                'verification_level_approved',
                'verification_level_rejected',
                'verification_level_started',
            ])
            ->where('is_read', false)
            ->count();
    }

    public function getPendingSignatureNotifications(User $user)
    {
        return AdminNotification::where('user_id', $user->id)
            ->whereIn('type', ['signature_requested', 'signature_uploaded'])
            ->where('is_read', false)
            ->with('documentRequest')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingVerificationNotifications(User $user)
    {
        return AdminNotification::where('user_id', $user->id)
            ->whereIn('type', ['verification_level_requested', 'verification_level_started'])
            ->where('is_read', false)
            ->with('documentRequest')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function clearOldNotifications(int $daysOld = 30): int
    {
        return AdminNotification::where('is_read', true)
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    public function getNotificationStats(User $user): array
    {
        $notifications = AdminNotification::where('user_id', $user->id);

        return [
            'total' => $notifications->count(),
            'unread' => $notifications->where('is_read', false)->count(),
            'read' => $notifications->where('is_read', true)->count(),
            'today' => $notifications->whereDate('created_at', today())->count(),
            'this_week' => $notifications->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'verification_pending' => $this->getVerificationNotificationsCount($user),
            'signature_pending' => $this->getSignatureNotificationsCount($user),
        ];
    }
}
