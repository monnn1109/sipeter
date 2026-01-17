<?php

namespace App\Services;

use App\Models\{DocumentRequest, DocumentActivity, SignatureAuthority};
use App\Enums\DocumentActivityType;
use Illuminate\Support\Facades\Auth;

class DocumentHistoryService
{
    protected function getActorInfo(): array
    {
        if (Auth::check()) {
            $user = Auth::user();
            return [
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_type' => $user->role->value,
            ];
        }

        return [
            'user_id' => null,
            'actor_name' => null,
            'actor_type' => 'mahasiswa',
        ];
    }

    protected function log(
        DocumentRequest $documentRequest,
        DocumentActivityType $activityType,
        ?string $description = null,
        ?array $metadata = null,
        ?string $statusFrom = null,
        ?string $statusTo = null
    ): DocumentActivity {
        $actorInfo = $this->getActorInfo();

        return DocumentActivity::create([
            'document_request_id' => $documentRequest->id,
            'user_id' => $actorInfo['user_id'],
            'actor_name' => $actorInfo['actor_name'],
            'actor_type' => $actorInfo['actor_type'],
            'activity_type' => $activityType,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'description' => $description ?? $activityType->label(),
            'metadata' => $metadata,
        ]);
    }

    public function logSubmitted(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::SUBMITTED,
            'Pengajuan dokumen dibuat',
            [
                'applicant_name' => $documentRequest->applicant_name,
                'document_type' => $documentRequest->documentType->name,
            ],
            null,
            'submitted'
        );
    }

    public function logApproved(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::APPROVED,
            'Pengajuan disetujui oleh admin',
            null,
            'pending',
            'approved'
        );
    }

    public function logRejected(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::REJECTED,
            'Pengajuan ditolak: ' . $documentRequest->rejection_reason,
            ['rejection_reason' => $documentRequest->rejection_reason],
            'pending',
            'rejected'
        );
    }

    public function logUploaded(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::FILE_UPLOADED,
            'File dokumen berhasil diupload',
            [
                'file_name' => $documentRequest->getFileName(),
                'file_size' => $documentRequest->file_size ?? 'N/A',
            ],
            'processing',
            'file_uploaded'
        );
    }

    // 🔥 NEW: Log Upload Dokumen Final (Sudah Ter-embed TTD)
    public function logFinalDocumentUploaded(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::FILE_UPLOADED,
            '📄 Dokumen final (sudah ter-embed 3 TTD) diupload oleh admin',
            [
                'file_name' => $documentRequest->getFileName(),
                'file_size' => $documentRequest->file_size ?? 'N/A',
                'upload_type' => 'final_document_with_signatures',
                'total_signatures_embedded' => 3,
                'uploaded_by' => Auth::user()->name ?? 'Admin',
            ],
            'signature_verified',
            'ready_for_pickup'
        );
    }

    public function logReady(DocumentRequest $documentRequest): DocumentActivity
    {
        $deliveryMethod = $documentRequest->delivery_method;
        $description = $deliveryMethod === 'pickup'
            ? 'Dokumen siap diambil'
            : 'Dokumen siap didownload';

        return $this->log(
            $documentRequest,
            DocumentActivityType::READY,
            $description,
            ['delivery_method' => $deliveryMethod],
            'processing',
            'ready_for_pickup'
        );
    }

    public function logDownloaded(DocumentRequest $documentRequest, array $metadata = []): DocumentActivity
    {
        $actorInfo = $this->getActorInfo();

        if ($actorInfo['user_id'] === null) {
            $actorInfo['actor_name'] = $documentRequest->applicant_name ?? 'Mahasiswa';
            $actorInfo['actor_type'] = 'mahasiswa';
        }

        $description = $actorInfo['user_id']
            ? "Dokumen didownload oleh {$actorInfo['actor_name']}"
            : "Dokumen didownload oleh mahasiswa";

        return DocumentActivity::create([
            'document_request_id' => $documentRequest->id,
            'user_id' => $actorInfo['user_id'],
            'actor_name' => $actorInfo['actor_name'],
            'actor_type' => $actorInfo['actor_type'],
            'activity_type' => DocumentActivityType::DOWNLOADED,
            'status_from' => 'ready_for_pickup',
            'status_to' => 'downloaded',
            'description' => $description,
            'metadata' => array_merge($metadata, [
                'downloaded_at' => now()->toDateTimeString(),
                'file_name' => $documentRequest->getFileName(),
            ]),
        ]);
    }

    public function logPickedUp(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::PICKED_UP,
            'Dokumen telah diambil',
            ['pickup_date' => now()->toDateString()],
            'ready_for_pickup',
            'picked_up'
        );
    }

    /**
     * 📦 NEW: Log Dokumen Pickup Fisik Ditandai "Sudah Diambil" oleh Admin
     */
    public function logDocumentPickedUp(DocumentRequest $documentRequest, ?string $notes = null): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::PICKED_UP,
            '📦 Dokumen pickup fisik ditandai "Sudah Diambil" oleh admin',
            [
                'pickup_date' => now()->toDateString(),
                'pickup_time' => now()->format('H:i'),
                'marked_by' => Auth::user()->name ?? 'Admin',
                'marked_by_id' => Auth::id(),
                'delivery_method' => 'pickup',
                'notes' => $notes,
            ],
            'ready_for_pickup',
            'completed'
        );
    }

    public function logCompleted(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::COMPLETED,
            'Proses dokumen selesai',
            ['completion_date' => now()->toDateString()],
            $documentRequest->status->value,
            'completed'
        );
    }

    public function logStatusChange(
        DocumentRequest $documentRequest,
        string $statusFrom,
        string $statusTo,
        ?string $notes = null
    ): DocumentActivity {
        $actorInfo = $this->getActorInfo();

        $statusLabels = [
            'submitted' => 'Submitted',
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'processing' => 'Diproses',
            'ready_for_pickup' => 'Siap Diambil',
            'ready' => 'Siap',
            'picked_up' => 'Sudah Diambil',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            'verification_requested' => 'Menunggu Verifikasi',
            'verification_approved' => 'Terverifikasi',
            'verification_rejected' => 'Verifikasi Ditolak',
            'waiting_signature' => 'Menunggu TTD',
            'signature_in_progress' => 'Proses TTD',
            'signature_completed' => 'TTD Selesai',
        ];

        $fromLabel = $statusLabels[$statusFrom] ?? $statusFrom;
        $toLabel = $statusLabels[$statusTo] ?? $statusTo;

        $description = "Status diubah dari '{$fromLabel}' menjadi '{$toLabel}'";

        if ($notes) {
            $description .= " - Catatan: {$notes}";
        }

        return DocumentActivity::create([
            'document_request_id' => $documentRequest->id,
            'user_id' => $actorInfo['user_id'],
            'actor_name' => $actorInfo['actor_name'],
            'actor_type' => $actorInfo['actor_type'],
            'activity_type' => DocumentActivityType::STATUS_UPDATED,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'description' => $description,
            'metadata' => [
                'changed_by' => $actorInfo['actor_name'],
                'changed_at' => now()->toDateTimeString(),
                'notes' => $notes,
            ],
        ]);
    }

    public function logCustomActivity(
        DocumentRequest $documentRequest,
        string $activityType,
        string $description,
        ?array $metadata = null
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::from($activityType),
            $description,
            $metadata
        );
    }

    public function logSignatureRequested(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level = 1 // 🔥 ADDED: Level parameter
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::SIGNATURE_REQUESTED,
            "✍️ TTD Level {$level} diminta ke {$authority->name} ({$authority->getAuthorityTypeLabel()})",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'signature_level' => $level,
                'requested_at' => now()->toDateTimeString(),
            ],
            'verification_approved',
            "signature_level_{$level}_requested"
        );
    }

    public function logSignatureUploaded(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $fileName,
        int $level = 1 // 🔥 ADDED
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::SIGNATURE_UPLOADED,
            "📤 TTD Level {$level} dari {$authority->name} telah diupload",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'signature_level' => $level,
                'file_name' => $fileName,
                'uploaded_at' => now()->toDateTimeString(),
            ],
            "signature_level_{$level}_requested",
            "signature_level_{$level}_uploaded"
        );
    }

    // 🔥 UPDATED: Make level required parameter
    public function logSignatureVerified(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level,
        ?string $notes = null
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::SIGNATURE_VERIFIED,
            "✅ TTD Level {$level} dari {$authority->name} diverifikasi oleh admin",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'signature_level' => $level,
                'verified_at' => now()->toDateTimeString(),
                'verified_by' => Auth::user()->name ?? 'Admin',
                'verification_notes' => $notes,
            ],
            "signature_level_{$level}_uploaded",
            "signature_level_{$level}_verified"
        );
    }

    public function logSignatureRejected(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $reason,
        int $level = 1 // 🔥 ADDED
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::SIGNATURE_REJECTED,
            "❌ TTD Level {$level} dari {$authority->name} ditolak oleh admin",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'signature_level' => $level,
                'rejection_reason' => $reason,
                'rejected_at' => now()->toDateTimeString(),
                'rejected_by' => Auth::user()->name ?? 'Admin',
            ],
            "signature_level_{$level}_uploaded",
            "signature_level_{$level}_rejected"
        );
    }

    public function logSignatureCompleted(DocumentRequest $documentRequest): DocumentActivity
    {
        return $this->log(
            $documentRequest,
            DocumentActivityType::SIGNATURE_COMPLETED,
            '🎉 Semua TTD digital (Level 1, 2, 3) telah selesai dan diverifikasi',
            [
                'total_signatures' => 3,
                'completed_at' => now()->toDateTimeString(),
            ],
            'signature_level_3_verified',
            'all_signatures_verified'
        );
    }

    public function logMarkedAsTaken(
        DocumentRequest $documentRequest,
        string $markedBy,
        ?string $notes = null
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::MARKED_AS_TAKEN,
            "Dokumen ditandai sudah diambil oleh {$markedBy}",
            [
                'marked_by' => $markedBy,
                'marked_by_role' => $documentRequest->marked_by_role,
                'marked_at' => now()->toDateTimeString(),
                'notes' => $notes,
            ],
            'ready_for_pickup',
            'completed'
        );
    }

    public function logVerificationRequested(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level = 1 // 🔥 ADDED
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::VERIFICATION_REQUESTED,
            "📝 Verifikasi Level {$level} diminta ke {$authority->name} ({$authority->getAuthorityTypeLabel()})",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'verification_level' => $level,
                'requested_at' => now()->toDateTimeString(),
            ],
            'approved',
            "verification_level_{$level}_requested"
        );
    }

    public function logVerificationApproved(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        int $level = 1 
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::VERIFICATION_APPROVED,
            "✅ Verifikasi Level {$level} disetujui oleh {$authority->name}",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'verification_level' => $level,
                'approved_at' => now()->toDateTimeString(),
            ],
            "verification_level_{$level}_requested",
            "verification_level_{$level}_approved"
        );
    }

    public function logVerificationRejected(
        DocumentRequest $documentRequest,
        SignatureAuthority $authority,
        string $reason,
        int $level = 1
    ): DocumentActivity {
        return $this->log(
            $documentRequest,
            DocumentActivityType::VERIFICATION_REJECTED,
            "❌ Verifikasi Level {$level} ditolak oleh {$authority->name}: {$reason}",
            [
                'authority_id' => $authority->id,
                'authority_name' => $authority->name,
                'authority_type' => $authority->authority_type->value,
                'verification_level' => $level,
                'rejection_reason' => $reason,
                'rejected_at' => now()->toDateTimeString(),
            ],
            "verification_level_{$level}_requested",
            'verification_rejected'
        );
    }
}
