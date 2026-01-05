<?php

namespace App\Services;

use App\Models\{DocumentRequest, SignatureAuthority, WhatsAppNotification};
use Illuminate\Support\Facades\{Http, Log};

class WhatsAppService
{
    private $apiUrl;
    private $apiKey;
    private $gatewayUrl;
    private $useGateway;
    private $loggingEnabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->apiKey = config('services.whatsapp.api_key');
        $this->gatewayUrl = config('services.whatsapp.gateway_url', 'http://localhost:3000');
        $this->useGateway = config('services.whatsapp.use_gateway', true);
        $this->loggingEnabled = config('services.whatsapp.logging_enabled', true);
    }

    public function sendMessage(string $phoneNumber, string $message): bool
    {
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        $waMessage = null;

        try {
            if ($this->loggingEnabled) {
                $waMessage = WhatsAppNotification::create([
                    'document_request_id' => null,
                    'recipient_phone' => $formattedPhone,
                    'recipient_name' => 'Unknown',
                    'recipient_type' => 'user',
                    'event_type' => 'other',
                    'message' => $message,
                    'status' => 'pending',
                ]);
            }

            $success = $this->useGateway
                ? $this->sendViaGateway($formattedPhone, $message)
                : $this->sendViaApi($formattedPhone, $message);

            if ($this->loggingEnabled && $waMessage) {
                if ($success) {
                    $waMessage->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                } else {
                    $waMessage->update([
                        'status' => 'failed',
                        'error_message' => 'Failed to send via ' . ($this->useGateway ? 'gateway' : 'API'),
                    ]);
                }
            }

            return $success;

        } catch (\Exception $e) {
            Log::error('WhatsApp exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            if ($this->loggingEnabled && $waMessage) {
                $waMessage->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return false;
        }
    }

    private function sendViaGateway(string $phone, string $message): bool
    {
        try {
            Log::info('WhatsApp Gateway - Sending request', [
                'url' => "{$this->gatewayUrl}/send-message",
                'phone' => $phone,
                'message_preview' => substr($message, 0, 50) . '...',
            ]);

            $response = Http::timeout(10)->post("{$this->gatewayUrl}/send-message", [
                'number' => $phone,
                'message' => $message,
                'password' => config('services.whatsapp.gateway_password', 'r11223344'),
            ]);

            Log::info('WhatsApp Gateway - Response received', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['success'] ?? true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp Gateway exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function sendViaApi(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl . '/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp sent via API', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            }

            Log::error('WhatsApp API failed', [
                'phone' => $phone,
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp API exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendWithLogging(
        string $phone,
        string $message,
        ?int $documentRequestId = null,
        string $recipientName = 'Unknown',
        string $recipientType = 'user',
        string $eventType = 'other'
    ): bool {
        $formattedPhone = $this->formatPhoneNumber($phone);
        $waMessage = null;

        try {
            if ($this->loggingEnabled) {
                $waMessage = WhatsAppNotification::create([
                    'document_request_id' => $documentRequestId,
                    'recipient_phone' => $formattedPhone,
                    'recipient_name' => $recipientName,
                    'recipient_type' => $recipientType,
                    'event_type' => $eventType,
                    'message' => $message,
                    'status' => 'pending',
                ]);
            }

            $success = $this->useGateway
                ? $this->sendViaGateway($formattedPhone, $message)
                : $this->sendViaApi($formattedPhone, $message);

            if ($this->loggingEnabled && $waMessage) {
                $waMessage->update([
                    'status' => $success ? 'sent' : 'failed',
                    'sent_at' => $success ? now() : null,
                    'error_message' => $success ? null : 'Failed to send message',
                ]);
            }

            return $success;

        } catch (\Exception $e) {
            Log::error('WhatsApp sendWithLogging exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            if ($this->loggingEnabled && $waMessage) {
                $waMessage->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
            return false;
        }
    }

    public function send(array $data): bool
    {
        return $this->sendMessage($data['phone'], $data['message']);
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    // ==================== 🆕 3-LEVEL VERIFICATION METHODS ====================

    /**
     * ✅ CRITICAL: Method yang dipanggil oleh Listener
     */
    public function sendVerificationRequestByLevel(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $verificationLink,
        int $level
    ): bool {
        return match($level) {
            1 => $this->sendVerificationLevel1Notification($documentRequest, $authority, $verificationLink),
            2 => $this->sendVerificationLevel2Notification($documentRequest, $authority, $verificationLink),
            3 => $this->sendVerificationLevel3Notification($documentRequest, $authority, $verificationLink),
            default => false
        };
    }

    /**
     * ✅ CRITICAL: Method yang dipanggil oleh Listener
     */
    public function sendVerificationInProgressByLevel(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level
    ): bool {
        $message = "ℹ️ *SIPETER - UPDATE DOKUMEN*\n\n";
        $message .= "Halo {$documentRequest->applicant_name},\n\n";
        $message .= "Dokumen Anda sedang dalam proses verifikasi Level {$level}:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👔 *Verifikator:* {$authority->name}\n";
        $message .= "⏳ *Status:* Level {$level}/3 sedang diverifikasi\n\n";

        if ($documentRequest->isGuestRequest()) {
            $message .= "🔗 Track: " . route('mahasiswa.tracking.detail', $documentRequest->request_code) . "\n\n";
        } else {
            $message .= "🔗 Dashboard: " . route('internal.my-documents.index') . "\n\n";
        }

        $message .= "Mohon menunggu. Terima kasih! 🙏";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            "verification_level_{$level}_in_progress"
        );
    }

    /**
     * ✅ NEW: Send verification request for Level 1 (Ketua Akademik)
     */
    public function sendVerificationLevel1Notification(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $verificationLink
    ): bool {
        $message = "📝 *SIPETER - REQUEST VERIFIKASI LEVEL 1*\n\n";
        $message .= "Yth. {$authority->name}\n";
        $message .= "{$authority->position}\n\n";
        $message .= "Mohon verifikasi kelayakan dokumen:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n";
        $message .= "🆔 *NIM/NIP:* {$documentRequest->applicant_identifier}\n";

        if ($documentRequest->purpose) {
            $message .= "📝 *Keperluan:* {$documentRequest->purpose}\n";
        }

        $message .= "\n⚠️ *LEVEL 1 dari 3*\n";
        $message .= "Setelah disetujui, otomatis lanjut ke Level 2.\n\n";
        $message .= "🔗 *Link Verifikasi:*\n{$verificationLink}\n\n";
        $message .= "⏰ *Expired:* 3 hari\n\n";
        $message .= "Terima kasih atas kerjasamanya. 🙏\n\n";
        $message .= "---\n";
        $message .= "_SIPETER - STABA Bandung_";

        return $this->sendWithLogging(
            $authority->phone,
            $message,
            $documentRequest->id,
            $authority->name,
            'authority',
            'verification_level_1_requested'
        );
    }

    /**
     * ✅ NEW: Send verification request for Level 2 (Wakil Ketua 3)
     */
    public function sendVerificationLevel2Notification(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $verificationLink
    ): bool {
        $level1Authority = $documentRequest->getVerificationLevelAuthority(1);
        $level1ApprovedName = $level1Authority ? $level1Authority->name : 'Level 1';

        $message = "📝 *SIPETER - REQUEST VERIFIKASI LEVEL 2*\n\n";
        $message .= "Yth. {$authority->name}\n";
        $message .= "{$authority->position}\n\n";
        $message .= "Mohon verifikasi dokumen yang telah disetujui Level 1:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n";
        $message .= "🆔 *NIM/NIP:* {$documentRequest->applicant_identifier}\n\n";
        $message .= "✅ *Level 1 Disetujui:* {$level1ApprovedName}\n\n";

        if ($documentRequest->purpose) {
            $message .= "📝 *Keperluan:* {$documentRequest->purpose}\n\n";
        }

        $message .= "⚠️ *LEVEL 2 dari 3*\n";
        $message .= "Setelah disetujui, otomatis lanjut ke Level 3 (Final).\n\n";
        $message .= "🔗 *Link Verifikasi:*\n{$verificationLink}\n\n";
        $message .= "⏰ *Expired:* 3 hari\n\n";
        $message .= "Terima kasih atas kerjasamanya. 🙏\n\n";
        $message .= "---\n";
        $message .= "_SIPETER - STABA Bandung_";

        return $this->sendWithLogging(
            $authority->phone,
            $message,
            $documentRequest->id,
            $authority->name,
            'authority',
            'verification_level_2_requested'
        );
    }

    /**
     * ✅ NEW: Send verification request for Level 3 (Direktur - Final)
     */
    public function sendVerificationLevel3Notification(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $verificationLink
    ): bool {
        $level1Authority = $documentRequest->getVerificationLevelAuthority(1);
        $level2Authority = $documentRequest->getVerificationLevelAuthority(2);

        $message = "📝 *SIPETER - REQUEST VERIFIKASI LEVEL 3 (FINAL)*\n\n";
        $message .= "Yth. {$authority->name}\n";
        $message .= "{$authority->position}\n\n";
        $message .= "Mohon verifikasi FINAL untuk dokumen:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n";
        $message .= "🆔 *NIM/NIP:* {$documentRequest->applicant_identifier}\n\n";
        $message .= "✅ *Disetujui:*\n";
        $message .= "  • Level 1: " . ($level1Authority ? $level1Authority->name : '-') . "\n";
        $message .= "  • Level 2: " . ($level2Authority ? $level2Authority->name : '-') . "\n\n";

        if ($documentRequest->purpose) {
            $message .= "📝 *Keperluan:* {$documentRequest->purpose}\n\n";
        }

        $message .= "⚠️ *LEVEL 3 dari 3 (FINAL APPROVAL)*\n";
        $message .= "Ini adalah persetujuan terakhir sebelum lanjut ke TTD.\n\n";
        $message .= "🔗 *Link Verifikasi:*\n{$verificationLink}\n\n";
        $message .= "⏰ *Expired:* 3 hari\n\n";
        $message .= "Terima kasih atas kerjasamanya. 🙏\n\n";
        $message .= "---\n";
        $message .= "_SIPETER - STABA Bandung_";

        return $this->sendWithLogging(
            $authority->phone,
            $message,
            $documentRequest->id,
            $authority->name,
            'authority',
            'verification_level_3_requested'
        );
    }

    public function sendLevelApprovedNotification(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level
    ): bool {
        $percentage = round(($level / 3) * 100);
        $nextLevelInfo = $level < 3 ? "\n\n✅ *Status:* Otomatis lanjut ke Level " . ($level + 1) : "\n\n🎉 *Status:* Semua verifikasi selesai! Lanjut ke TTD.";

        $messageUser = "✅ *SIPETER - VERIFIKASI LEVEL {$level} DISETUJUI!*\n\n";
        $messageUser .= "Halo {$documentRequest->applicant_name},\n\n";
        $messageUser .= "Kabar baik! Verifikasi Level {$level} telah disetujui:\n\n";
        $messageUser .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $messageUser .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $messageUser .= "✅ *Disetujui:* {$authority->name}\n";
        $messageUser .= "📊 *Progress:* {$percentage}% ({$level}/3)\n";
        $messageUser .= $nextLevelInfo . "\n\n";

        if ($documentRequest->isGuestRequest()) {
            $messageUser .= "🔗 Track: " . route('mahasiswa.tracking.detail', $documentRequest->request_code) . "\n\n";
        } else {
            $messageUser .= "🔗 Dashboard: " . route('internal.my-documents.index') . "\n\n";
        }

        $messageUser .= "Terima kasih! 🙏\n\n";
        $messageUser .= "---\n";
        $messageUser .= "_SIPETER - STABA Bandung_";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $messageUser,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            "verification_level_{$level}_approved"
        );
    }

    public function sendLevelApprovedToAdmin(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level
    ): bool {
        $adminPhones = config('services.whatsapp.admin_phones', ['6282129554934']);

        $nextAction = $level < 3
            ? "Sistem akan otomatis kirim ke Level " . ($level + 1)
            : "🎉 SEMUA VERIFIED! Silakan lanjut request TTD";

        $message = "✅ *SIPETER - LEVEL {$level} APPROVED*\n\n";
        $message .= "{$authority->name} telah memverifikasi:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n";
        $message .= "✅ *Level:* {$level}/3\n";
        $message .= "📊 *Progress:* " . round(($level / 3) * 100) . "%\n\n";
        $message .= "ℹ️ *Next:* {$nextAction}\n\n";
        $message .= "🔗 " . route('admin.documents.show', $documentRequest->id) . "\n\n";
        $message .= "---\n";
        $message .= "_SIPETER - STABA Bandung_";

        $success = false;
        foreach ($adminPhones as $phone) {
            if ($this->sendWithLogging(
                $phone,
                $message,
                $documentRequest->id,
                'Admin',
                'admin',
                "verification_level_{$level}_approved"
            )) {
                $success = true;
            }
        }

        return $success;
    }

    public function sendLevelRejectedNotification(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level,
        string $reason
    ): bool {
        $messageUser = "❌ *SIPETER - VERIFIKASI DITOLAK*\n\n";
        $messageUser .= "Halo {$documentRequest->applicant_name},\n\n";
        $messageUser .= "Mohon maaf, verifikasi dokumen Anda ditolak di Level {$level}:\n\n";
        $messageUser .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $messageUser .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $messageUser .= "❌ *Ditolak oleh:* {$authority->name} (Level {$level})\n\n";
        $messageUser .= "📝 *Alasan:*\n{$reason}\n\n";
        $messageUser .= "⚠️ *PROSES BERHENTI*\n\n";
        $messageUser .= "💡 Silakan hubungi bagian akademik untuk informasi lebih lanjut.\n\n";
        $messageUser .= "📞 *Kontak:*\n";
        $messageUser .= "Telp: " . config('services.staba.phone', '-') . "\n";
        $messageUser .= "Email: " . config('services.staba.email', '-') . "\n\n";
        $messageUser .= "---\n";
        $messageUser .= "_SIPETER - STABA Bandung_";

        $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $messageUser,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            "verification_level_{$level}_rejected"
        );

        $adminPhones = config('services.whatsapp.admin_phones', ['6282129554934']);

        $messageAdmin = "❌ *SIPETER - VERIFIKASI DITOLAK*\n\n";
        $messageAdmin .= "{$authority->name} menolak verifikasi Level {$level}:\n\n";
        $messageAdmin .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $messageAdmin .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $messageAdmin .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n";
        $messageAdmin .= "❌ *Level:* {$level}/3\n\n";
        $messageAdmin .= "📝 *Alasan:*\n{$reason}\n\n";
        $messageAdmin .= "⚠️ *PROSES BERHENTI*\n\n";
        $messageAdmin .= "🔗 " . route('admin.documents.show', $documentRequest->id) . "\n\n";
        $messageAdmin .= "---\n";
        $messageAdmin .= "_SIPETER - STABA Bandung_";

        $success = false;
        foreach ($adminPhones as $phone) {
            if ($this->sendWithLogging(
                $phone,
                $messageAdmin,
                $documentRequest->id,
                'Admin',
                'admin',
                "verification_level_{$level}_rejected"
            )) {
                $success = true;
            }
        }

        return $success;
    }

    public function sendVerificationRequest(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $verificationLink
    ): bool {
        $level = $authority->getVerificationLevel();
        return match($level) {
            1 => $this->sendVerificationLevel1Notification($documentRequest, $authority, $verificationLink),
            2 => $this->sendVerificationLevel2Notification($documentRequest, $authority, $verificationLink),
            3 => $this->sendVerificationLevel3Notification($documentRequest, $authority, $verificationLink),
            default => false
        };
    }

    public function sendVerificationApproved(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): bool {
        $level = $authority->getVerificationLevel();
        return $this->sendLevelApprovedNotification($documentRequest, $authority, $level);
    }

    public function sendVerificationRejected(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $reason
    ): bool {
        $level = $authority->getVerificationLevel();
        return $this->sendLevelRejectedNotification($documentRequest, $authority, $level, $reason);
    }

    public function sendVerificationApprovedToAdmin(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): bool {
        $level = $authority->getVerificationLevel();
        return $this->sendLevelApprovedToAdmin($documentRequest, $authority, $level);
    }

    public function sendVerificationRejectedToAdmin(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $reason
    ): bool {
        return $this->sendLevelRejectedNotification($documentRequest, $authority, $authority->getVerificationLevel(), $reason);
    }

    public function sendVerificationInProgress(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority
    ): bool {
        $level = $authority->getVerificationLevel();

        $message = "ℹ️ *SIPETER - UPDATE DOKUMEN*\n\n";
        $message .= "Halo {$documentRequest->applicant_name},\n\n";
        $message .= "Dokumen Anda sedang dalam proses verifikasi Level {$level}:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👔 *Verifikator:* {$authority->name}\n";
        $message .= "⏳ *Status:* Level {$level}/3 sedang diverifikasi\n\n";

        if ($documentRequest->isGuestRequest()) {
            $message .= "🔗 Track: " . route('mahasiswa.tracking.detail', $documentRequest->request_code) . "\n\n";
        } else {
            $message .= "🔗 Dashboard: " . route('internal.my-documents.index') . "\n\n";
        }

        $message .= "Mohon menunggu. Terima kasih! 🙏";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'verification_in_progress'
        );
    }

    public function notifyDocumentSubmitted(DocumentRequest $documentRequest): bool
    {
        $message = "✅ *SIPETER STABA*\n\n";
        $message .= "*Request Dokumen Berhasil Dikirim!*\n\n";
        $message .= "📄 *Kode Tracking:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis Dokumen:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Nama:* {$documentRequest->applicant_name}\n";
        $message .= "🆔 *NIM/NIP:* {$documentRequest->applicant_identifier}\n";
        $message .= "📅 *Tanggal:* " . now()->format('d M Y, H:i') . " WIB\n\n";

        if ($documentRequest->purpose) {
            $message .= "📝 *Keperluan:* {$documentRequest->purpose}\n\n";
        }

        $message .= "⏳ *Status:* Menunggu persetujuan admin\n\n";
        $message .= "📲 *Track Status:*\n";

        if ($documentRequest->isGuestRequest()) {
            $message .= route('mahasiswa.tracking.detail', $documentRequest->request_code) . "\n\n";
            $message .= "💡 Simpan kode tracking Anda dengan baik!\n\n";
        } else {
            $message .= route('internal.my-documents.index') . "\n\n";
            $message .= "💡 Login ke dashboard untuk melihat status!\n\n";
        }

        $message .= "Terima kasih telah menggunakan SIPETER! 🙏\n\n";
        $message .= "---\n";
        $message .= "_SIPETER - STABA Bandung_";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'request_submitted'
        );
    }

    public function notifyDocumentApproved(DocumentRequest $documentRequest): bool
    {
        $message = "✅ *SIPETER STABA*\n\n";
        $message .= "*Dokumen Anda Telah Disetujui!*\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Dokumen:* {$documentRequest->documentType->name}\n";
        $message .= "✅ *Status:* Disetujui oleh Admin\n\n";
        $message .= "Selanjutnya dokumen akan diproses lebih lanjut (Verifikasi 3 Level).\n\n";
        $message .= "📲 *Track Status:*\n";

        if ($documentRequest->isGuestRequest()) {
            $message .= route('mahasiswa.tracking.detail', $documentRequest->request_code) . "\n\n";
        } else {
            $message .= route('internal.my-documents.index') . "\n\n";
        }

        $message .= "Terima kasih! 🙏";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'request_approved'
        );
    }

    public function notifyDocumentRejected(DocumentRequest $documentRequest, string $reason): bool
    {
        $message = "❌ *SIPETER STABA*\n\n";
        $message .= "*Mohon Maaf, Dokumen Anda Ditolak*\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Dokumen:* {$documentRequest->documentType->name}\n";
        $message .= "❌ *Status:* Ditolak\n\n";
        $message .= "📝 *Alasan:*\n{$reason}\n\n";
        $message .= "💡 Silakan hubungi bagian akademik untuk info lebih lanjut.\n\n";
        $message .= "📞 *Kontak:*\n";
        $message .= "Telp: " . config('services.staba.phone') . "\n";
        $message .= "Email: " . config('services.staba.email');

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'request_rejected'
        );
    }

    public function notifyDocumentReadyForPickup(DocumentRequest $documentRequest): bool
    {
        $message = "🎉 *SIPETER STABA*\n\n";
        $message .= "*Dokumen Anda Sudah Siap Diambil!*\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Dokumen:* {$documentRequest->documentType->name}\n";
        $message .= "🏢 *Status:* Siap Pickup\n\n";
        $message .= "📍 *Lokasi Pengambilan:*\nTata Usaha STABA Bandung\n\n";
        $message .= "🕐 *Jam Pelayanan:*\nSenin - Jumat: 08:00 - 15:00 WIB\n\n";
        $message .= "⚠️ *Jangan lupa bawa:*\n- KTM / Identitas\n- Kode tracking: {$documentRequest->request_code}\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'document_ready'
        );
    }

    public function notifyDocumentReadyForDownload(DocumentRequest $documentRequest): bool
    {
        $message = "🎉 *SIPETER STABA*\n\n";
        $message .= "*Dokumen Anda Sudah Siap Diunduh!*\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Dokumen:* {$documentRequest->documentType->name}\n";
        $message .= "📥 *Status:* Siap Download\n\n";
        $message .= "🔗 *Download Sekarang:*\n";

        if ($documentRequest->isGuestRequest()) {
            $message .= route('mahasiswa.tracking.detail', $documentRequest->request_code) . "\n\n";
            $message .= "💡 Masukkan kode tracking Anda untuk download.\n";
        } else {
            $message .= route('internal.my-documents.index') . "\n\n";
            $message .= "💡 Login dan download dokumen Anda sekarang!\n";
        }

        $message .= "\n📌 *File:* {$documentRequest->file_name}\n";
        $message .= "📅 *Tersedia:* " . now()->format('d M Y, H:i') . " WIB\n\n";
        $message .= "⚠️ PENTING: Jangan lupa klik 'Konfirmasi Sudah Diterima' setelah download!\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'document_ready'
        );
    }

    public function notifyDocumentCompleted(DocumentRequest $documentRequest): bool
    {
        $message = "✅ *SIPETER STABA*\n\n";
        $message .= "*Dokumen Telah Selesai!*\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Dokumen:* {$documentRequest->documentType->name}\n";
        $message .= "✅ *Status:* Selesai\n\n";
        $message .= "Terima kasih telah menggunakan SIPETER STABA!\n\n";
        $message .= "📞 *Butuh bantuan?*\nHubungi Bagian Akademik:\n";
        $message .= "📱 " . config('services.staba.phone') . "\n";
        $message .= "📧 " . config('services.staba.email') . "\n\n";
        $message .= "Semoga sukses! 🎓";

        return $this->sendWithLogging(
            $documentRequest->applicant_phone,
            $message,
            $documentRequest->id,
            $documentRequest->applicant_name,
            'user',
            'document_completed'
        );
    }

    public function notifySignatureRequested(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $uploadLink
    ): bool {
        $message = "✍️ *SIPETER - Request Tanda Tangan*\n\n";
        $message .= "Yth. {$authority->name}\n";
        $message .= "{$authority->position}\n\n";
        $message .= "Mohon upload tanda tangan digital + QR Code:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n\n";
        $message .= "🔗 *Upload TTD Sekarang:*\n{$uploadLink}\n\n";
        $message .= "⏰ *Link Expired:* 7 hari\n\n";
        $message .= "📝 *Yang Perlu Diupload:*\n1. File TTD Digital (PNG, transparan)\n2. File QR Code (PNG)\n\n";
        $message .= "⚠️ PENTING: Klik link di atas untuk upload, BUKAN reply WA ini!\n\n";
        $message .= "Terima kasih atas kerjasamanya. 🙏\n\n---\n_SIPETER - STABA Bandung_";

        return $this->sendWithLogging(
            $authority->phone,
            $message,
            $documentRequest->id,
            $authority->name,
            'authority',
            'signature_requested'
        );
    }

    public function notifyAdminNewRequest(DocumentRequest $documentRequest): bool
    {
        $adminPhones = config('services.whatsapp.admin_phones', ['6282129554934']);

        $applicantType = match($documentRequest->applicant_type->value) {
            'mahasiswa' => '🎓 Mahasiswa',
            'dosen' => '👨‍🏫 Dosen',
            'staff' => '👔 Staff',
            default => '👤 Umum'
        };

        $deliveryLabel = $documentRequest->delivery_method === 'download'
            ? '📥 Download Online'
            : '📦 Pickup Fisik';

        $message = "🔔 *SIPETER - REQUEST BARU!*\n\n";
        $message .= "Admin yang terhormat,\n\nAda pengajuan dokumen baru:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n";
        $message .= "🆔 *Identifier:* {$documentRequest->applicant_identifier}\n";
        $message .= "📱 *Tipe:* {$applicantType}\n";
        $message .= "🚚 *Pengambilan:* {$deliveryLabel}\n";
        $message .= "📅 *Waktu:* " . now()->timezone('Asia/Jakarta')->format('d M Y, H:i') . " WIB\n\n";

        if ($documentRequest->purpose) {
            $message .= "📝 *Keperluan:*\n{$documentRequest->purpose}\n\n";
        }

        $message .= "🔗 *Review Sekarang:*\n" . route('admin.documents.pending') . "\n\n";
        $message .= "Mohon segera ditindaklanjuti. 🙏\n\n---\n_SIPETER - STABA Bandung_";

        $success = false;
        foreach ($adminPhones as $phone) {
            if ($this->sendWithLogging($phone, $message, $documentRequest->id, 'Admin', 'admin', 'new_request')) {
                $success = true;
            }
        }
        return $success;
    }

    public function notifyAdminSignatureUploaded(DocumentRequest $documentRequest, SignatureAuthority $authority): bool
    {
        $adminPhones = config('services.whatsapp.admin_phones', ['6282129554934']);
        $message = "📤 *SIPETER - TTD Digital Diupload*\n\n";
        $message .= "Admin yang terhormat,\n\n{$authority->name} telah mengupload TTD digital untuk:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "👤 *Pemohon:* {$documentRequest->applicant_name}\n\n";
        $message .= "🔗 *Verifikasi Sekarang:*\n" . route('admin.signatures.verify.index') . "\n\n";
        $message .= "Mohon segera diverifikasi. 🙏";

        $success = false;
        foreach ($adminPhones as $phone) {
            if ($this->sendWithLogging($phone, $message, $documentRequest->id, 'Admin', 'admin', 'signature_uploaded')) {
                $success = true;
            }
        }
        return $success;
    }

    public function notifySignatureVerified(DocumentRequest $documentRequest, SignatureAuthority $authority): bool
    {
        $message = "✅ *SIPETER - TTD Digital Diverifikasi*\n\n";
        $message .= "Yth. {$authority->name}\n\nTTD digital Anda telah diverifikasi untuk:\n\n";
        $message .= "📄 *Kode:* {$documentRequest->request_code}\n";
        $message .= "📋 *Jenis:* {$documentRequest->documentType->name}\n";
        $message .= "✅ *Status:* Terverifikasi\n\nTerima kasih atas kerjasamanya! 🙏";

        return $this->sendWithLogging($authority->phone, $message, $documentRequest->id, $authority->name, 'authority', 'signature_verified');
    }

    public function isConnected(): bool
    {
        try {
            if ($this->useGateway) {
                $response = Http::timeout(5)->get("{$this->gatewayUrl}/status");
                return $response->successful();
            }
            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp connection check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
