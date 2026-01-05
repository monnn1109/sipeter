<?php

namespace App\Listeners;

use App\Events\{
    DocumentRequestApproved,
    DocumentRequestRejected,
    DocumentReadyForPickup,
    DocumentVerificationRequested,
    DocumentVerificationApproved,
    DocumentVerificationRejected
};
use App\Services\NotificationService;

class SendUserNotification
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handle($event): void
    {
        if ($event instanceof DocumentRequestApproved) {
            $this->notificationService->notifyUserDocumentApproved($event->documentRequest);
        }

        if ($event instanceof DocumentRequestRejected) {
            $this->notificationService->notifyUserDocumentRejected($event->documentRequest);
        }

        if ($event instanceof DocumentReadyForPickup) {
            $this->notificationService->notifyUserDocumentReady($event->documentRequest);
        }
        if ($event instanceof DocumentVerificationRequested) {
            \Log::info('User notification: Verification requested', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $event->verification->verification_level,
                'has_user' => $event->documentRequest->user_id ? 'yes' : 'no',
            ]);
        }

        if ($event instanceof DocumentVerificationApproved) {
            \Log::info('User notification: Verification approved', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $event->verification->verification_level,
                'progress' => $this->getProgressPercentage($event->verification->verification_level),
                'has_user' => $event->documentRequest->user_id ? 'yes' : 'no',
            ]);
        }

        if ($event instanceof DocumentVerificationRejected) {
            \Log::info('User notification: Verification rejected', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $event->verification->verification_level,
                'has_user' => $event->documentRequest->user_id ? 'yes' : 'no',
            ]);
        }
    }

    public function __invoke($event): void
    {
        $this->handle($event);
    }
    private function getProgressPercentage(int $level): string
    {
        $progressMap = [1 => '33%', 2 => '66%', 3 => '100%'];
        return $progressMap[$level] ?? '0%';
    }
}
