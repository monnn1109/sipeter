<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\VerificationLevel;

class DocumentVerification extends Model
{
    protected $fillable = [
        'document_request_id',
        'authority_id',
        'token',
        'type',
        'status',
        'decision',
        'notes',
        'verified_at',
        'expires_at',
        'sent_at',
        'verification_level',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'verification_level' => 'integer',
    ];

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function authority(): BelongsTo
    {
        return $this->belongsTo(SignatureAuthority::class);
    }

    public function isLevel1(): bool
    {
        return $this->verification_level === 1;
    }

    public function isLevel2(): bool
    {
        return $this->verification_level === 2;
    }

    public function isLevel3(): bool
    {
        return $this->verification_level === 3;
    }

    public function isFinal(): bool
    {
        return $this->verification_level === 3;
    }

    public function getLevel(): int
    {
        return $this->verification_level ?? 1;
    }

    public function getLevelEnum(): VerificationLevel
    {
        return VerificationLevel::from($this->verification_level ?? 1);
    }

    public function getLevelLabel(): string
    {
        return match($this->verification_level) {
            1 => 'Level 1: Ketua Akademik',
            2 => 'Level 2: Wakil Ketua 3',
            3 => 'Level 3: Direktur (Final)',
            default => 'Unknown Level'
        };
    }

    public function getShortLevelLabel(): string
    {
        return "Level {$this->verification_level}";
    }

    public function getLevelProgressPercentage(): float
    {
        return $this->getLevelEnum()->progressPercentage();
    }

    public function canProceedToNext(): bool
    {
        return $this->isApproved() && !$this->isFinal();
    }

    public function getNextLevel(): ?int
    {
        if ($this->isFinal()) {
            return null;
        }

        return $this->verification_level + 1;
    }

    public function getPreviousLevelVerifications()
    {
        if ($this->verification_level <= 1) {
            return collect();
        }

        return self::where('document_request_id', $this->document_request_id)
            ->where('verification_level', '<', $this->verification_level)
            ->where('status', 'approved')
            ->with('authority')
            ->orderBy('verification_level')
            ->get();
    }

    public function arePreviousLevelsApproved(): bool
    {
        if ($this->verification_level <= 1) {
            return true; 
        }

        $previousCount = self::where('document_request_id', $this->document_request_id)
            ->where('verification_level', '<', $this->verification_level)
            ->where('status', 'approved')
            ->count();

        return $previousCount === ($this->verification_level - 1);
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved' && $this->decision === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected' && $this->decision === 'rejected';
    }

    public function isValid(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }


    public function getVerificationLinkAttribute(): string
    {
        return route('verification.show', $this->token);
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }

    public function getFullStatusLabel(): string
    {
        $level = $this->getLevelLabel();
        $status = $this->getStatusLabel();

        return "{$level} - {$status}";
    }

    public function getDaysUntilExpiry(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    public function getHoursUntilExpiry(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInHours($this->expires_at);
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '<=', now());
    }

    public function scopeByLevel($query, int $level)
    {
        return $query->where('verification_level', $level);
    }

    public function scopePendingLevel($query, int $level)
    {
        return $query->where('verification_level', $level)
            ->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    public function scopeApprovedLevel($query, int $level)
    {
        return $query->where('verification_level', $level)
            ->where('status', 'approved');
    }

    public function scopeRejectedLevel($query, int $level)
    {
        return $query->where('verification_level', $level)
            ->where('status', 'rejected');
    }

    public function scopeLevel1($query)
    {
        return $query->where('verification_level', 1);
    }

    public function scopeLevel2($query)
    {
        return $query->where('verification_level', 2);
    }

    public function scopeLevel3($query)
    {
        return $query->where('verification_level', 3);
    }

    public function scopeByAuthority($query, int $authorityId)
    {
        return $query->where('authority_id', $authorityId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
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

    public static function getStatsByLevel(): array
    {
        return [
            'level_1' => [
                'total' => self::where('verification_level', 1)->count(),
                'pending' => self::pendingLevel(1)->count(),
                'approved' => self::approvedLevel(1)->count(),
                'rejected' => self::rejectedLevel(1)->count(),
            ],
            'level_2' => [
                'total' => self::where('verification_level', 2)->count(),
                'pending' => self::pendingLevel(2)->count(),
                'approved' => self::approvedLevel(2)->count(),
                'rejected' => self::rejectedLevel(2)->count(),
            ],
            'level_3' => [
                'total' => self::where('verification_level', 3)->count(),
                'pending' => self::pendingLevel(3)->count(),
                'approved' => self::approvedLevel(3)->count(),
                'rejected' => self::rejectedLevel(3)->count(),
            ],
        ];
    }

    public static function getCompletionRate(int $level): float
    {
        $total = self::where('verification_level', $level)->count();

        if ($total === 0) {
            return 0;
        }

        $approved = self::approvedLevel($level)->count();

        return round(($approved / $total) * 100, 2);
    }
}
