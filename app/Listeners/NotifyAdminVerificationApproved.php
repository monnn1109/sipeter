<?php

namespace App\Listeners;

use App\Events\DocumentVerificationApproved;
use App\Services\{WhatsAppService, NotificationService, DocumentVerificationService};
use Illuminate\Support\Facades\Log;

class NotifyAdminVerificationApproved
{
    public function __construct(
        private WhatsAppService $whatsappService,
        private NotificationService $notificationService,
        private DocumentVerificationService $verificationService
    ) {}

    public function handle(DocumentVerificationApproved $event): void
    {
        $document = $event->documentRequest;
        $authority = $event->authority;
        $notes = $event->notes;

        // 🔥 FIXED: Get current level from document (after update)
        $currentLevel = $document->current_verification_step;

        $progressMap = [1 => 33, 2 => 66, 3 => 100];
        $progressPercentage = $progressMap[$currentLevel] ?? 0;

        Log::info('NotifyAdminVerificationApproved - START', [
            'document_code' => $document->request_code,
            'current_level' => $currentLevel,
            'progress' => "{$progressPercentage}%",
            'authority' => $authority->name,
        ]);

        try {
            // 1. Send WhatsApp to Admin
            $this->whatsappService->sendLevelApprovedToAdmin(
                $document,
                $authority,
                $currentLevel
            );

            // 2. Send WhatsApp to User
            $this->whatsappService->sendLevelApprovedNotification(
                $document,
                $authority,
                $currentLevel
            );

            // 3. Send WhatsApp to Authority (confirmation)
            $levelLabels = [
                1 => 'Level 1 (Ketua Akademik)',
                2 => 'Level 2 (Wakil Ketua 3)',
                3 => 'Level 3 (Direktur - Final)'
            ];

            $nextStepMap = [
                1 => 'Level 2 (Wakil Ketua 3)',
                2 => 'Level 3 (Direktur - Final)',
                3 => 'Penandatanganan (TTD)'
            ];

            $message = "✅ *SIPETER - Verifikasi Berhasil*\n\n";
            $message .= "Terima kasih telah memverifikasi dokumen {$document->request_code}.\n\n";
            $message .= "📊 *Detail:*\n";
            $message .= "• Level: {$levelLabels[$currentLevel]}\n";
            $message .= "• Progress: {$progressPercentage}% ({$currentLevel}/3)\n";
            $message .= "• Selanjutnya: {$nextStepMap[$currentLevel]}\n\n";

            if ($currentLevel === 3) {
                $message .= "🎉 *Semua verifikasi selesai!*\n";
                $message .= "Dokumen akan segera ditandatangani.\n\n";
            } else {
                $message .= "✅ Dokumen otomatis diteruskan ke {$nextStepMap[$currentLevel]}.\n\n";
            }

            $message .= "---\n";
            $message .= "_SIPETER - STABA Bandung_";

            $this->whatsappService->sendWithLogging(
                $authority->phone,
                $message,
                $document->id,
                $authority->name,
                'authority',
                "verification_level_{$currentLevel}_confirmed"
            );

            // 4. Send in-app notification to Admin
            $this->notificationService->notifyAdminVerificationLevelApproved(
                $document,
                $document->verifications()->where('verification_level', $currentLevel)->first(),
                $currentLevel
            );

            // 5. Send in-app notification to User
            if ($document->user_id) {
                $this->notificationService->notifyUserVerificationLevelApproved(
                    $document,
                    $currentLevel
                );
            }

            Log::info('Notifications sent successfully', [
                'level' => $currentLevel,
                'progress' => "{$progressPercentage}%",
                'document_code' => $document->request_code,
            ]);

            // 🔥 CRITICAL: AUTO-TRIGGER NEXT LEVEL (if not final)
            if ($currentLevel < 3) {
                Log::info('Auto-triggering next verification level', [
                    'current_level' => $currentLevel,
                    'next_level' => $currentLevel + 1,
                    'document_code' => $document->request_code,
                ]);

                try {
                    // 🔥 Call proceedToNextLevel (this is the ONLY place it should be called)
                    $this->verificationService->proceedToNextLevel($document->fresh());

                    Log::info('✅ Successfully triggered next verification level', [
                        'current_level' => $currentLevel,
                        'next_level' => $currentLevel + 1,
                        'document_code' => $document->request_code,
                    ]);
                } catch (\Exception $e) {
                    Log::error('❌ Failed to trigger next verification level', [
                        'current_level' => $currentLevel,
                        'next_level' => $currentLevel + 1,
                        'document_code' => $document->request_code,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    // Send notification to admin about the failure
                    $errorMessage = "⚠️ *SIPETER - Auto-Trigger Gagal*\n\n";
                    $errorMessage .= "Dokumen: {$document->request_code}\n";
                    $errorMessage .= "Level {$currentLevel} approved, tapi gagal otomatis lanjut ke Level " . ($currentLevel + 1) . "\n\n";
                    $errorMessage .= "Error: {$e->getMessage()}\n\n";
                    $errorMessage .= "Silakan trigger manual dari dashboard admin.";

                    $this->whatsappService->sendToAdmin($errorMessage);
                }
            } else {
                Log::info('🎉 All verification levels completed', [
                    'document_code' => $document->request_code,
                    'status' => 'verification_step_3_approved',
                    'message' => 'Ready for signature request',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Failed in NotifyAdminVerificationApproved', [
                'level' => $currentLevel,
                'document_code' => $document->request_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Try to notify admin about the error
            try {
                $errorMessage = "⚠️ *SIPETER - Error Notifikasi*\n\n";
                $errorMessage .= "Dokumen: {$document->request_code}\n";
                $errorMessage .= "Level: {$currentLevel}\n";
                $errorMessage .= "Error: {$e->getMessage()}";

                $this->whatsappService->sendToAdmin($errorMessage);
            } catch (\Exception $nestedError) {
                Log::error('Failed to send error notification', [
                    'original_error' => $e->getMessage(),
                    'nested_error' => $nestedError->getMessage(),
                ]);
            }
        }
    }
}
