<?php

namespace App\Listeners;

use App\Events\SignatureRequested;
use App\Services\{WhatsAppService, NotificationService};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSignatureRequestNotification implements ShouldQueue
{
    public function __construct(
        protected WhatsAppService $whatsappService,
        protected NotificationService $notificationService
    ) {}

    public function handle(SignatureRequested $event): void
    {
        try {
            $this->whatsappService->notifySignatureRequested(
                $event->documentRequest,
                $event->authority,
                $event->uploadLink
            );

           $this->whatsappService->sendWithLogging(
                $event->documentRequest->applicant_phone,
                $this->buildUserNotificationMessage($event),
                $event->documentRequest->id,
                $event->documentRequest->applicant_name,
                'user',
                'signature_requested'
            );

            Log::info('Signature request notification sent', [
                'document_request_id' => $event->documentRequest->id,
                'authority_id' => $event->authority->id,
                'authority_name' => $event->authority->name,
                'upload_link' => $event->uploadLink,
                'all_verifications_completed' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send signature request notification', [
                'document_request_id' => $event->documentRequest->id,
                'authority_id' => $event->authority->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildUserNotificationMessage(SignatureRequested $event): string
    {
        $message = "ℹ️ *SIPETER - Update Status Dokumen*\n\n";
        $message .= "Halo {$event->documentRequest->applicant_name},\n\n";

        $message .= "🎉 *Semua verifikasi selesai!* (100%)\n\n";
        $message .= "Dokumen Anda sedang diproses untuk penandatanganan:\n\n";
        $message .= "📄 *Kode:* {$event->documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$event->documentRequest->documentType->name}\n";
        $message .= "✏️ *Penanda Tangan:* {$event->authority->name}\n";
        $message .= "💼 *Jabatan:* {$event->authority->position}\n";
        $message .= "⏳ *Status:* Menunggu Tanda Tangan\n\n";

        $message .= "✅ *Verifikasi:*\n";
        $message .= "• Level 1: Ketua Akademik ✓\n";
        $message .= "• Level 2: Wakil Ketua 3 ✓\n";
        $message .= "• Level 3: Direktur ✓\n\n";

        if ($event->documentRequest->isGuestRequest()) {
            $message .= "🔗 Track: " . route('mahasiswa.tracking.detail', $event->documentRequest->request_code) . "\n\n";
        } else {
            $message .= "🔗 Dashboard: " . route('internal.my-documents.index') . "\n\n";
        }

        $message .= "Mohon menunggu, dokumen akan segera ditandatangani. 🙏\n\n";
        $message .= "---\n";
        $message .= "_SIPETER - STABA Bandung_";

        return $message;
    }

    public function failed(SignatureRequested $event, \Throwable $exception): void
    {
        Log::error('Signature request notification job failed', [
            'document_request_id' => $event->documentRequest->id,
            'authority_id' => $event->authority->id,
            'upload_link' => $event->uploadLink,
            'error' => $exception->getMessage(),
        ]);
    }
}
