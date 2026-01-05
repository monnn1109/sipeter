<?php

namespace App\Listeners;

use App\Events\DocumentVerificationRequested;
use App\Services\{WhatsAppService, NotificationService};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendVerificationRequestNotification implements ShouldQueue
{
    public function __construct(
        private WhatsAppService $whatsappService,
        private NotificationService $notificationService
    ) {}

    public function handle(DocumentVerificationRequested $event): void
    {
        $document = $event->documentRequest;
        $authority = $event->authority;
        $link = $event->verificationLink;

        // Get current level from document
        $currentLevel = $document->current_verification_step;

        try {
            // Send WA to authority
            $authoritySuccess = $this->whatsappService->sendVerificationRequestByLevel(
                $document,
                $authority,
                $link,
                $currentLevel
            );

            if ($authoritySuccess) {
                Log::info('Verification request WA sent to authority', [
                    'level' => $currentLevel,
                    'authority' => $authority->name,
                    'phone' => $authority->phone,
                    'document_code' => $document->request_code,
                ]);
            }

            // Send WA to user
            $userSuccess = $this->whatsappService->sendVerificationInProgressByLevel(
                $document,
                $authority,
                $currentLevel
            );

            if ($userSuccess) {
                Log::info('Verification progress WA sent to user', [
                    'level' => $currentLevel,
                    'phone' => $document->applicant_phone,
                    'document_code' => $document->request_code,
                ]);
            }

            // Get verification record for admin notification
            $verification = $document->verifications()
                ->where('verification_level', $currentLevel)
                ->where('status', 'pending')
                ->first();

            if ($verification) {
                // Notify admin
                $this->notificationService->notifyAdminVerificationLevelRequested(
                    $document,
                    $verification,
                    $currentLevel
                );
            }

            // Notify user (in-app notification)
            if ($document->user_id) {
                $this->notificationService->notifyUserVerificationLevelStarted(
                    $document,
                    $currentLevel
                );
            }

        } catch (\Exception $e) {
            Log::error('Exception in SendVerificationRequestNotification', [
                'level' => $currentLevel,
                'document_code' => $document->request_code,
                'authority' => $authority->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function failed(DocumentVerificationRequested $event, \Throwable $exception): void
    {
        Log::error('SendVerificationRequestNotification job failed', [
            'document_code' => $event->documentRequest->request_code,
            'authority' => $event->authority->name,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
