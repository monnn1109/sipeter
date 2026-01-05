<?php

namespace App\Listeners;

use App\Events\SignatureUploaded;
use App\Services\{WhatsAppService, NotificationService};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminSignatureUploaded implements ShouldQueue
{
    public function __construct(
        protected WhatsAppService $whatsappService,
        protected NotificationService $notificationService
    ) {}

    public function handle(SignatureUploaded $event): void
    {
        try {
            // Send WhatsApp notification to admins
            $this->whatsappService->notifyAdminSignatureUploaded(
                $event->documentRequest,
                $event->authority
            );

            // Send in-app notification to admins
            $this->notificationService->notifyAdminSignatureUploaded(
                $event->documentRequest,
                $event->authority
            );

            Log::info('Admin notified about signature upload', [
                'document_request_id' => $event->documentRequest->id,
                'authority_id' => $event->authority->id,
                'authority_name' => $event->authority->name,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify admin about signature upload', [
                'document_request_id' => $event->documentRequest->id,
                'authority_id' => $event->authority->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(SignatureUploaded $event, \Throwable $exception): void
    {
        Log::error('Admin notification job failed for signature upload', [
            'document_request_id' => $event->documentRequest->id,
            'authority_id' => $event->authority->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
