<?php

namespace App\Listeners;

use App\Events\DocumentVerificationRejected;
use App\Services\{WhatsAppService, NotificationService};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminVerificationRejected implements ShouldQueue
{
    public function __construct(
        private WhatsAppService $whatsappService,
        private NotificationService $notificationService
    ) {}

    public function handle(DocumentVerificationRejected $event): void
    {
        $document = $event->documentRequest;
        $verification = $event->verification;
        $authority = $verification->authority;
        $reason = $event->reason;

        $currentLevel = $verification->verification_level;

        try {
            $this->whatsappService->sendVerificationRejectedToAdminByLevel(
                $document,
                $authority,
                $reason,
                $currentLevel
            );

            $this->whatsappService->sendVerificationRejectedByLevel(
                $document,
                $authority,
                $reason,
                $currentLevel
            );

            $levelLabels = [
                1 => 'Level 1 (Ketua Akademik)',
                2 => 'Level 2 (Wakil Ketua 3)',
                3 => 'Level 3 (Direktur - Final)'
            ];

            $message = "ℹ️ *SIPETER - Penolakan Tercatat*\n\n";
            $message .= "Dokumen {$document->request_code} telah Anda tolak.\n\n";
            $message .= "📊 *Detail:*\n";
            $message .= "• Level: {$levelLabels[$currentLevel]}\n";
            $message .= "• Alasan: {$reason}\n\n";
            $message .= "⚠️ *PROSES BERHENTI*\n";
            $message .= "Verifikasi dihentikan pada {$levelLabels[$currentLevel]}.\n\n";
            $message .= "Penolakan telah dicatat dan dikirim ke admin serta pemohon.\n\n";
            $message .= "---\n";
            $message .= "_SIPETER - STABA Bandung_";

            $this->whatsappService->sendMessage($authority->phone, $message);

            $this->notificationService->notifyAdminVerificationLevelRejected(
                $document,
                $verification,
                $currentLevel,
                $reason
            );

            if ($document->user_id) {
                $this->notificationService->notifyUserVerificationLevelRejected(
                    $document,
                    $currentLevel,
                    $reason
                );
            }

            Log::info('Verification rejected notifications sent', [
                'level' => $currentLevel,
                'document_code' => $document->request_code,
                'authority' => $authority->name,
                'reason' => $reason,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send verification rejected notifications', [
                'level' => $currentLevel,
                'document_code' => $document->request_code,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(DocumentVerificationRejected $event, \Throwable $exception): void
    {
        Log::error('NotifyAdminVerificationRejected job failed', [
            'document_code' => $event->documentRequest->request_code,
            'verification_level' => $event->verification->verification_level,
            'authority' => $event->verification->authority->name,
            'error' => $exception->getMessage(),
        ]);
    }
}
