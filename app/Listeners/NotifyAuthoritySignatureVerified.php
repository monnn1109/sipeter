<?php

namespace App\Listeners;

use App\Events\SignatureVerified;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyAuthoritySignatureVerified implements ShouldQueue
{
    public function __construct(
        protected WhatsAppService $whatsappService
    ) {}

    public function handle(SignatureVerified $event): void
    {
        try {
            // Send WhatsApp notification to authority (confirmation)
            $this->whatsappService->notifySignatureVerified(
                $event->documentRequest,
                $event->authority
            );

            Log::info('Authority notified about signature verification', [
                'document_request_id' => $event->documentRequest->id,
                'authority_id' => $event->authority->id,
                'authority_name' => $event->authority->name,
                'verified_by' => $event->verifiedBy->name,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify authority about signature verification', [
                'document_request_id' => $event->documentRequest->id,
                'authority_id' => $event->authority->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(SignatureVerified $event, \Throwable $exception): void
    {
        Log::error('Authority notification job failed for signature verification', [
            'document_request_id' => $event->documentRequest->id,
            'authority_id' => $event->authority->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
