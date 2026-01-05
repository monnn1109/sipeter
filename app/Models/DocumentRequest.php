<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\{DocumentStatus, ApplicantType, DeliveryMethod, VerificationLevel};

class DocumentRequest extends Model
{
    use HasFactory;

    protected $table = 'document_requests';

    protected $fillable = [
        'request_code',
        'document_type_id',
        'user_id',

        'applicant_type',
        'applicant_name',
        'applicant_identifier',
        'applicant_email',
        'applicant_phone',
        'applicant_unit',
        'applicant_address',

        'purpose',
        'notes',
        'delivery_method',

        'status',
        'admin_notes',
        'rejection_reason',

        'file_path',
        'file_name',
        'file_uploaded_at',
        'uploaded_by',

        'approved_at',
        'approved_by',
        'ready_at',
        'picked_up_at',
        'completed_at',

        'verification_authority_id',
        'verification_status',
        'verification_token',
        'verification_sent_at',
        'verification_responded_at',
        'verification_notes',
        'rejection_session',
        'current_verification_step',
        'current_signature_step',

        'requires_signature',
        'signature_status',
        'signature_requested_at',
        'signature_completed_at',
        'signature_requested_by',
        'signatures_required',
        'signatures_completed',

        'is_marked_as_taken',
        'marked_as_taken_at',
        'marked_as_taken_by',
        'marked_by_role',
        'taken_notes',
    ];

    protected $casts = [
        'status' => DocumentStatus::class,
        'applicant_type' => ApplicantType::class,
        'delivery_method' => DeliveryMethod::class,

        'approved_at' => 'datetime',
        'file_uploaded_at' => 'datetime',
        'ready_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'completed_at' => 'datetime',

        'verification_sent_at' => 'datetime',
        'verification_responded_at' => 'datetime',
        'current_verification_step' => 'integer',
        'current_signature_step' => 'integer',

        'requires_signature' => 'boolean',
        'signature_requested_at' => 'datetime',
        'signature_completed_at' => 'datetime',
        'signatures_required' => 'integer',
        'signatures_completed' => 'integer',

        'is_marked_as_taken' => 'boolean',
        'marked_as_taken_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verificationAuthority(): BelongsTo
    {
        return $this->belongsTo(SignatureAuthority::class, 'verification_authority_id');
    }

    public function verification(): HasOne
    {
        return $this->hasOne(DocumentVerification::class);
    }

    public function documentVerification(): HasOne
    {
        return $this->verification();
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(DocumentVerification::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }

    public function documentSignatures(): HasMany
    {
        return $this->signatures();
    }

    public function signatureToken(): HasOne
    {
        return $this->hasOne(SignatureToken::class);
    }

    public function signatureRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signature_requested_by');
    }

    public function pendingSignatures(): HasMany
    {
        return $this->signatures()->where('status', 'pending');
    }

    public function verifiedSignatures(): HasMany
    {
        return $this->signatures()->where('status', 'verified');
    }

    public function markedAsTakenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_as_taken_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DocumentActivity::class, 'document_request_id');
    }

    // ==================== VERIFICATION METHODS ====================

    public function getCurrentVerificationLevel(): int
    {
        return $this->current_verification_step ?? 0;
    }

    public function getCurrentVerificationLevelEnum(): VerificationLevel
    {
        return VerificationLevel::from($this->getCurrentVerificationLevel());
    }

    public function isVerificationInProgress(): bool
    {
        $level = $this->getCurrentVerificationLevel();
        return $level > 0 && $level < 3;
    }

    public function isAllVerificationCompleted(): bool
    {
        return $this->getCurrentVerificationLevel() === 3
            && $this->status === DocumentStatus::VERIFICATION_STEP_3_APPROVED;
    }

    public function canProceedToNextLevel(): bool
    {
        $currentLevel = $this->getCurrentVerificationLevel();

        if ($currentLevel >= 3) {
            return false;
        }

        $currentVerification = $this->verifications()
            ->where('verification_level', $currentLevel)
            ->where('status', 'approved')
            ->exists();

        return $currentVerification;
    }

    public function getNextVerificationAuthority(): ?SignatureAuthority
    {
        $nextLevel = $this->getCurrentVerificationLevel() + 1;

        if ($nextLevel > 3) {
            return null;
        }

        $verificationLevel = VerificationLevel::from($nextLevel);
        $authorityType = $verificationLevel->authorityType();

        return SignatureAuthority::where('authority_type', $authorityType)
            ->where('is_active', true)
            ->first();
    }

    public function getVerificationProgress(): array
    {
        $currentLevel = $this->getCurrentVerificationLevel();
        $percentage = 0;

        if ($currentLevel > 0) {
            $percentage = round(($currentLevel / 3) * 100);
        }

        $levels = [
            1 => [
                'name' => 'Level 1: Ketua Akademik',
                'status' => $this->getVerificationLevelStatus(1),
                'authority' => $this->getVerificationLevelAuthority(1),
                'verified_at' => $this->getVerificationLevelDate(1),
            ],
            2 => [
                'name' => 'Level 2: Wakil Ketua 3',
                'status' => $this->getVerificationLevelStatus(2),
                'authority' => $this->getVerificationLevelAuthority(2),
                'verified_at' => $this->getVerificationLevelDate(2),
            ],
            3 => [
                'name' => 'Level 3: Direktur',
                'status' => $this->getVerificationLevelStatus(3),
                'authority' => $this->getVerificationLevelAuthority(3),
                'verified_at' => $this->getVerificationLevelDate(3),
            ],
        ];

        return [
            'current_level' => $currentLevel,
            'percentage' => $percentage,
            'levels' => $levels,
            'is_completed' => $currentLevel === 3,
        ];
    }

    public function getVerificationLevelStatus(int $level): string
    {
        $verification = $this->verifications()
            ->where('verification_level', $level)
            ->first();

        if (!$verification) {
            return $this->getCurrentVerificationLevel() < $level ? 'locked' : 'pending';
        }

        return $verification->status;
    }

    public function getVerificationLevelAuthority(int $level): ?SignatureAuthority
    {
        $verification = $this->verifications()
            ->where('verification_level', $level)
            ->first();

        return $verification?->authority;
    }

    public function getVerificationLevelDate(int $level): ?string
    {
        $verification = $this->verifications()
            ->where('verification_level', $level)
            ->where('status', 'approved')
            ->first();

        return $verification?->verified_at?->format('d/m/Y H:i');
    }

    // ==================== ✅ NEW: SIGNATURE PROGRESS METHODS ====================

    /**
     * ✅ NEW: Get 3-level signature progress (similar to verification)
     * Returns: current_step, percentage, levels detail, is_completed
     */
    public function getSignatureProgress(): array
    {
        $currentStep = $this->current_signature_step ?? 0;

        $percentage = 0;
        if ($currentStep > 0) {
            $percentage = round(($currentStep / 3) * 100);
        }

        $levels = [
            1 => [
                'name' => 'Level 1: Ketua Akademik',
                'status' => $this->getSignatureLevelStatus(1),
                'authority' => $this->getSignatureLevelAuthority(1),
                'uploaded_at' => $this->getSignatureLevelUploadDate(1),
                'verified_at' => $this->getSignatureLevelVerifiedDate(1),
            ],
            2 => [
                'name' => 'Level 2: Wakil Ketua 3',
                'status' => $this->getSignatureLevelStatus(2),
                'authority' => $this->getSignatureLevelAuthority(2),
                'uploaded_at' => $this->getSignatureLevelUploadDate(2),
                'verified_at' => $this->getSignatureLevelVerifiedDate(2),
            ],
            3 => [
                'name' => 'Level 3: Direktur',
                'status' => $this->getSignatureLevelStatus(3),
                'authority' => $this->getSignatureLevelAuthority(3),
                'uploaded_at' => $this->getSignatureLevelUploadDate(3),
                'verified_at' => $this->getSignatureLevelVerifiedDate(3),
            ],
        ];

        return [
            'current_step' => $currentStep,
            'percentage' => $percentage,
            'levels' => $levels,
            'is_completed' => $currentStep === 3 && $this->areAllSignaturesVerified(),
        ];
    }

    /**
     * ✅ NEW: Get signature status for specific level
     */
    private function getSignatureLevelStatus(int $level): string
    {
        $signature = $this->signatures()
            ->where('signature_level', $level)
            ->first();

        if (!$signature) {
            return ($this->current_signature_step ?? 0) < $level ? 'locked' : 'pending';
        }

        return $signature->status->value;
    }

    /**
     * ✅ NEW: Get authority for signature level
     */
    private function getSignatureLevelAuthority(int $level): ?SignatureAuthority
    {
        $signature = $this->signatures()
            ->where('signature_level', $level)
            ->first();

        return $signature?->signatureAuthority;
    }

    /**
     * ✅ NEW: Get upload date for signature level
     */
    private function getSignatureLevelUploadDate(int $level): ?string
    {
        $signature = $this->signatures()
            ->where('signature_level', $level)
            ->whereNotNull('uploaded_at')
            ->first();

        return $signature?->uploaded_at?->format('d/m/Y H:i');
    }

    /**
     * ✅ NEW: Get verified date for signature level
     */
    private function getSignatureLevelVerifiedDate(int $level): ?string
    {
        $signature = $this->signatures()
            ->where('signature_level', $level)
            ->where('status', 'verified')
            ->first();

        return $signature?->verified_at?->format('d/m/Y H:i');
    }

    // ==================== APPLICANT METHODS ====================

    public function isGuestRequest(): bool
    {
        return $this->applicant_type->value === 'mahasiswa';
    }

    public function isInternalRequest(): bool
    {
        return in_array($this->applicant_type->value, ['dosen', 'staff']);
    }

    public function getApplicantNameWithType(): string
    {
        $type = match($this->applicant_type->value) {
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'staff' => 'Staff',
            default => ''
        };

        return "{$this->applicant_name} ({$type})";
    }

    // ==================== DELIVERY METHODS ====================

    public function isPickupDelivery(): bool
    {
        if ($this->delivery_method instanceof DeliveryMethod) {
            return $this->delivery_method === DeliveryMethod::PICKUP;
        }
        return $this->delivery_method === 'pickup';
    }

    public function isDownloadDelivery(): bool
    {
        if ($this->delivery_method instanceof DeliveryMethod) {
            return $this->delivery_method === DeliveryMethod::DOWNLOAD;
        }
        return $this->delivery_method === 'download';
    }

    public function getDeliveryMethodLabel(): string
    {
        if ($this->delivery_method instanceof DeliveryMethod) {
            return $this->delivery_method->label();
        }

        return match($this->delivery_method) {
            'pickup' => 'Ambil di Tempat',
            'download' => 'Download Online',
            default => '-'
        };
    }

    // ==================== FILE METHODS ====================

    public function hasUploadedFile(): bool
    {
        return !empty($this->file_path);
    }

    public function hasFile(): bool
    {
        return $this->hasUploadedFile();
    }

    public function getFileUrl(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }

    public function getFileName(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return $this->file_name ?? basename($this->file_path);
    }

    public function isDownloadable(): bool
    {
        if (!$this->hasUploadedFile()) {
            return false;
        }

        if (!$this->isDownloadDelivery()) {
            return false;
        }

        return in_array($this->status->value, ['ready_for_pickup', 'picked_up', 'completed']);
    }

    public function canBeDownloaded(): bool
    {
        return $this->isDownloadable();
    }

    public function needsFileUpload(): bool
    {
        return $this->isDownloadDelivery()
            && !$this->hasUploadedFile()
            && in_array($this->status->value, ['approved', 'processing', 'signature_completed']);
    }

    public function isDownloaded(): bool
    {
        return $this->activities()
            ->where('activity_type', 'downloaded')
            ->exists();
    }

    public function getDownloadedAt()
    {
        $activity = $this->activities()
            ->where('activity_type', 'downloaded')
            ->latest()
            ->first();

        return $activity ? $activity->created_at : null;
    }

    public function getDownloadCount(): int
    {
        return $this->activities()
            ->where('activity_type', 'downloaded')
            ->count();
    }

    // ==================== VERIFICATION STATUS METHODS ====================

    public function needsVerification(): bool
    {
        return $this->status->value === 'approved'
            && $this->getCurrentVerificationLevel() === 0;
    }

    public function isVerified(): bool
    {
        return $this->isAllVerificationCompleted();
    }

    public function isVerificationRejected(): bool
    {
        return $this->status === DocumentStatus::VERIFICATION_REJECTED;
    }

    public function canBeVerified(): bool
    {
        return $this->status->value === 'approved'
            && $this->getCurrentVerificationLevel() === 0;
    }

    public function hasVerificationAuthority(): bool
    {
        return !empty($this->verification_authority_id);
    }

    // ==================== SIGNATURE METHODS ====================

    public function requiresSignature(): bool
    {
        return $this->requires_signature === true;
    }

    public function needsSignature(): bool
    {
        return $this->requiresSignature()
            && !$this->areAllSignaturesVerified();
    }

    public function hasSignatures(): bool
    {
        return $this->signatures()->exists();
    }

    /**
     * ✅ UPDATED: Improve to check 3 levels explicitly
     */
    public function areAllSignaturesVerified(): bool
    {
        $verifiedCount = $this->signatures()
            ->whereIn('signature_level', [1, 2, 3])
            ->where('status', 'verified')
            ->count();

        return $verifiedCount === 3;
    }

    public function getPendingSignaturesCount(): int
    {
        return $this->pendingSignatures()->count();
    }

    public function getSignatureCompletionPercentage(): float
    {
        if ($this->signatures_required === 0) {
            return 0;
        }

        return round(($this->signatures_completed / $this->signatures_required) * 100, 2);
    }

    public function isWaitingForSignature(): bool
    {
        return $this->signature_status === 'waiting';
    }

    public function isSignatureInProgress(): bool
    {
        return $this->signature_status === 'in_progress';
    }

    public function isSignatureCompleted(): bool
    {
        return $this->signature_status === 'completed';
    }

    public function canBeSigned(): bool
    {
        return $this->requiresSignature()
            && $this->isVerified()
            && !$this->areAllSignaturesVerified();
    }

    // ==================== MARKED AS TAKEN METHODS ====================

    public function isMarkedAsTaken(): bool
    {
        return $this->is_marked_as_taken === true;
    }

    public function canBeMarkedAsTaken(): bool
    {
        return in_array($this->status->value, ['ready_for_pickup', 'picked_up'])
            && !$this->is_marked_as_taken;
    }

    public function wasMarkedByAdmin(): bool
    {
        return $this->marked_by_role === 'admin';
    }

    public function wasMarkedByUser(): bool
    {
        return $this->marked_by_role === 'user';
    }

    // ==================== STATUS CHECK METHODS ====================

    public function canChangeStatus(): bool
    {
        return !in_array($this->status->value, ['rejected', 'completed', 'verification_rejected']);
    }

    public function isPending(): bool
    {
        return $this->status->value === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status->value === 'approved';
    }

    public function isReady(): bool
    {
        return $this->status->value === 'ready_for_pickup';
    }

    public function isCompleted(): bool
    {
        return $this->status->value === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status->value === 'rejected';
    }

    // ==================== SCOPES ====================

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApplicantType($query, $type)
    {
        return $query->where('applicant_type', $type);
    }

    public function scopeDeliveryMethod($query, $method)
    {
        return $query->where('delivery_method', $method);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready_for_pickup');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopePickedUp($query)
    {
        return $query->where('status', 'picked_up');
    }

    public function scopeWithFiles($query)
    {
        return $query->whereNotNull('file_path');
    }

    public function scopeWithoutFiles($query)
    {
        return $query->whereNull('file_path');
    }

    public function scopeDownloadable($query)
    {
        return $query->where('delivery_method', 'download')
            ->whereNotNull('file_path')
            ->whereIn('status', ['ready_for_pickup', 'picked_up', 'completed']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeNeedsVerification($query)
    {
        return $query->where('status', 'approved')
            ->where('current_verification_step', 0);
    }

    public function scopeVerificationStep($query, int $step)
    {
        return $query->where('current_verification_step', $step);
    }

    public function scopeAwaitingVerificationLevel($query, int $level)
    {
        return $query->where('current_verification_step', $level - 1)
            ->whereIn('status', [
                'verification_step_1_requested',
                'verification_step_2_requested',
                'verification_step_3_requested',
            ]);
    }

    public function scopeVerificationRequested($query)
    {
        return $query->whereIn('status', [
            'verification_step_1_requested',
            'verification_step_2_requested',
            'verification_step_3_requested',
        ]);
    }

    public function scopeVerificationApproved($query)
    {
        return $query->where('status', 'verification_step_3_approved')
            ->where('current_verification_step', 3);
    }

    public function scopeVerificationRejected($query)
    {
        return $query->where('status', 'verification_rejected');
    }

    public function scopeRequiringSignature($query)
    {
        return $query->where('requires_signature', true);
    }

    public function scopeWaitingSignature($query)
    {
        return $query->where('status', 'waiting_signature');
    }

    public function scopeSignatureInProgress($query)
    {
        return $query->where('status', 'signature_in_progress');
    }

    public function scopeSignatureCompleted($query)
    {
        return $query->where('status', 'signature_completed');
    }

    public function scopeMarkedAsTaken($query)
    {
        return $query->where('is_marked_as_taken', true);
    }

    public function scopeNotMarkedAsTaken($query)
    {
        return $query->where('is_marked_as_taken', false);
    }
}
