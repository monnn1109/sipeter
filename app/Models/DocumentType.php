<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code_prefix',
        'description',
        'applicable_for',
        'processing_days',
        'required_fields',
        'is_active',
    ];

    /**
     * 🔥 FIXED untuk Laravel 12 - Gunakan method casts()
     */
    protected function casts(): array
    {
        return [
            'applicable_for' => 'array',
            'required_fields' => 'array',
            'is_active' => 'boolean',
            'processing_days' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 🔥 FIXED - Query JSON dengan multiple approaches
     */
    public function scopeForApplicant($query, string $applicantType)
    {
        return $query->where(function($q) use ($applicantType) {
            // Approach 1: Direct array search (setelah di-cast)
            $q->whereRaw("JSON_CONTAINS(applicable_for, ?)", [json_encode($applicantType)])
              ->orWhereRaw("JSON_CONTAINS(applicable_for, ?)", [json_encode('all')]);

            // Approach 2: Internal untuk dosen & staff
            if (in_array($applicantType, ['dosen', 'staff'])) {
                $q->orWhereRaw("JSON_CONTAINS(applicable_for, ?)", [json_encode('internal')]);
            }
        });
    }

    /**
     * Generate unique document code for this document type
     * Format: PREFIX-YYYYMM-SEQUENCE
     * Example: SKAK-202411-001
     */
    public function generateDocumentCode(): string
    {
        $prefix = $this->code_prefix;
        $yearMonth = date('Ym'); // Format: 202411

        // Get last sequence for this month
        $lastRequest = DocumentRequest::where('request_code', 'LIKE', "{$prefix}-{$yearMonth}-%")
            ->orderBy('request_code', 'desc')
            ->first();

        if ($lastRequest) {
            // Extract sequence number from last code
            $lastCode = $lastRequest->request_code;
            $parts = explode('-', $lastCode);
            $lastSequence = (int) end($parts);
            $newSequence = $lastSequence + 1;
        } else {
            // First document this month
            $newSequence = 1;
        }

        // Format sequence with leading zeros (3 digits)
        $sequence = str_pad($newSequence, 3, '0', STR_PAD_LEFT);

        return "{$prefix}-{$yearMonth}-{$sequence}";
    }

    /**
     * Check if document type is applicable for specific applicant type
     */
    public function isApplicableFor(string $applicantType): bool
    {
        $applicableFor = $this->applicable_for;

        // Check direct match
        if (in_array($applicantType, $applicableFor)) {
            return true;
        }

        // Check for 'all'
        if (in_array('all', $applicableFor)) {
            return true;
        }

        // Check for 'internal' (dosen/staff)
        if (in_array($applicantType, ['dosen', 'staff']) && in_array('internal', $applicableFor)) {
            return true;
        }

        return false;
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
}
