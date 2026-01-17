<?php

namespace App\Listeners;

use App\Events\SignatureVerified;
use App\Services\{WhatsAppService, DocumentHistoryService};
use App\Enums\DocumentStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\{Log, DB};

class NotifyAllSignaturesVerified implements ShouldQueue
{
    public function __construct(
        protected WhatsAppService $whatsappService,
        protected DocumentHistoryService $historyService
    ) {}

    public function handle(SignatureVerified $event): void
    {
        try {
            $document = $event->documentRequest;
            $signature = $event->signature ?? $document->signatures()->where('signature_authority_id', $event->authority->id)->latest()->first();

            Log::info('🔍 Checking if all signatures verified', [
                'document_id' => $document->id,
                'document_code' => $document->request_code,
                'just_verified_level' => $signature?->signature_level,
                'current_status' => $document->status->value,
            ]);

            // 🔥 CHECK: Apakah semua 3 TTD sudah verified?
            $verifiedSignatures = $document->signatures()
                ->whereIn('signature_level', [1, 2, 3])
                ->where('status', 'verified')
                ->get();

            $verifiedCount = $verifiedSignatures->count();

            Log::info('📊 Signature verification status', [
                'document_id' => $document->id,
                'verified_count' => $verifiedCount,
                'required_count' => 3,
                'verified_levels' => $verifiedSignatures->pluck('signature_level')->toArray(),
            ]);

            // ❌ Kalau belum 3, skip
            if ($verifiedCount < 3) {
                Log::info('⏳ Not all signatures verified yet, skipping status update', [
                    'verified_count' => $verifiedCount,
                    'required_count' => 3,
                ]);
                return;
            }

            // ✅ SEMUA TTD VERIFIED! Update status
            DB::beginTransaction();

            $oldStatus = $document->status->value;

            $document->update([
                'status' => DocumentStatus::ALL_SIGNATURES_VERIFIED,
                'signature_completed_at' => now(),
            ]);

            Log::info('✅ ALL SIGNATURES VERIFIED - Status updated', [
                'document_id' => $document->id,
                'old_status' => $oldStatus,
                'new_status' => DocumentStatus::ALL_SIGNATURES_VERIFIED->value,
            ]);

            $this->historyService->logSignatureCompleted($document);
            $this->sendUserNotification($document);
            $this->sendAdminNotification($document);

            DB::commit();

            Log::info('🎉 All signatures verification process completed successfully', [
                'document_id' => $document->id,
                'document_code' => $document->request_code,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Failed to process all signatures verified', [
                'document_id' => $event->documentRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function sendUserNotification($document): void
    {
        try {
            $deliveryMethod = $document->delivery_method->value;
            $isPickup = $deliveryMethod === 'pickup';

            $message = "🎉 *SIPETER - SEMUA TTD SELESAI!*\n\n";
            $message .= "Halo {$document->applicant_name},\n\n";
            $message .= "Kabar baik! Semua tanda tangan digital (3 Level) telah selesai diverifikasi:\n\n";
            $message .= "📄 *Kode:* {$document->request_code}\n";
            $message .= "📋 *Jenis:* {$document->documentType->name}\n\n";
            $message .= "✅ *Ditandatangani:*\n";
            $message .= "  • Level 1: Ketua Akademik\n";
            $message .= "  • Level 2: Wakil Direktur 3\n";
            $message .= "  • Level 3: Direktur\n\n";

            if ($isPickup) {
                $message .= "📦 *Status:* Sedang finalisasi untuk pickup fisik\n\n";
                $message .= "ℹ️ Anda akan menerima notifikasi saat dokumen siap diambil di Tata Usaha.\n\n";
            } else {
                $message .= "📥 *Status:* Sedang finalisasi untuk download\n\n";
                $message .= "ℹ️ Anda akan menerima notifikasi + link download saat dokumen final sudah diupload.\n\n";
            }

            if ($document->isGuestRequest()) {
                $message .= "🔗 Track: " . route('mahasiswa.tracking.detail', $document->request_code) . "\n\n";
            } else {
                $message .= "🔗 Dashboard: " . route('internal.my-documents.index') . "\n\n";
            }

            $message .= "Terima kasih atas kesabarannya! 🙏\n\n";
            $message .= "---\n";
            $message .= "_SIPETER - STABA Bandung_";

            $this->whatsappService->sendWithLogging(
                $document->applicant_phone,
                $message,
                $document->id,
                $document->applicant_name,
                'user',
                'all_signatures_verified'
            );

            Log::info('📲 User notification sent for all signatures verified', [
                'document_id' => $document->id,
                'phone' => $document->applicant_phone,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send user notification', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendAdminNotification($document): void
    {
        try {
            $adminPhones = config('services.whatsapp.admin_phones', ['6282129554934']);
            $deliveryMethod = $document->delivery_method->value;
            $isPickup = $deliveryMethod === 'pickup';

            $message = "🎉 *SIPETER - ALL SIGNATURES VERIFIED!*\n\n";
            $message .= "Admin yang terhormat,\n\n";
            $message .= "Semua TTD (3 Level) telah selesai diverifikasi untuk:\n\n";
            $message .= "📄 *Kode:* {$document->request_code}\n";
            $message .= "📋 *Jenis:* {$document->documentType->name}\n";
            $message .= "👤 *Pemohon:* {$document->applicant_name}\n";
            $message .= "🏆 *NIM/NIP:* {$document->applicant_identifier}\n\n";
            $message .= "✅ *Ditandatangani:*\n";
            $message .= "  • Level 1: Ketua Akademik ✅\n";
            $message .= "  • Level 2: Wakil Direktur 3 ✅\n";
            $message .= "  • Level 3: Direktur ✅\n\n";

            if ($isPickup) {
                $message .= "📦 *Metode:* Pickup Fisik\n\n";
                $message .= "⚡ *LANGKAH SELANJUTNYA:*\n";
                $message .= "1. Download semua TTD (3 file + 3 QR Code)\n";
                $message .= "2. Embed TTD ke PDF template (manual)\n";
                $message .= "3. Cetak dokumen fisik\n";
                $message .= "4. Klik \"Dokumen Siap Diambil\" di sistem\n\n";
            } else {
                $message .= "📥 *Metode:* Download Online\n\n";
                $message .= "⚡ *LANGKAH SELANJUTNYA:*\n";
                $message .= "1. Download semua TTD (3 file + 3 QR Code)\n";
                $message .= "2. Embed TTD ke PDF template (manual)\n";
                $message .= "3. Upload PDF final ke sistem\n";
                $message .= "4. User akan otomatis menerima link download\n\n";
            }

            $message .= "🔗 *Action:*\n" . route('admin.documents.show', $document->id) . "\n\n";
            $message .= "Silakan lanjutkan proses finalisasi! 🙏\n\n";
            $message .= "---\n";
            $message .= "_SIPETER - STABA Bandung_";

            foreach ($adminPhones as $phone) {
                $this->whatsappService->sendWithLogging(
                    $phone,
                    $message,
                    $document->id,
                    'Admin',
                    'admin',
                    'all_signatures_verified'
                );
            }

            Log::info('📲 Admin notification sent for all signatures verified', [
                'document_id' => $document->id,
                'admin_count' => count($adminPhones),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(SignatureVerified $event, \Throwable $exception): void
    {
        Log::error('❌ NotifyAllSignaturesVerified job failed', [
            'document_id' => $event->documentRequest->id,
            'document_code' => $event->documentRequest->request_code,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
