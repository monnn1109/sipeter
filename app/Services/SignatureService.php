<?php

namespace App\Services;

use App\Models\{DocumentRequest, DocumentSignature, SignatureAuthority};
use App\Enums\{SignatureStatus, DocumentStatus};
use App\Events\{SignatureRequested, SignatureVerified};
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Str;

class SignatureService
{
    /**
     * ✅ FIXED: Proceed to next signature level (auto-triggered after verify)
     *
     * Flow:
     * - Verify Level 1 → Call this → Request Level 2
     * - Verify Level 2 → Call this → Request Level 3
     * - Verify Level 3 → Do nothing (final)
     */
    public function proceedToNextLevel(DocumentRequest $document, int $currentLevel): array
    {
        try {
            if ($currentLevel >= 3) {
                return [
                    'success' => false,
                    'message' => 'Sudah di level final!'
                ];
            }

            $nextLevel = $currentLevel + 1;

            Log::info('Proceeding to next signature level', [
                'document_id' => $document->id,
                'current_level' => $currentLevel,
                'next_level' => $nextLevel,
            ]);

            DB::beginTransaction();

            // ✅ CHECK: Apakah sudah ada signature untuk next level?
            $existingNextLevel = DocumentSignature::forDocumentLevel($document->id, $nextLevel)
                ->whereIn('status', [SignatureStatus::REQUESTED, SignatureStatus::UPLOADED])
                ->first();

            if ($existingNextLevel) {
                DB::rollBack();

                Log::warning('Next level signature already exists', [
                    'next_level' => $nextLevel,
                    'signature_id' => $existingNextLevel->id,
                    'status' => $existingNextLevel->status->value,
                ]);

                return [
                    'success' => false,
                    'message' => "Request TTD Level {$nextLevel} sudah ada!"
                ];
            }

            // ✅ GET: Authority untuk next level
            $authority = SignatureAuthority::getActiveByLevel($nextLevel);

            if (!$authority) {
                DB::rollBack();

                Log::error('Authority not found for level', [
                    'level' => $nextLevel,
                ]);

                return [
                    'success' => false,
                    'message' => "Pejabat Level {$nextLevel} tidak ditemukan atau tidak aktif!"
                ];
            }

            // ✅ CREATE: Signature request untuk next level
            $token = Str::random(64);
            $tokenExpiry = now()->addDays(7);

            $signature = DocumentSignature::create([
                'document_request_id' => $document->id,
                'signature_authority_id' => $authority->id,
                'signature_level' => $nextLevel,
                'status' => SignatureStatus::REQUESTED,
                'requested_at' => now(),
                'metadata' => [
                    'token' => $token,
                    'token_expires_at' => $tokenExpiry->toDateTimeString(),
                    'auto_triggered_from_level' => $currentLevel,
                ]
            ]);

            Log::info('Next level signature created', [
                'signature_id' => $signature->id,
                'level' => $nextLevel,
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
            ]);

            // ✅ UPDATE: Document current_signature_step (akan diupdate di controller setelah verify)
            // Tidak perlu update disini karena sudah diupdate di SignatureVerificationController

            // ✅ FIRE: Event untuk kirim WA
            $uploadLink = route('signature.upload.show', $token);

            event(new SignatureRequested($signature, $document, $authority, $uploadLink));

            Log::info('SignatureRequested event fired', [
                'level' => $nextLevel,
                'upload_link' => $uploadLink,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Otomatis lanjut ke Level {$nextLevel}!",
                'signature' => $signature,
                'upload_link' => $uploadLink,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to proceed to next level', [
                'document_id' => $document->id,
                'current_level' => $currentLevel,
                'next_level' => $nextLevel ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => "Gagal lanjut ke Level {$nextLevel}: " . $e->getMessage()
            ];
        }
    }

    public function verifySignature(DocumentSignature $signature, $admin, ?string $notes = null): array
    {
        try {
            DB::beginTransaction();

            $signature->markAsVerified($admin, $notes);

            $document = $signature->documentRequest;
            $level = $signature->signature_level;

            event(new SignatureVerified($signature, $document, $signature->signatureAuthority, $admin));

            if ($level >= 3) {
                $document->update([
                    'status' => DocumentStatus::SIGNATURE_VERIFIED,
                ]);

                DB::commit();

                Log::info('All signature levels completed', [
                    'document_id' => $document->id,
                ]);

                return [
                    'success' => true,
                    'message' => '🎉 Semua TTD selesai! Dokumen siap untuk finalisasi.',
                    'all_complete' => true,
                ];
            }

            $nextLevelResult = $this->proceedToNextLevel($document, $level);

            DB::commit();

            if ($nextLevelResult['success']) {
                return [
                    'success' => true,
                    'message' => "✅ Level {$level} verified! Otomatis lanjut ke Level " . ($level + 1),
                    'next_level' => $level + 1,
                    'all_complete' => false,
                ];
            }

            return $nextLevelResult;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to verify signature', [
                'signature_id' => $signature->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal verifikasi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Reject signature
     */
    public function rejectSignature(DocumentSignature $signature, $admin, string $reason): array
    {
        try {
            DB::beginTransaction();

            $signature->markAsRejected($admin, $reason);

            DB::commit();

            Log::info('Signature rejected', [
                'signature_id' => $signature->id,
                'level' => $signature->signature_level,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => '❌ TTD ditolak. Pejabat akan diminta upload ulang.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal menolak: ' . $e->getMessage()
            ];
        }
    }
}
