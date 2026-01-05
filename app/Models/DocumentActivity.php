<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DocumentActivityType;

class DocumentActivity extends Model
{
    protected $fillable = [
        'document_request_id',
        'user_id',
        'actor_name', // ✅ Can be null for guest users
        'actor_type', // ✅ Can be null for guest users
        'activity_type',
        'status_from',
        'status_to',
        'description',
        'metadata',
    ];

    protected $casts = [
        'activity_type' => DocumentActivityType::class,
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Document Request
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Relationship: User (Actor)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get actor name with fallback for guest users
     */
    public function getActorNameDisplayAttribute(): string
    {
        return $this->actor_name ?? 'Mahasiswa (Guest)';
    }

    /**
     * Get actor type with fallback
     */
    public function getActorTypeDisplayAttribute(): string
    {
        return $this->actor_type ?? 'mahasiswa';
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d M Y, H:i');
    }

    /**
     * Get activity icon
     */
    public function getIconAttribute(): string
    {
        return $this->activity_type->icon();
    }

    /**
     * Get activity label
     */
    public function getLabelAttribute(): string
    {
        return $this->activity_type->label();
    }

    /**
     * Get activity color
     */
    public function getColorAttribute(): string
    {
        return $this->activity_type->color();
    }

    /**
     * Check if positive activity
     */
    public function isPositive(): bool
    {
        return $this->activity_type->isPositive();
    }

    /**
     * Check if negative activity
     */
    public function isNegative(): bool
    {
        return $this->activity_type->isNegative();
    }

    /**
     * Check if activity performed by guest
     */
    public function isGuestActivity(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Scope: Recent activities
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope: By document
     */
    public function scopeForDocument($query, int $documentRequestId)
    {
        return $query->where('document_request_id', $documentRequestId);
    }

    /**
     * Scope: By activity type
     */
    public function scopeOfType($query, DocumentActivityType $type)
    {
        return $query->where('activity_type', $type->value);
    }

    /**
     * Scope: Guest activities only
     */
    public function scopeGuestOnly($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope: Authenticated activities only
     */
    public function scopeAuthenticatedOnly($query)
    {
        return $query->whereNotNull('user_id');
    }
}
