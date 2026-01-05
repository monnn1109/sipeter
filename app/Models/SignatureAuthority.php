<?php

namespace App\Models;

use App\Enums\AuthorityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignatureAuthority extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'authority_type',
        'position',
        'phone',
        'email',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'authority_type' => AuthorityType::class,
        'is_active' => 'boolean',
    ];


    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }

    public function pendingSignatures(): HasMany
    {
        return $this->signatures()->pending();
    }

    public function verifiedSignatures(): HasMany
    {
        return $this->signatures()->verified();
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(DocumentVerification::class, 'authority_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeKetuaAkademik($query)
    {
        return $query->where('authority_type', AuthorityType::KETUA_AKADEMIK);
    }

    public function scopeWakilKetua3($query)
    {
        return $query->where('authority_type', AuthorityType::WAKIL_KETUA_3);
    }

    public function scopeKetua($query)
    {
        return $query->where('authority_type', AuthorityType::KETUA);
    }

    public function scopeByType($query, AuthorityType $type)
    {
        return $query->where('authority_type', $type);
    }


    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;

        if (str_starts_with($phone, '62')) {
            $phone = '+' . $phone;
            return substr($phone, 0, 3) . ' ' .
                   substr($phone, 3, 3) . '-' .
                   substr($phone, 6, 4) . '-' .
                   substr($phone, 10);
        }

        return $phone;
    }

    public function getWhatsappLinkAttribute(): string
    {
        $phone = $this->phone;
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!str_starts_with($phone, '62')) {
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }

        return "https://wa.me/{$phone}";
    }


    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isKetuaAkademik(): bool
    {
        return $this->authority_type === AuthorityType::KETUA_AKADEMIK;
    }

    public function isWakilKetua3(): bool
    {
        return $this->authority_type === AuthorityType::WAKIL_KETUA_3;
    }

    public function isKetua(): bool
    {
        return $this->authority_type === AuthorityType::KETUA;
    }

    public function getVerificationLevel(): int
    {
        return $this->authority_type->level();
    }

    public function isFinalLevel(): bool
    {
        return $this->authority_type->isFinal();
    }

    public function isFirstLevel(): bool
    {
        return $this->authority_type->isFirst();
    }


    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }


    public function getTotalSignatures(): int
    {
        return $this->signatures()->count();
    }

    public function getPendingSignatures(): int
    {
        return $this->pendingSignatures()->count();
    }

    public function getVerifiedSignatures(): int
    {
        return $this->verifiedSignatures()->count();
    }

    public function getCompletionRate(): float
    {
        $total = $this->getTotalSignatures();

        if ($total === 0) {
            return 0;
        }

        $verified = $this->getVerifiedSignatures();

        return round(($verified / $total) * 100, 2);
    }

    public function getAverageResponseTime(): float
    {
        $signatures = $this->signatures()
            ->whereNotNull('uploaded_at')
            ->get();

        if ($signatures->isEmpty()) {
            return 0;
        }

        $totalHours = $signatures->sum(function ($signature) {
            return $signature->requested_at->diffInHours($signature->uploaded_at);
        });

        return round($totalHours / $signatures->count(), 2);
    }

    public function getTotalVerifications(): int
    {
        return $this->verifications()->count();
    }

    public function getPendingVerifications(): int
    {
        return $this->verifications()->where('status', 'pending')->count();
    }

    public function getApprovedVerifications(): int
    {
        return $this->verifications()->where('status', 'approved')->count();
    }

    public function getRejectedVerifications(): int
    {
        return $this->verifications()->where('status', 'rejected')->count();
    }

    public function getVerificationCompletionRate(): float
    {
        $total = $this->getTotalVerifications();

        if ($total === 0) {
            return 0;
        }

        $approved = $this->getApprovedVerifications();

        return round(($approved / $total) * 100, 2);
    }

    public function getAverageVerificationTime(): float
    {
        $verifications = $this->verifications()
            ->whereNotNull('verified_at')
            ->get();

        if ($verifications->isEmpty()) {
            return 0;
        }

        $totalHours = $verifications->sum(function ($verification) {
            return $verification->sent_at->diffInHours($verification->verified_at);
        });

        return round($totalHours / $verifications->count(), 2);
    }


    public function getAuthorityTypeLabel(): string
    {
        return $this->authority_type->label();
    }

    public function getAuthorityTypeShortLabel(): string
    {
        return $this->authority_type->shortLabel();
    }

    public function getAuthorityTypeColor(): string
    {
        return $this->authority_type->color();
    }

    public function getAuthorityTypeIcon(): string
    {
        return $this->authority_type->icon();
    }


    public static function getActiveKetuaAkademik(): ?self
    {
        return self::active()->ketuaAkademik()->first();
    }

    public static function getActiveWakilKetua3(): ?self
    {
        return self::active()->wakilKetua3()->first();
    }

    public static function getActiveKetua(): ?self
    {
        return self::active()->ketua()->first();
    }

    public static function getActiveAll3Levels(): array
    {
        return [
            'level_1' => self::getActiveKetuaAkademik(),
            'level_2' => self::getActiveWakilKetua3(),
            'level_3' => self::getActiveKetua(),
        ];
    }
    public static function getActiveByLevel(int $level): ?self
    {
        $authorityType = match($level) {
            1 => AuthorityType::KETUA_AKADEMIK,
            2 => AuthorityType::WAKIL_KETUA_3,
            3 => AuthorityType::KETUA,
            default => null
        };

        if (!$authorityType) {
            return null;
        }

        return self::active()->byType($authorityType)->first();
    }

    public static function getAllActive(): \Illuminate\Database\Eloquent\Collection
    {
        return self::active()->orderBy('authority_type')->get();
    }

    public static function getAllGroupedByLevel(): array
    {
        $authorities = self::active()->get();

        return [
            'level_1' => $authorities->where('authority_type', AuthorityType::KETUA_AKADEMIK)->first(),
            'level_2' => $authorities->where('authority_type', AuthorityType::WAKIL_KETUA_3)->first(),
            'level_3' => $authorities->where('authority_type', AuthorityType::KETUA)->first(),
        ];
    }


    public function canReceiveSignatureRequest(): bool
    {
        return $this->is_active && !empty($this->phone);
    }

    public function canReceiveVerificationRequest(): bool
    {
        return $this->is_active && !empty($this->phone);
    }

    public function isValidForLevel(int $level): bool
    {
        return $this->is_active
            && $this->getVerificationLevel() === $level
            && !empty($this->phone);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($authority) {
            if ($authority->phone) {
                $phone = preg_replace('/[^0-9]/', '', $authority->phone);

                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }

                if (!str_starts_with($phone, '62')) {
                    $phone = '62' . $phone;
                }

                $authority->phone = $phone;
            }
        });
    }

    public function getFullDisplayName(): string
    {
        return "{$this->name} - {$this->position}";
    }

    public function getShortDisplay(): string
    {
        return $this->name;
    }

    public function getCompleteInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'authority_type' => $this->authority_type->value,
            'authority_label' => $this->getAuthorityTypeLabel(),
            'level' => $this->getVerificationLevel(),
            'phone' => $this->phone,
            'formatted_phone' => $this->formatted_phone,
            'whatsapp_link' => $this->whatsapp_link,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'is_final_level' => $this->isFinalLevel(),
            'is_first_level' => $this->isFirstLevel(),
        ];
    }
}
