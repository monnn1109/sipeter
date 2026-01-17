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
            $currentLevel = $event->documentRequest->current_verification_step ?? 1;

            \Log::info('User notification: Verification requested', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $currentLevel,
                'has_user' => $event->documentRequest->user_id ? 'yes' : 'no',
            ]);
        }

        if ($event instanceof DocumentVerificationApproved) {
            $level = $event->authority->getVerificationLevel();

            \Log::info('User notification: Verification approved', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $level,
                'progress' => $this->getProgressPercentage($level),
                'has_user' => $event->documentRequest->user_id ? 'yes' : 'no',
            ]);
        }

        if ($event instanceof DocumentVerificationRejected) {
            $level = $event->verification->verification_level;

            \Log::info('User notification: Verification rejected', [
                'document_code' => $event->documentRequest->request_code,
                'level' => $level,
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
