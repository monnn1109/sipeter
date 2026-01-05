<?php

namespace App\Listeners;

use App\Events\{
    DocumentRequestSubmitted,
    DocumentDownloaded,
    DocumentCompleted,
    DocumentVerificationRequested,
    DocumentVerificationApproved,
    DocumentVerificationRejected
};
use App\Services\NotificationService;
use App\Models\{User, AdminNotification};

class SendAdminNotification
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handle($event): void
    {
        if ($event instanceof DocumentRequestSubmitted) {
            $this->notificationService->notifyAdminNewDocumentRequest($event->documentRequest);
        }

        if ($event instanceof DocumentCompleted) {
            $this->notifyAdminDocumentCompleted($event);
        }

        if ($event instanceof DocumentVerificationRequested) {
            \Log::info('Admin notification: Verification requested', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $event->verification->verification_level,
            ]);
        }

        if ($event instanceof DocumentVerificationApproved) {
            \Log::info('Admin notification: Verification approved', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $event->verification->verification_level,
            ]);
        }

        if ($event instanceof DocumentVerificationRejected) {
            \Log::info('Admin notification: Verification rejected', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $event->verification->verification_level,
            ]);
        }
    }

    public function __invoke($event): void
    {
        $this->handle($event);
    }

    protected function notifyAdminDocumentCompleted($event): void
    {
        $documentRequest = $event->documentRequest;
        $admins = User::where('role', 'admin')->get();

        $applicantInfo = $documentRequest->isGuestRequest()
            ? "{$documentRequest->applicant_name} (Mahasiswa)"
            : "{$documentRequest->user->name} ({$documentRequest->user->role->label()})";

        foreach ($admins as $admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'document_completed',
                'title' => '✅ Dokumen Selesai Diambil',
                'message' => "{$applicantInfo} telah mengkonfirmasi pengambilan dokumen {$documentRequest->documentType->name} (Kode: {$documentRequest->request_code})",
                'document_request_id' => $documentRequest->id,
                'action_url' => route('admin.documents.show', $documentRequest->id),
                'icon' => 'check-circle',
                'color' => 'green',
            ]);
        }
    }
}
