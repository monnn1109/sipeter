<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppNotification extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_notifications';

    protected $fillable = [
        'document_request_id',
        'recipient_phone',
        'recipient_name',
        'recipient_type',
        'event_type',
        'message',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'read_at',
        'retry_count',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'retry_count' => 'integer',
        'metadata' => 'array',
    ];

    // ==================== CONSTANTS ====================

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';

    const RECIPIENT_USER = 'user';
    const RECIPIENT_ADMIN = 'admin';
    const RECIPIENT_AUTHORITY = 'authority';

    // Event types matching with workflow steps
    const EVENT_REQUEST_SUBMITTED = 'request_submitted';
    const EVENT_REQUEST_APPROVED = 'request_approved';
    const EVENT_REQUEST_REJECTED = 'request_rejected';
    const EVENT_VERIFICATION_REQUESTED = 'verification_requested';
    const EVENT_VERIFICATION_APPROVED = 'verification_approved';
    const EVENT_VERIFICATION_REJECTED = 'verification_rejected';
    const EVENT_SIGNATURE_REQUESTED = 'signature_requested';
    const EVENT_SIGNATURE_UPLOADED = 'signature_uploaded';
    const EVENT_SIGNATURE_VERIFIED = 'signature_verified';
    const EVENT_SIGNATURE_REJECTED = 'signature_rejected';
    const EVENT_DOCUMENT_READY = 'document_ready';
    const EVENT_DOCUMENT_COMPLETED = 'document_completed';
    const EVENT_NEW_REQUEST = 'new_request';
    const EVENT_REMINDER = 'reminder';
    const EVENT_OTHER = 'other';

    // ==================== RELATIONSHIPS ====================

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeToUser($query)
    {
        return $query->where('recipient_type', self::RECIPIENT_USER);
    }

    public function scopeToAdmin($query)
    {
        return $query->where('recipient_type', self::RECIPIENT_ADMIN);
    }

    public function scopeToAuthority($query)
    {
        return $query->where('recipient_type', self::RECIPIENT_AUTHORITY);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeForDocument($query, int $documentRequestId)
    {
        return $query->where('document_request_id', $documentRequestId);
    }

    public function scopeRecent($query)
    {
        return $query->latest('created_at');
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

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_SENT => 'Terkirim',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_READ => 'Dibaca',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_SENT => 'badge-success',
            self::STATUS_FAILED => 'badge-danger',
            self::STATUS_DELIVERED => 'badge-info',
            self::STATUS_READ => 'badge-primary',
            default => 'badge-secondary'
        };
    }

    public function getRecipientTypeLabelAttribute(): string
    {
        return match($this->recipient_type) {
            self::RECIPIENT_USER => 'User',
            self::RECIPIENT_ADMIN => 'Admin',
            self::RECIPIENT_AUTHORITY => 'Pejabat',
            default => 'Unknown'
        };
    }

    public function getEventTypeLabelAttribute(): string
    {
        return match($this->event_type) {
            self::EVENT_REQUEST_SUBMITTED => 'Request Submitted',
            self::EVENT_REQUEST_APPROVED => 'Request Approved',
            self::EVENT_REQUEST_REJECTED => 'Request Rejected',
            self::EVENT_VERIFICATION_REQUESTED => 'Verification Requested',
            self::EVENT_VERIFICATION_APPROVED => 'Verification Approved',
            self::EVENT_VERIFICATION_REJECTED => 'Verification Rejected',
            self::EVENT_SIGNATURE_REQUESTED => 'Signature Requested',
            self::EVENT_SIGNATURE_UPLOADED => 'Signature Uploaded',
            self::EVENT_SIGNATURE_VERIFIED => 'Signature Verified',
            self::EVENT_SIGNATURE_REJECTED => 'Signature Rejected',
            self::EVENT_DOCUMENT_READY => 'Document Ready',
            self::EVENT_DOCUMENT_COMPLETED => 'Document Completed',
            self::EVENT_NEW_REQUEST => 'New Request',
            self::EVENT_REMINDER => 'Reminder',
            self::EVENT_OTHER => 'Other',
            default => 'Unknown'
        };
    }

    public function getFormattedPhoneAttribute(): string
    {
        // Format: +62 812-3456-7890
        $phone = $this->recipient_phone;

        if (strlen($phone) >= 10) {
            return '+' . substr($phone, 0, 2) . ' ' .
                   substr($phone, 2, 3) . '-' .
                   substr($phone, 5, 4) . '-' .
                   substr($phone, 9);
        }

        return $phone;
    }

    // ==================== MUTATORS ====================

    public function setRecipientPhoneAttribute($value)
    {
        // Auto format to 62xxx
        $phone = preg_replace('/[^0-9]/', '', $value);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        $this->attributes['recipient_phone'] = $phone;
    }

    // ==================== HELPER METHODS ====================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isRead(): bool
    {
        return $this->status === self::STATUS_READ;
    }

    public function markAsSent(): bool
    {
        return $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): bool
    {
        return $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function markAsRead(): bool
    {
        return $this->update([
            'status' => self::STATUS_READ,
            'read_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    public function incrementRetry(): bool
    {
        return $this->increment('retry_count');
    }

    public function canRetry(int $maxRetries = 3): bool
    {
        return $this->isFailed() && $this->retry_count < $maxRetries;
    }

    public function addMetadata(string $key, $value): bool
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;

        return $this->update(['metadata' => $metadata]);
    }

    // ==================== STATIC HELPERS ====================

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_SENT => 'Terkirim',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_READ => 'Dibaca',
        ];
    }

    public static function getRecipientTypeOptions(): array
    {
        return [
            self::RECIPIENT_USER => 'User',
            self::RECIPIENT_ADMIN => 'Admin',
            self::RECIPIENT_AUTHORITY => 'Pejabat',
        ];
    }

    public static function getEventTypeOptions(): array
    {
        return [
            self::EVENT_REQUEST_SUBMITTED => 'Request Submitted',
            self::EVENT_REQUEST_APPROVED => 'Request Approved',
            self::EVENT_REQUEST_REJECTED => 'Request Rejected',
            self::EVENT_VERIFICATION_REQUESTED => 'Verification Requested',
            self::EVENT_VERIFICATION_APPROVED => 'Verification Approved',
            self::EVENT_VERIFICATION_REJECTED => 'Verification Rejected',
            self::EVENT_SIGNATURE_REQUESTED => 'Signature Requested',
            self::EVENT_SIGNATURE_UPLOADED => 'Signature Uploaded',
            self::EVENT_SIGNATURE_VERIFIED => 'Signature Verified',
            self::EVENT_SIGNATURE_REJECTED => 'Signature Rejected',
            self::EVENT_DOCUMENT_READY => 'Document Ready',
            self::EVENT_DOCUMENT_COMPLETED => 'Document Completed',
            self::EVENT_NEW_REQUEST => 'New Request',
            self::EVENT_REMINDER => 'Reminder',
            self::EVENT_OTHER => 'Other',
        ];
    }

    // ==================== STATISTICS ====================

    public static function getTodayStats(): array
    {
        return [
            'total' => self::today()->count(),
            'sent' => self::today()->sent()->count(),
            'failed' => self::today()->failed()->count(),
            'pending' => self::today()->pending()->count(),
        ];
    }

    public static function getWeekStats(): array
    {
        return [
            'total' => self::thisWeek()->count(),
            'sent' => self::thisWeek()->sent()->count(),
            'failed' => self::thisWeek()->failed()->count(),
            'pending' => self::thisWeek()->pending()->count(),
        ];
    }

    public static function getMonthStats(): array
    {
        return [
            'total' => self::thisMonth()->count(),
            'sent' => self::thisMonth()->sent()->count(),
            'failed' => self::thisMonth()->failed()->count(),
            'pending' => self::thisMonth()->pending()->count(),
        ];
    }
}
