<?php

namespace App\Services;

use App\Models\{DocumentRequest, SignatureAuthority, User};
use Illuminate\Support\Facades\Log;

/**
 * SignatureNotificationService
 *
 * Handles all signature-related notifications
 * Integrates with WhatsAppService and NotificationService
 */
class SignatureNotificationService
{
    public function __construct(
        protected WhatsAppService $whatsappService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Send signature request notifications to authorities
     *
     * @param DocumentRequest $documentRequest
     * @param array $authorities Array of SignatureAuthority models
     * @return array Results
     */
    public function notifySignatureRequested(
        DocumentRequest $documentRequest,
        array $authorities
    ): array {
        $results = [
            'whatsapp' => [],
            'in_app' => false,
        ];

        // Send WhatsApp to authorities
        $whatsappResult = $this->whatsappService->notifySignatureRequestedBulk(
            $documentRequest,
            $authorities
        );
        $results['whatsapp'] = $whatsappResult;

        // Send in-app notification to admins
        try {
            $this->notificationService->notifyAdminSignatureRequested($documentRequest);
            $results['in_app'] = true;
        } catch (\Exception $e) {
            Log::error('Failed to send in-app notification for signature request', [
                'document_request_id' => $documentRequest->id,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Send notification when signature is uploaded
     *
     * @param DocumentRequest $documentRequest
     * @param SignatureAuthority $authority
     * @return array Results
     */
    public function notifySignatureUploaded(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): array {
        $results = [
            'whatsapp' => false,
            'in_app' => false,
        ];

        // Send WhatsApp to admins
        try {
            $results['whatsapp'] = $this->whatsappService->notifyAdminSignatureUploaded(
                $documentRequest,
                $authority
            );
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp for signature upload', [
                'document_request_id' => $documentRequest->id,
                'authority_id' => $authority->id,
                'error' => $e->getMessage()
            ]);
        }

        // Send in-app notification to admins
        try {
            $this->notificationService->notifyAdminSignatureUploaded(
                $documentRequest,
                $authority
            );
            $results['in_app'] = true;
        } catch (\Exception $e) {
            Log::error('Failed to send in-app notification for signature upload', [
                'document_request_id' => $documentRequest->id,
                'authority_id' => $authority->id,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Send notification when signature is verified
     *
     * @param DocumentRequest $documentRequest
     * @param SignatureAuthority $authority
     * @return array Results
     */
    public function notifySignatureVerified(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): array {
        $results = [
            'whatsapp' => false,
        ];

        // Send WhatsApp to authority (confirming verification)
        try {
            $results['whatsapp'] = $this->whatsappService->notifySignatureVerified(
                $documentRequest,
                $authority
            );
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp for signature verified', [
                'document_request_id' => $documentRequest->id,
                'authority_id' => $authority->id,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Send notification when signature is rejected
     *
     * @param DocumentRequest $documentRequest
     * @param SignatureAuthority $authority
     * @param string $reason
     * @return array Results
     */
    public function notifySignatureRejected(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $reason
    ): array {
        $results = [
            'whatsapp' => false,
        ];

        // Send WhatsApp to authority (notify rejection)
        try {
            $results['whatsapp'] = $this->whatsappService->notifySignatureRejected(
                $documentRequest,
                $authority,
                $reason
            );
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp for signature rejected', [
                'document_request_id' => $documentRequest->id,
                'authority_id' => $authority->id,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Send notification when all signatures are completed
     *
     * @param DocumentRequest $documentRequest
     * @return array Results
     */
    public function notifyAdminAllSignaturesCompleted(DocumentRequest $documentRequest): array
    {
        $results = [
            'in_app' => false,
            'user_notified' => false,
        ];

        // Notify admins (in-app)
        try {
            $this->notificationService->notifyAdminSignatureCompleted($documentRequest);
            $results['in_app'] = true;
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about signature completion', [
                'document_request_id' => $documentRequest->id,
                'error' => $e->getMessage()
            ]);
        }

        // Notify internal user if exists
        if ($documentRequest->user_id) {
            try {
                $this->notificationService->notifyUserSignatureCompleted($documentRequest);
                $results['user_notified'] = true;
            } catch (\Exception $e) {
                Log::error('Failed to notify user about signature completion', [
                    'document_request_id' => $documentRequest->id,
                    'user_id' => $documentRequest->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Send signature reminder
     *
     * @param DocumentRequest $documentRequest
     * @param SignatureAuthority $authority
     * @return bool
     */
    public function sendSignatureReminder(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): bool {
        try {
            return $this->whatsappService->sendSignatureReminder(
                $documentRequest,
                $authority
            );
        } catch (\Exception $e) {
            Log::error('Failed to send signature reminder', [
                'document_request_id' => $documentRequest->id,
                'authority_id' => $authority->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send bulk reminders for overdue signatures
     *
     * @param array $signatures Array of DocumentSignature models
     * @return array Results
     */
    public function sendBulkReminders(array $signatures): array
    {
        $results = [
            'total' => count($signatures),
            'sent' => 0,
            'failed' => 0,
            'details' => [],
        ];

        foreach ($signatures as $signature) {
            $success = $this->sendSignatureReminder(
                $signature->documentRequest,
                $signature->signatureAuthority
            );

            $results['details'][] = [
                'signature_id' => $signature->id,
                'document_code' => $signature->documentRequest->request_code,
                'authority_name' => $signature->signatureAuthority->name,
                'success' => $success,
            ];

            if ($success) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Notify user about signature progress
     *
     * @param DocumentRequest $documentRequest
     * @return bool
     */
    public function notifyUserSignatureInProgress(DocumentRequest $documentRequest): bool
    {
        // Only for internal users
        if (!$documentRequest->user_id) {
            return false;
        }

        try {
            $this->notificationService->notifyUserSignatureInProgress($documentRequest);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to notify user about signature progress', [
                'document_request_id' => $documentRequest->id,
                'user_id' => $documentRequest->user_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get notification summary for document
     *
     * @param DocumentRequest $documentRequest
     * @return array
     */
    public function getNotificationSummary(DocumentRequest $documentRequest): array
    {
        $signatures = $documentRequest->signatures;

        return [
            'total_authorities' => $signatures->count(),
            'requested' => $signatures->where('status', 'requested')->count(),
            'uploaded' => $signatures->where('status', 'uploaded')->count(),
            'verified' => $signatures->where('status', 'verified')->count(),
            'rejected' => $signatures->where('status', 'rejected')->count(),
            'pending_notifications' => $signatures->whereIn('status', ['requested', 'uploaded'])->count(),
            'last_activity' => $documentRequest->activities()
                ->whereIn('activity_type', [
                    'signature_requested',
                    'signature_uploaded',
                    'signature_verified',
                    'signature_rejected',
                ])
                ->latest()
                ->first()?->created_at,
        ];
    }

    /**
     * Test WhatsApp connection
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function testWhatsAppConnection(string $phoneNumber): bool
    {
        try {
            $testMessage = "🔔 *SIPETER STABA - Test Connection*\n\n";
            $testMessage .= "Ini adalah pesan test dari sistem SIPETER.\n";
            $testMessage .= "Jika Anda menerima pesan ini, koneksi WhatsApp berhasil!\n\n";
            $testMessage .= "Waktu: " . now()->format('d M Y, H:i:s') . " WIB";

            return $this->whatsappService->sendMessage($phoneNumber, $testMessage);
        } catch (\Exception $e) {
            Log::error('WhatsApp test connection failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Log notification activity
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    protected function logNotification(string $type, array $data): void
    {
        Log::info("Signature notification sent: {$type}", $data);
    }
}
