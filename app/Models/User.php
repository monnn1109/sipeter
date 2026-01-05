<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nip_nidn',
        'phone_number',
        'password',
        'role',
        'unit',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isInternal(): bool
    {
        return in_array($this->role, [UserRole::DOSEN, UserRole::STAFF]);
    }

    public function isDosen(): bool
    {
        return $this->role === UserRole::DOSEN;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF;
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'user_id');
    }

    public function approvedDocuments()
    {
        return $this->hasMany(DocumentRequest::class, 'approved_by');
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(DocumentRequest::class, 'uploaded_by');
    }

    public function documentActivities()
    {
        return $this->hasMany(DocumentActivity::class);
    }

    public function markedDocuments()
    {
        return $this->hasMany(DocumentRequest::class, 'marked_as_taken_by');
    }

    public function verifications()
    {
        return $this->hasMany(DocumentVerification::class, 'verified_by');
    }

    public function signatures()
    {
        return $this->hasMany(DocumentSignature::class, 'uploaded_by');
    }

    public function authorityInfo()
    {
        return $this->hasOne(SignatureAuthority::class, 'email', 'email');
    }

    public function verifiedSignatures()
    {
        return $this->hasMany(DocumentSignature::class, 'verified_by');
    }

    public function getPhoneAttribute()
    {
        return $this->phone_number;
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active
            ? '<span class="badge bg-success">Aktif</span>'
            : '<span class="badge bg-danger">Nonaktif</span>';
    }

    public function isVerificationAuthority(): bool
    {
        return $this->authorityInfo()->exists();
    }

    public function isSignatureAuthority(): bool
    {
        return $this->authorityInfo()
            ->whereNotNull('signature_path')
            ->exists();
    }

    public function getAuthorityType(): ?string
    {
        return $this->authorityInfo?->authority_type;
    }

    public function getAuthorityPosition(): ?string
    {
        return $this->authorityInfo?->position;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::ADMIN);
    }

    public function scopeInternal($query)
    {
        return $query->whereIn('role', [UserRole::DOSEN, UserRole::STAFF]);
    }

    public function scopeAcademics($query)
    {
        return $query->whereHas('authorityInfo', function($q) {
            $q->whereNotNull('id');
        });
    }


    public function scopeAuthorities($query)
    {
        return $query->whereHas('authorityInfo', function($q) {
            $q->whereNotNull('signature_path');
        });
    }
}
