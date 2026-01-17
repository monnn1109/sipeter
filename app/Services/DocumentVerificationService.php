<?php

namespace App\Services;

use App\Models\{DocumentRequest, DocumentVerification, SignatureAuthority, DocumentActivity};
use App\Events\{DocumentVerificationRequested, DocumentVerificationApproved, DocumentVerificationRejected};
use App\Enums\{DocumentStatus, VerificationLevel};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DocumentVerificationService
{
    /**
     * ✅ CRITICAL: Send verification to specific level (1, 2, or 3)
     * Called by Admin or auto-triggered after previous level approved
     */
    public function sendToLevel(DocumentRequest $document, int $level): DocumentVerification
    {
        // ✅ Use config for total levels validation
        $totalLevels = config('services.verification.total_levels', 3);

        if ($level < 1 || $level > $totalLevels) {
            throw new \InvalidArgumentException("Invalid verification level: {$level}. Must be 1-{$totalLevels}.");
        }

        $authority = SignatureAuthority::getActiveByLevel($level);

        if (!$authority) {
            throw new \Exception("Tidak ada pejabat aktif untuk Level {$level}");
        }

        // ✅ Use config for token settings
        $tokenLength = config('services.verification.token_length', 64);
        $expiryDays = config('services.verification.token_expiry_days', 3);
        $token = Str::random($tokenLength);

        $verification = DocumentVerification::create([
            'document_request_id' => $document->id,
            'authority_id' => $authority->id,
            'token' => $token,
            'type' => 'document_verification',
            'status' => 'pending',
            'verification_level' => $level,
            'expires_at' => now()->addDays($expiryDays),
            'sent_at' => now()
        ]);

        $newStatus = match($level) {
            1 => DocumentStatus::VERIFICATION_STEP_1_REQUESTED,
            2 => DocumentStatus::VERIFICATION_STEP_2_REQUESTED,
            3 => DocumentStatus::VERIFICATION_STEP_3_REQUESTED,
        };

        $document->update([
            'status' => $newStatus,
            'current_verification_step' => $level,
            'verification_authority_id' => $authority->id
        ]);

        DocumentActivity::create([
            'document_request_id' => $document->id,
            'activity_type' => 'status_updated',
            'description' => "Request verifikasi Level {$level} dikirim ke {$authority->name} ({$authority->position})",
            'user_id' => auth()->id(),
            'actor_name' => auth()->user()?->name ?? 'System',
            'actor_type' => auth()->check() ? auth()->user()->role : 'system',
            'status_from' => $level === 1 ? 'approved' : "verification_step_" . ($level - 1) . "_approved",
            'status_to' => $newStatus->value,
        ]);

        $verificationLink = route('verification.show', $token);

        event(new DocumentVerificationRequested($document, $authority, $verificationLink));

        Log::info('Verification request sent', [
            'document_code' => $document->request_code,
            'level' => $level,
            'authority' => $authority->name,
            'token' => substr($token, 0, 10) . '...',
        ]);

        return $verification;
    }

    /**
     * ✅ CRITICAL: Approve a verification level
     * 🔥 FIXED: Removed duplicate proceedToNextLevel() call - now handled ONLY by Listener
     */
    public function approveLevel(DocumentVerification $verification, ?string $notes = null): void
    {
        $level = $verification->verification_level;
        $document = $verification->documentRequest;

        $verification->update([
            'status' => 'approved',
            'decision' => 'approved',
            'notes' => $notes,
            'verified_at' => now()
        ]);

        $newStatus = match($level) {
            1 => DocumentStatus::VERIFICATION_STEP_1_APPROVED,
            2 => DocumentStatus::VERIFICATION_STEP_2_APPROVED,
            3 => DocumentStatus::VERIFICATION_STEP_3_APPROVED,
        };

        $document->update([
            'status' => $newStatus,
            'current_verification_step' => $level
        ]);

        DocumentActivity::create([
            'document_request_id' => $document->id,
            'activity_type' => 'approved',
            'description' => "Verifikasi Level {$level} disetujui oleh {$verification->authority->name}",
            'metadata' => json_encode(['notes' => $notes, 'level' => $level]),
            'actor_name' => $verification->authority->name,
            'actor_type' => 'pejabat',
            'status_from' => "verification_step_{$level}_requested",
            'status_to' => $newStatus->value,
        ]);

        Log::info('Verification level approved', [
            'document_code' => $document->request_code,
            'level' => $level,
            'authority' => $verification->authority->name,
            'notes' => $notes,
        ]);

        // 🔥 FIRE EVENT - Auto-progression will be handled by NotifyAdminVerificationApproved listener
        event(new DocumentVerificationApproved($document, $verification->authority, $notes));

        // ❌ REMOVED: Duplicate call that caused the stuck issue
        // if ($level < $totalLevels && $autoProceed) {
        //     $this->proceedToNextLevel($document);
        // }
    }

    /**
     * ✅ CRITICAL: Reject a verification level
     * 🔥 FIXED: Removed $verification->authority from event (causes TypeError)
     * Stops the entire verification process
     */
    public function rejectLevel(DocumentVerification $verification, string $reason): void
    {
        $level = $verification->verification_level;
        $document = $verification->documentRequest;

        $verification->update([
            'status' => 'rejected',
            'decision' => 'rejected',
            'notes' => $reason,
            'verified_at' => now()
        ]);

        $document->update([
            'status' => DocumentStatus::VERIFICATION_REJECTED,
            'rejection_reason' => "Ditolak di Level {$level}: {$reason}"
        ]);

        DocumentActivity::create([
            'document_request_id' => $document->id,
            'activity_type' => 'rejected',
            'description' => "Verifikasi Level {$level} ditolak oleh {$verification->authority->name}",
            'metadata' => json_encode(['reason' => $reason, 'level' => $level]),
            'actor_name' => $verification->authority->name,
            'actor_type' => 'pejabat',
            'status_from' => "verification_step_{$level}_requested",
            'status_to' => 'verification_rejected',
        ]);

        Log::warning('Verification level rejected', [
            'document_code' => $document->request_code,
            'level' => $level,
            'authority' => $verification->authority->name,
            'reason' => $reason,
        ]);

        // 🔥 FIXED: Removed $verification->authority parameter (listener will get it via $verification->authority)
        event(new DocumentVerificationRejected($document, $verification, $reason));
    }

    public function proceedToNextLevel(DocumentRequest $document): void
    {
        $currentLevel = $document->getCurrentVerificationLevel();
        $nextLevel = $currentLevel + 1;
        $totalLevels = config('services.verification.total_levels', 3);

        Log::info('Attempting to proceed to next level', [
            'document_code' => $document->request_code,
            'current_level' => $currentLevel,
            'next_level' => $nextLevel,
            'total_levels' => $totalLevels,
        ]);

        if ($nextLevel > $totalLevels) {
            Log::info('All verification levels completed - no next level', [
                'document_code' => $document->request_code,
                'current_level' => $currentLevel,
            ]);
            return;
        }

        // 🔥 Check if next level verification already exists (prevent duplicate)
        $existingVerification = DocumentVerification::where('document_request_id', $document->id)
            ->where('verification_level', $nextLevel)
            ->where('status', 'pending')
            ->first();

        if ($existingVerification) {
            Log::warning('Next level verification already exists - skipping', [
                'document_code' => $document->request_code,
                'next_level' => $nextLevel,
                'existing_verification_id' => $existingVerification->id,
            ]);
            return;
        }

        try {
            $this->sendToLevel($document, $nextLevel);

            Log::info('Successfully proceeded to next level', [
                'document_code' => $document->request_code,
                'from_level' => $currentLevel,
                'to_level' => $nextLevel,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to proceed to next level', [
                'document_code' => $document->request_code,
                'current_level' => $currentLevel,
                'next_level' => $nextLevel,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    // ==================== QUERY & CHECK METHODS ====================

    public function getByToken(string $token): ?DocumentVerification
    {
        return DocumentVerification::with(['documentRequest.documentType', 'authority'])
            ->where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function hasPendingVerification(DocumentRequest $document): bool
    {
        return DocumentVerification::where('document_request_id', $document->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function hasPendingVerificationAtLevel(DocumentRequest $document, int $level): bool
    {
        return DocumentVerification::where('document_request_id', $document->id)
            ->where('verification_level', $level)
            ->where('status', 'pending')
            ->exists();
    }

    public function canStartVerification(DocumentRequest $document): bool
    {
        return $document->status === DocumentStatus::APPROVED
            && $document->getCurrentVerificationLevel() === 0
            && !$this->hasPendingVerification($document);
    }

    public function isAllLevelsCompleted(DocumentRequest $document): bool
    {
        return $document->isAllVerificationCompleted();
    }

    public function getCurrentLevel(DocumentRequest $document): int
    {
        return $document->getCurrentVerificationLevel();
    }

    public function canSendToNextLevel(DocumentRequest $document): bool
    {
        return $document->canProceedToNextLevel();
    }

    // ==================== ALIAS METHODS (for backward compatibility) ====================

    public function sendVerificationRequest(
        DocumentRequest $document,
        SignatureAuthority $authority
    ): DocumentVerification {
        $level = $authority->getVerificationLevel();
        return $this->sendToLevel($document, $level);
    }

    public function approveVerification(
        DocumentVerification $verification,
        ?string $notes = null
    ): void {
        $this->approveLevel($verification, $notes);
    }

    public function rejectVerification(
        DocumentVerification $verification,
        string $reason
    ): void {
        $this->rejectLevel($verification, $reason);
    }

    // ==================== ADMIN ACTIONS ====================

    public function resendVerificationRequest(DocumentVerification $verification): void
    {
        $verification->update([
            'sent_at' => now()
        ]);

        $verificationLink = route('verification.show', $verification->token);

        event(new DocumentVerificationRequested(
            $verification->documentRequest,
            $verification->authority,
            $verificationLink
        ));

        DocumentActivity::create([
            'document_request_id' => $verification->document_request_id,
            'activity_type' => 'status_updated',
            'description' => "Request verifikasi Level {$verification->verification_level} dikirim ulang ke {$verification->authority->name}",
            'user_id' => auth()->id(),
            'actor_name' => auth()->user()?->name ?? 'System',
            'actor_type' => auth()->check() ? auth()->user()->role : 'system',
        ]);
    }

    public function cancelVerification(DocumentVerification $verification): void
    {
        $level = $verification->verification_level;

        $verification->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'notes' => 'Dibatalkan oleh admin'
        ]);

        $previousStatus = $level === 1
            ? DocumentStatus::APPROVED
            : DocumentStatus::from("verification_step_" . ($level - 1) . "_approved");

        $verification->documentRequest->update([
            'status' => $previousStatus,
            'current_verification_step' => $level - 1
        ]);

        DocumentActivity::create([
            'document_request_id' => $verification->document_request_id,
            'activity_type' => 'status_updated',
            'description' => "Request verifikasi Level {$level} dibatalkan oleh admin",
            'user_id' => auth()->id(),
            'actor_name' => auth()->user()?->name,
            'actor_type' => auth()->user()?->role,
            'status_from' => "verification_step_{$level}_requested",
            'status_to' => $previousStatus->value,
        ]);
    }

    // ==================== MAINTENANCE & STATS ====================

    public function markExpiredVerifications(): int
    {
        $expiryDays = config('services.verification.token_expiry_days', 3);

        return DocumentVerification::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update([
                'status' => 'rejected',
                'notes' => "Link verifikasi telah kedaluwarsa ({$expiryDays} hari)"
            ]);
    }

    public function getStatsByLevel(): array
    {
        return DocumentVerification::getStatsByLevel();
    }

    public function getPendingCount(): int
    {
        return DocumentVerification::where('status', 'pending')
            ->where('expires_at', '>', now())
            ->count();
    }

    public function getPendingCountByLevel(int $level): int
    {
        return DocumentVerification::where('status', 'pending')
            ->where('verification_level', $level)
            ->where('expires_at', '>', now())
            ->count();
    }

    // ==================== HELPER METHODS ====================

    public function getAllVerifications(DocumentRequest $document)
    {
        return DocumentVerification::where('document_request_id', $document->id)
            ->with('authority')
            ->orderBy('verification_level')
            ->get();
    }

    public function getVerificationForLevel(DocumentRequest $document, int $level): ?DocumentVerification
    {
        return DocumentVerification::where('document_request_id', $document->id)
            ->where('verification_level', $level)
            ->first();
    }

    public function getCompletionPercentage(DocumentRequest $document): float
    {
        return $document->getVerificationProgress()['percentage'];
    }

    public function getNextAction(DocumentRequest $document): string
    {
        $currentLevel = $document->getCurrentVerificationLevel();
        $totalLevels = config('services.verification.total_levels', 3);

        if ($currentLevel === 0) {
            $level1Config = config('services.verification.levels.1');
            $levelName = $level1Config['name'] ?? 'Level 1 (Ketua Akademik)';
            return "Mulai verifikasi {$levelName}";
        }

        if ($currentLevel === $totalLevels && $document->isAllVerificationCompleted()) {
            return 'Semua verifikasi selesai - Lanjut ke TTD';
        }

        $pendingVerification = DocumentVerification::where('document_request_id', $document->id)
            ->where('verification_level', $currentLevel)
            ->where('status', 'pending')
            ->first();

        if ($pendingVerification) {
            return "Menunggu approval Level {$currentLevel}";
        }

        $nextLevel = $currentLevel + 1;
        if ($nextLevel <= $totalLevels) {
            $nextLevelConfig = config("services.verification.levels.{$nextLevel}");
            $nextLevelName = $nextLevelConfig['name'] ?? "Level {$nextLevel}";
            return "Siap lanjut ke {$nextLevelName}";
        }

        return "Proses verifikasi selesai";
    }

    // ==================== CONFIG ACCESS METHODS ====================

    public function getLevelConfig(int $level): ?array
    {
        return config("services.verification.levels.{$level}");
    }

    public function getAllLevelsConfig(): array
    {
        return config('services.verification.levels', []);
    }

    public function isLevelRequired(int $level): bool
    {
        $levelConfig = $this->getLevelConfig($level);
        return $levelConfig['required'] ?? true;
    }

    public function canSkipLevel(int $level): bool
    {
        $levelConfig = $this->getLevelConfig($level);
        return $levelConfig['can_skip'] ?? false;
    }

    public function isAutoProceedEnabled(): bool
    {
        return config('services.verification.auto_proceed_to_next_level', true);
    }

    public function getTotalLevels(): int
    {
        return config('services.verification.total_levels', 3);
    }

    public function getTokenExpiryDays(): int
    {
        return config('services.verification.token_expiry_days', 3);
    }
}
