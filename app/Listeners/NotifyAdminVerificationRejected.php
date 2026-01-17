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
        Log::info('🎯 NotifyAdminVerificationRejected LISTENER TRIGGERED', [
            'document_code' => $event->documentRequest->request_code,
            'verification_level' => $event->verification->verification_level,
        ]);

        $document = $event->documentRequest;
        $verification = $event->verification;
        $authority = $verification->authority;
        $reason = $event->reason;
        $currentLevel = $verification->verification_level;

        Log::info('📋 Rejection notification details', [
            'document_code' => $document->request_code,
            'level' => $currentLevel,
            'authority' => $authority->name,
            'user_phone' => $document->applicant_phone,
            'user_name' => $document->applicant_name,
        ]);

        try {
            // 🔥 FIXED: Use WhatsAppService method instead of duplicating logic
            // This method already handles:
            // 1. Send WA to user
            // 2. Send WA to admin(s)
            // 3. All logging
            $sent = $this->whatsappService->sendLevelRejectedNotification(
                $document,
                $authority,
                $currentLevel,
                $reason
            );

            if ($sent) {
                Log::info('✅ Rejection notifications sent successfully via WhatsAppService', [
                    'document_code' => $document->request_code,
                    'level' => $currentLevel,
                    'authority' => $authority->name,
                ]);
            } else {
                Log::warning('⚠️ Rejection notifications partially failed', [
                    'document_code' => $document->request_code,
                    'level' => $currentLevel,
                ]);
            }

            // 📱 Send confirmation WA to Authority
            $authorityMessage = "✅ *SIPETER - PENOLAKAN TERCATAT*\n\n";
            $authorityMessage .= "Halo *{$authority->name}*,\n\n";
            $authorityMessage .= "Penolakan Anda telah tercatat di sistem.\n\n";
            $authorityMessage .= "📊 *Detail:*\n";
            $authorityMessage .= "• Kode Dokumen: *{$document->request_code}*\n";
            $authorityMessage .= "• Level: {$this->getLevelLabel($currentLevel)}\n";
            $authorityMessage .= "• Alasan: _{$reason}_\n\n";
            $authorityMessage .= "⚠️ *PROSES BERHENTI*\n";
            $authorityMessage .= "Verifikasi dihentikan pada {$this->getLevelLabel($currentLevel)}.\n\n";
            $authorityMessage .= "Penolakan telah dikirim ke admin dan pemohon.\n\n";
            $authorityMessage .= "---\n_SIPETER - STABA Bandung_";

            $authoritySent = $this->whatsappService->sendMessage($authority->phone, $authorityMessage);

            if ($authoritySent) {
                Log::info('✅ Rejection confirmation WA sent to authority', [
                    'level' => $currentLevel,
                    'authority' => $authority->name,
                    'phone' => $authority->phone,
                    'document_code' => $document->request_code,
                ]);
            }

            // 🔔 Send in-app notification to Admin
            $this->notificationService->notifyAdminVerificationLevelRejected(
                $document,
                $verification,
                $currentLevel,
                $reason
            );

            // 🔔 Send in-app notification to User (if logged in)
            if ($document->user_id) {
                $this->notificationService->notifyUserVerificationLevelRejected(
                    $document,
                    $currentLevel,
                    $reason
                );
            }

            Log::info('✅ All rejection notifications processed', [
                'level' => $currentLevel,
                'document_code' => $document->request_code,
                'authority' => $authority->name,
                'reason' => $reason,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to send verification rejected notifications', [
                'level' => $currentLevel,
                'document_code' => $document->request_code,
                'authority' => $authority->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function getLevelLabel(int $level): string
    {
        return match($level) {
            1 => 'Level 1 (Ketua Akademik)',
            2 => 'Level 2 (Wakil Ketua 3 - Kemahasiswaan)',
            3 => 'Level 3 (Direktur - Final)',
            default => "Level {$level}",
        };
    }

    public function failed(DocumentVerificationRejected $event, \Throwable $exception): void
    {
        Log::error('❌ NotifyAdminVerificationRejected job failed permanently', [
            'document_code' => $event->documentRequest->request_code,
            'verification_level' => $event->verification->verification_level,
            'authority' => $event->verification->authority->name,
            'reason' => $event->reason,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
