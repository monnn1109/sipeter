<?php

namespace App\Listeners;

use App\Events\{
    DocumentRequestSubmitted,
    DocumentRequestApproved,
    DocumentRequestRejected,
    DocumentReadyForPickup,
    DocumentPickedUp,
    DocumentCompleted,
    DocumentUploaded,
    DocumentDownloaded,
    DocumentVerificationRequested,
    DocumentVerificationApproved,
    DocumentVerificationRejected
};
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Exception;

class SendWhatsAppNotification
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function handleDocumentRequestSubmitted(DocumentRequestSubmitted $event): void
    {
        try {
            $this->whatsAppService->notifyDocumentSubmitted($event->documentRequest);
            $this->notifyAdminNewRequest($event->documentRequest);

            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document submitted notification',
                'sent',
                $event->documentRequest->id
            );

            Log::info('WhatsApp: Document submitted notification sent', [
                'request_code' => $event->documentRequest->request_code,
                'phone' => $event->documentRequest->applicant_phone
            ]);
        } catch (Exception $e) {
            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document submitted notification',
                'failed',
                $event->documentRequest->id,
                $e->getMessage()
            );

            Log::error('WhatsApp: Failed to send submitted notification', [
                'request_code' => $event->documentRequest->request_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function notifyAdminNewRequest($documentRequest): void
    {
        try {
            $this->whatsAppService->notifyAdminNewRequest($documentRequest);

            Log::info('Admin notified about new request', [
                'request_code' => $documentRequest->request_code
            ]);
        } catch (Exception $e) {
            Log::error('Failed to notify admin', [
                'request_code' => $documentRequest->request_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentRequestApproved(DocumentRequestApproved $event): void
    {
        try {
            $this->whatsAppService->notifyDocumentApproved($event->documentRequest);

            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document approved notification',
                'sent',
                $event->documentRequest->id
            );

            Log::info('WhatsApp: Document approved notification sent', [
                'request_code' => $event->documentRequest->request_code,
                'phone' => $event->documentRequest->applicant_phone
            ]);
        } catch (Exception $e) {
            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document approved notification',
                'failed',
                $event->documentRequest->id,
                $e->getMessage()
            );

            Log::error('WhatsApp: Failed to send approved notification', [
                'request_code' => $event->documentRequest->request_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentRequestRejected(DocumentRequestRejected $event): void
    {
        try {
            $this->whatsAppService->notifyDocumentRejected($event->documentRequest);

            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document rejected notification',
                'sent',
                $event->documentRequest->id
            );

            Log::info('WhatsApp: Document rejected notification sent', [
                'request_code' => $event->documentRequest->request_code,
                'phone' => $event->documentRequest->applicant_phone,
                'reason' => $event->documentRequest->rejection_reason
            ]);
        } catch (Exception $e) {
            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document rejected notification',
                'failed',
                $event->documentRequest->id,
                $e->getMessage()
            );

            Log::error('WhatsApp: Failed to send rejected notification', [
                'request_code' => $event->documentRequest->request_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentVerificationRequested(DocumentVerificationRequested $event): void
    {
        try {
            $level = $event->verification->verification_level;

            Log::info('WhatsApp: Verification requested (handled by dedicated listener)', [
                'request_code' => $event->documentRequest->request_code,
                'level' => $level,
                'authority' => $event->verification->authority->name,
            ]);
        } catch (Exception $e) {
            Log::error('WhatsApp: Verification requested logging failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentVerificationApproved(DocumentVerificationApproved $event): void
    {
        try {
            $level = $event->verification->verification_level;
            $progressMap = [1 => '33%', 2 => '66%', 3 => '100%'];

            Log::info('WhatsApp: Verification approved (handled by dedicated listener)', [
                'request_code' => $event->documentRequest->request_code,
                'level' => $level,
                'progress' => $progressMap[$level] ?? '0%',
                'authority' => $event->verification->authority->name,
            ]);
        } catch (Exception $e) {
            Log::error('WhatsApp: Verification approved logging failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentVerificationRejected(DocumentVerificationRejected $event): void
    {
        try {
            $level = $event->verification->verification_level;

            Log::info('WhatsApp: Verification rejected (handled by dedicated listener)', [
                'request_code' => $event->documentRequest->request_code,
                'level' => $level,
                'authority' => $event->verification->authority->name,
                'reason' => $event->reason,
            ]);
        } catch (Exception $e) {
            Log::error('WhatsApp: Verification rejected logging failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentReadyForPickup(DocumentReadyForPickup $event): void
    {
        $request = $event->documentRequest;

        if ($request->delivery_method === 'pickup') {
            try {
                $this->whatsAppService->notifyDocumentReady($request);

                $this->logNotification(
                    $request->applicant_phone,
                    'Document ready for pickup',
                    'sent',
                    $request->id
                );

                Log::info('WhatsApp: Document ready for pickup notification sent', [
                    'request_code' => $request->request_code,
                    'phone' => $request->applicant_phone,
                    'delivery_method' => 'pickup'
                ]);
            } catch (Exception $e) {
                $this->logNotification(
                    $request->applicant_phone,
                    'Document ready for pickup',
                    'failed',
                    $request->id,
                    $e->getMessage()
                );

                Log::error('WhatsApp: Failed to send ready notification', [
                    'request_code' => $request->request_code,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function handleDocumentUploaded(DocumentUploaded $event): void
    {
        $request = $event->documentRequest;

        if ($request->delivery_method === 'download') {
            try {
                $this->whatsAppService->notifyDocumentReadyForDownload($request);

                $this->logNotification(
                    $request->applicant_phone,
                    'Document ready for download',
                    'sent',
                    $request->id
                );

                Log::info('WhatsApp: Document ready for download notification sent', [
                    'request_code' => $request->request_code,
                    'phone' => $request->applicant_phone,
                    'delivery_method' => 'download',
                    'file_name' => $request->file_name
                ]);
            } catch (Exception $e) {
                $this->logNotification(
                    $request->applicant_phone,
                    'Document ready for download',
                    'failed',
                    $request->id,
                    $e->getMessage()
                );

                Log::error('WhatsApp: Failed to send upload notification', [
                    'request_code' => $request->request_code,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function handleDocumentDownloaded(DocumentDownloaded $event): void
    {
        Log::info('WhatsApp: Document downloaded by user', [
            'request_code' => $event->documentRequest->request_code,
            'downloaded_at' => now()->toDateTimeString()
        ]);
    }

    public function handleDocumentPickedUp(DocumentPickedUp $event): void
    {
        try {
            $this->whatsAppService->notifyDocumentCompleted($event->documentRequest);

            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document picked up',
                'sent',
                $event->documentRequest->id
            );

            Log::info('WhatsApp: Document picked up notification sent', [
                'request_code' => $event->documentRequest->request_code,
                'phone' => $event->documentRequest->applicant_phone
            ]);
        } catch (Exception $e) {
            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document picked up',
                'failed',
                $event->documentRequest->id,
                $e->getMessage()
            );

            Log::error('WhatsApp: Failed to send picked up notification', [
                'request_code' => $event->documentRequest->request_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleDocumentCompleted(DocumentCompleted $event): void
    {
        try {
            $this->whatsAppService->notifyDocumentCompleted($event->documentRequest);

            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document completed',
                'sent',
                $event->documentRequest->id
            );

            Log::info('WhatsApp: Document completed notification sent', [
                'request_code' => $event->documentRequest->request_code,
                'phone' => $event->documentRequest->applicant_phone,
                'delivery_method' => $event->documentRequest->delivery_method
            ]);
        } catch (Exception $e) {
            $this->logNotification(
                $event->documentRequest->applicant_phone,
                'Document completed',
                'failed',
                $event->documentRequest->id,
                $e->getMessage()
            );

            Log::error('WhatsApp: Failed to send completed notification', [
                'request_code' => $event->documentRequest->request_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handle($event): void
    {
        $eventClass = get_class($event);
        $methodName = 'handle' . class_basename($eventClass);

        if (method_exists($this, $methodName)) {
            $this->$methodName($event);
        } else {
            Log::warning('WhatsApp: No handler found for event', [
                'event' => $eventClass,
                'expected_method' => $methodName
            ]);
        }
    }

    private function logNotification(
        string $phone,
        string $message,
        string $status,
        ?int $documentRequestId = null,
        ?string $errorMessage = null
    ): void {
        try {
            if (!\Schema::hasTable('whatsapp_notifications')) {
                return;
            }

            \DB::table('whatsapp_notifications')->insert([
                'document_request_id' => $documentRequestId,
                'recipient_phone' => $phone,
                'message_type' => $message,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::warning('Failed to log WhatsApp notification to database', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
