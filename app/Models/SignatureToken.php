<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureToken extends Model
{
    protected $fillable = [
        'document_request_id',
        'authority_id',
        'token',
        'type',
        'status',
        'used_at',
        'expires_at',
        'sent_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Relationship ke DocumentRequest
     */
    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Relationship ke SignatureAuthority (Pejabat)
     */
    public function authority(): BelongsTo
    {
        return $this->belongsTo(SignatureAuthority::class);
    }

    /**
     * Check apakah token sudah expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Check apakah masih pending (belum digunakan)
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check apakah sudah digunakan
     */
    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    /**
     * Mark token sebagai used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => now()
        ]);
    }

    /**
     * Mark token sebagai expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired'
        ]);
    }

    /**
     * Get signature upload link
     */
    public function getUploadLinkAttribute(): string
    {
        return route('signature.upload', $this->token);
    }

    /**
     * Scope untuk token yang valid (pending & belum expired)
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope untuk token yang sudah expired
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '<=', now());
    }
}
