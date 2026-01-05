<?php

namespace App\Models;

use App\Enums\SignatureStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentSignature extends Model
{
    protected $fillable = [
        'document_request_id',
        'signature_authority_id',
        'signature_level',             
        'signature_file',
        'file_type',
        'file_size',
        'status',
        'requested_at',
        'uploaded_at',
        'verified_at',
        'rejected_at',
        'verified_by',
        'verification_notes',
        'rejection_reason',
        'uploaded_from',
        'metadata',
    ];

    protected $casts = [
        'status' => SignatureStatus::class,
        'signature_level' => 'integer',
        'requested_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function signatureAuthority(): BelongsTo
    {
        return $this->belongsTo(SignatureAuthority::class);
    }

    public function authority(): BelongsTo
    {
        return $this->signatureAuthority();
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeRequested($query)
    {
        return $query->where('status', SignatureStatus::REQUESTED);
    }

    public function scopeUploaded($query)
    {
        return $query->where('status', SignatureStatus::UPLOADED);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', SignatureStatus::VERIFIED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', SignatureStatus::REJECTED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            SignatureStatus::REQUESTED,
            SignatureStatus::UPLOADED
        ]);
    }

    public function scopeForAuthority($query, $authorityId)
    {
        return $query->where('signature_authority_id', $authorityId);
    }

    public function scopeForDocument($query, $documentRequestId)
    {
        return $query->where('document_request_id', $documentRequestId);
    }

    public function scopeByLevel($query, int $level)
    {
        return $query->where('signature_level', $level);
    }

    public function scopeLevel1($query)
    {
        return $query->where('signature_level', 1);
    }

    public function scopeLevel2($query)
    {
        return $query->where('signature_level', 2);
    }

    public function scopeLevel3($query)
    {
        return $query->where('signature_level', 3);
    }

    public function scopeForDocumentLevel($query, int $documentRequestId, int $level)
    {
        return $query->where('document_request_id', $documentRequestId)
                     ->where('signature_level', $level);
    }

    public function getSignatureUrlAttribute(): ?string
    {
        if (!$this->signature_file) {
            return null;
        }

        return Storage::disk('signatures')->url($this->signature_file);
    }

    public function getSignaturePathAttribute(): ?string
    {
        if (!$this->signature_file) {
            return null;
        }

        return Storage::disk('signatures')->path($this->signature_file);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) {
            return '0 KB';
        }

        $kb = $this->file_size / 1024;

        if ($kb < 1024) {
            return round($kb, 2) . ' KB';
        }

        return round($kb / 1024, 2) . ' MB';
    }

    public function isRequested(): bool
    {
        return $this->status === SignatureStatus::REQUESTED;
    }

    public function isUploaded(): bool
    {
        return $this->status === SignatureStatus::UPLOADED;
    }

    public function isVerified(): bool
    {
        return $this->status === SignatureStatus::VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status === SignatureStatus::REJECTED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            SignatureStatus::REQUESTED,
            SignatureStatus::UPLOADED
        ]);
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    public function canBeUploaded(): bool
    {
        return $this->status->canBeUploaded();
    }

    public function canBeVerified(): bool
    {
        return $this->status->canBeVerified();
    }

    public function getLevel(): int
    {
        return $this->signature_level ?? 1;
    }

    public function isLevel1(): bool
    {
        return $this->signature_level === 1;
    }

    public function isLevel2(): bool
    {
        return $this->signature_level === 2;
    }

    public function isLevel3(): bool
    {
        return $this->signature_level === 3;
    }

    public function isFinalLevel(): bool
    {
        return $this->signature_level === 3;
    }

    public function getNextLevel(): ?int
    {
        if ($this->signature_level >= 3) {
            return null;
        }
        return $this->signature_level + 1;
    }

    public function getLevelLabel(): string
    {
        return match($this->signature_level) {
            1 => 'Level 1 - Ketua Akademik',
            2 => 'Level 2 - Wakil Ketua 3',
            3 => 'Level 3 - Direktur (Final)',
            default => 'Unknown Level'
        };
    }

    public function getShortLevelLabel(): string
    {
        return "Level {$this->signature_level}/3";
    }

    public function getProgressPercentage(): int
    {
        return round(($this->signature_level / 3) * 100);
    }

    public function canProceedToNextLevel(): bool
    {
        return $this->isVerified() && !$this->isFinalLevel();
    }

    public function markAsUploaded(string $filePath, string $fileType, int $fileSize, ?string $uploadedFrom = 'manual'): void
    {
        $this->update([
            'signature_file' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'uploaded_from' => $uploadedFrom,
            'status' => SignatureStatus::UPLOADED,
            'uploaded_at' => now(),
        ]);
    }

    public function markAsVerified(User $admin, ?string $notes = null): void
    {
        $this->update([
            'status' => SignatureStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    public function markAsRejected(User $admin, string $reason): void
    {
        $this->update([
            'status' => SignatureStatus::REJECTED,
            'verified_by' => $admin->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function resetToRequested(): void
    {
        if ($this->signature_file) {
            Storage::disk('signatures')->delete($this->signature_file);
        }

        $this->update([
            'signature_file' => null,
            'file_type' => null,
            'file_size' => null,
            'status' => SignatureStatus::REQUESTED,
            'uploaded_at' => null,
            'verified_at' => null,
            'rejected_at' => null,
            'verified_by' => null,
            'verification_notes' => null,
            'rejection_reason' => null,
        ]);
    }

    public function deleteSignatureFile(): bool
    {
        if (!$this->signature_file) {
            return false;
        }

        return Storage::disk('signatures')->delete($this->signature_file);
    }

    public function hasSignatureFile(): bool
    {
        return !empty($this->signature_file) &&
               Storage::disk('signatures')->exists($this->signature_file);
    }

    public function getDaysWaiting(): int
    {
        if ($this->isVerified() || $this->isRejected()) {
            $endDate = $this->verified_at ?? $this->rejected_at;
            return $this->requested_at->diffInDays($endDate);
        }

        return $this->requested_at?->diffInDays(now()) ?? 0;
    }

    public function getHoursWaiting(): int
    {
        if ($this->isVerified() || $this->isRejected()) {
            $endDate = $this->verified_at ?? $this->rejected_at;
            return $this->requested_at->diffInHours($endDate);
        }

        return $this->requested_at?->diffInHours(now()) ?? 0;
    }

    public static function getAllLevelsForDocument(int $documentRequestId)
    {
        return self::where('document_request_id', $documentRequestId)
                   ->orderBy('signature_level')
                   ->get()
                   ->keyBy('signature_level');
    }

    public static function areAllLevelsCompleted(int $documentRequestId): bool
    {
        $verifiedCount = self::where('document_request_id', $documentRequestId)
                             ->verified()
                             ->count();

        return $verifiedCount === 3;
    }

    public static function areAllLevelsVerified(int $documentRequestId): bool
    {
        return self::areAllLevelsCompleted($documentRequestId);
    }

    public static function getCurrentLevel(int $documentRequestId): int
    {
        $lastVerified = self::where('document_request_id', $documentRequestId)
                            ->verified()
                            ->orderBy('signature_level', 'desc')
                            ->first();

        if (!$lastVerified) {
            return 1;
        }

        if ($lastVerified->signature_level >= 3) {
            return 3;
        }

        return $lastVerified->signature_level + 1;
    }

    public static function getProgressForDocument(int $documentRequestId): array
    {
        $signatures = self::getAllLevelsForDocument($documentRequestId);

        $verifiedCount = $signatures->filter(fn($sig) => $sig->isVerified())->count();
        $percentage = round(($verifiedCount / 3) * 100);

        return [
            'total_levels' => 3,
            'completed_levels' => $verifiedCount,
            'percentage' => $percentage,
            'current_level' => self::getCurrentLevel($documentRequestId),
            'is_complete' => $verifiedCount === 3,
            'signatures' => $signatures,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($signature) {
            if (!$signature->requested_at) {
                $signature->requested_at = now();
            }

            if (!$signature->signature_level) {
                $signature->signature_level = 1;
            }
        });

        static::deleting(function ($signature) {
            $signature->deleteSignatureFile();
        });
    }
}
