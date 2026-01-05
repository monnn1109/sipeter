<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case SUBMITTED = 'submitted';
    case PENDING = 'pending';
    case APPROVED = 'approved';

    // ========================================
    // MULTI-LEVEL VERIFICATION (3 STEPS)
    // ========================================
    case VERIFICATION_STEP_1_REQUESTED = 'verification_step_1_requested';   // Ketua Akademik
    case VERIFICATION_STEP_1_APPROVED = 'verification_step_1_approved';

    case VERIFICATION_STEP_2_REQUESTED = 'verification_step_2_requested';   // Ketua Kemahasiswaan
    case VERIFICATION_STEP_2_APPROVED = 'verification_step_2_approved';

    case VERIFICATION_STEP_3_REQUESTED = 'verification_step_3_requested';   // Ketua (Final)
    case VERIFICATION_STEP_3_APPROVED = 'verification_step_3_approved';     // All verified!

    case VERIFICATION_REJECTED = 'verification_rejected';                   // Rejected di level manapun

    case REJECTED = 'rejected';
    case PROCESSING = 'processing';

    // ========================================
    // 🔥 NEW: 3-LEVEL SIGNATURE (TTD)
    // ========================================
    case SIGNATURE_REQUESTED = 'signature_requested';           // Admin request TTD (auto ke Level 1)
    case SIGNATURE_UPLOADED = 'signature_uploaded';             // 🔥 NEW: At least 1 TTD uploaded
    case SIGNATURE_LEVEL_1_REQUESTED = 'signature_level_1_requested';   // 🔥 NEW
    case SIGNATURE_LEVEL_1_UPLOADED = 'signature_level_1_uploaded';     // 🔥 NEW
    case SIGNATURE_LEVEL_2_REQUESTED = 'signature_level_2_requested';   // 🔥 NEW
    case SIGNATURE_LEVEL_2_UPLOADED = 'signature_level_2_uploaded';     // 🔥 NEW
    case SIGNATURE_LEVEL_3_REQUESTED = 'signature_level_3_requested';   // 🔥 NEW
    case SIGNATURE_LEVEL_3_UPLOADED = 'signature_level_3_uploaded';     // 🔥 NEW (All TTD uploaded!)
    case SIGNATURE_VERIFIED = 'signature_verified';             // Admin verified all TTD
    case SIGNATURE_COMPLETED = 'signature_completed';           // PDF final embedded

    // Legacy (keep for backward compatibility)
    case WAITING_SIGNATURE = 'waiting_signature';
    case SIGNATURE_IN_PROGRESS = 'signature_in_progress';

    case READY_FOR_PICKUP = 'ready_for_pickup';
    case PICKED_UP = 'picked_up';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::SUBMITTED => 'Baru Diajukan',
            self::PENDING => 'Menunggu Approval',
            self::APPROVED => 'Disetujui',

            // Level 1: Ketua Akademik
            self::VERIFICATION_STEP_1_REQUESTED => 'Verifikasi Ketua Akademik',
            self::VERIFICATION_STEP_1_APPROVED => 'Disetujui Ketua Akademik',

            // Level 2: Wakil Ketua 3 (Kemahasiswaan)
            self::VERIFICATION_STEP_2_REQUESTED => 'Verifikasi Wakil Ketua 3',
            self::VERIFICATION_STEP_2_APPROVED => 'Disetujui Wakil Ketua 3',

            // Level 3: Ketua/Direktur (Final)
            self::VERIFICATION_STEP_3_REQUESTED => 'Verifikasi Direktur (Final)',
            self::VERIFICATION_STEP_3_APPROVED => 'Disetujui Direktur',

            self::VERIFICATION_REJECTED => 'Verifikasi Ditolak',

            self::REJECTED => 'Ditolak',
            self::PROCESSING => 'Sedang Diproses',

            // 🔥 NEW: 3-Level Signature Labels
            self::SIGNATURE_REQUESTED => 'Request TTD Level 1',
            self::SIGNATURE_UPLOADED => 'TTD Sedang Diupload',
            self::SIGNATURE_LEVEL_1_REQUESTED => 'Menunggu TTD Ketua Akademik',
            self::SIGNATURE_LEVEL_1_UPLOADED => 'TTD Ketua Akademik Selesai',
            self::SIGNATURE_LEVEL_2_REQUESTED => 'Menunggu TTD Wakil Ketua 3',
            self::SIGNATURE_LEVEL_2_UPLOADED => 'TTD Wakil Ketua 3 Selesai',
            self::SIGNATURE_LEVEL_3_REQUESTED => 'Menunggu TTD Direktur',
            self::SIGNATURE_LEVEL_3_UPLOADED => 'Semua TTD Selesai',
            self::SIGNATURE_VERIFIED => 'TTD Terverifikasi Admin',
            self::SIGNATURE_COMPLETED => 'TTD Selesai Diembed',

            self::WAITING_SIGNATURE => 'Menunggu Tanda Tangan Digital',
            self::SIGNATURE_IN_PROGRESS => 'Proses Tanda Tangan',

            self::READY_FOR_PICKUP => 'Siap Diambil/Download',
            self::PICKED_UP => 'Sudah Diambil',
            self::COMPLETED => 'Selesai',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SUBMITTED => 'blue',
            self::PENDING => 'yellow',
            self::APPROVED => 'green',

            self::VERIFICATION_STEP_1_REQUESTED => 'blue',
            self::VERIFICATION_STEP_1_APPROVED => 'emerald',

            self::VERIFICATION_STEP_2_REQUESTED => 'green',
            self::VERIFICATION_STEP_2_APPROVED => 'teal',

            self::VERIFICATION_STEP_3_REQUESTED => 'purple',
            self::VERIFICATION_STEP_3_APPROVED => 'violet',

            self::VERIFICATION_REJECTED => 'rose',

            self::REJECTED => 'red',
            self::PROCESSING => 'indigo',

            // 🔥 NEW: 3-Level Signature Colors
            self::SIGNATURE_REQUESTED => 'orange',
            self::SIGNATURE_UPLOADED => 'cyan',
            self::SIGNATURE_LEVEL_1_REQUESTED => 'blue',
            self::SIGNATURE_LEVEL_1_UPLOADED => 'emerald',
            self::SIGNATURE_LEVEL_2_REQUESTED => 'indigo',
            self::SIGNATURE_LEVEL_2_UPLOADED => 'teal',
            self::SIGNATURE_LEVEL_3_REQUESTED => 'purple',
            self::SIGNATURE_LEVEL_3_UPLOADED => 'violet',
            self::SIGNATURE_VERIFIED => 'green',
            self::SIGNATURE_COMPLETED => 'cyan',

            self::WAITING_SIGNATURE => 'purple',
            self::SIGNATURE_IN_PROGRESS => 'violet',

            self::READY_FOR_PICKUP => 'purple',
            self::PICKED_UP => 'teal',
            self::COMPLETED => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::SUBMITTED => 'file-text',
            self::PENDING => 'clock',
            self::APPROVED => 'check-circle',

            self::VERIFICATION_STEP_1_REQUESTED => 'book-open',
            self::VERIFICATION_STEP_1_APPROVED => 'check',

            self::VERIFICATION_STEP_2_REQUESTED => 'users',
            self::VERIFICATION_STEP_2_APPROVED => 'check',

            self::VERIFICATION_STEP_3_REQUESTED => 'crown',
            self::VERIFICATION_STEP_3_APPROVED => 'shield-check',

            self::VERIFICATION_REJECTED => 'shield-x',

            self::REJECTED => 'x-circle',
            self::PROCESSING => 'refresh-cw',

            // 🔥 NEW: 3-Level Signature Icons
            self::SIGNATURE_REQUESTED => 'send',
            self::SIGNATURE_UPLOADED => 'upload',
            self::SIGNATURE_LEVEL_1_REQUESTED => 'edit-3',
            self::SIGNATURE_LEVEL_1_UPLOADED => 'check',
            self::SIGNATURE_LEVEL_2_REQUESTED => 'edit-3',
            self::SIGNATURE_LEVEL_2_UPLOADED => 'check',
            self::SIGNATURE_LEVEL_3_REQUESTED => 'edit-3',
            self::SIGNATURE_LEVEL_3_UPLOADED => 'check-circle',
            self::SIGNATURE_VERIFIED => 'shield-check',
            self::SIGNATURE_COMPLETED => 'check-square',

            self::WAITING_SIGNATURE => 'edit-3',
            self::SIGNATURE_IN_PROGRESS => 'pen-tool',

            self::READY_FOR_PICKUP => 'package',
            self::PICKED_UP => 'shopping-bag',
            self::COMPLETED => 'check-square',
        };
    }

    public function badgeClass(): string
    {
        $colorMap = [
            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'green' => 'bg-green-100 text-green-800 border-green-200',
            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'teal' => 'bg-teal-100 text-teal-800 border-teal-200',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
            'violet' => 'bg-violet-100 text-violet-800 border-violet-200',
            'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
            'red' => 'bg-red-100 text-red-800 border-red-200',
            'indigo' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
            'cyan' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
        ];

        return $colorMap[$this->color()] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    }

    public function isFinished(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::REJECTED,
            self::VERIFICATION_REJECTED
        ]);
    }

    public function canBeUpdated(): bool
    {
        return !$this->isFinished();
    }

    public function isPendingAction(): bool
    {
        return in_array($this, [
            self::SUBMITTED,
            self::PENDING,
            self::VERIFICATION_STEP_1_REQUESTED,
            self::VERIFICATION_STEP_2_REQUESTED,
            self::VERIFICATION_STEP_3_REQUESTED,
            self::SIGNATURE_LEVEL_1_REQUESTED,
            self::SIGNATURE_LEVEL_2_REQUESTED,
            self::SIGNATURE_LEVEL_3_REQUESTED,
        ]);
    }

    public function isReadyForUser(): bool
    {
        return in_array($this, [
            self::READY_FOR_PICKUP,
            self::PICKED_UP,
            self::COMPLETED
        ]);
    }

    public function isVerificationProcess(): bool
    {
        return in_array($this, [
            self::VERIFICATION_STEP_1_REQUESTED,
            self::VERIFICATION_STEP_1_APPROVED,
            self::VERIFICATION_STEP_2_REQUESTED,
            self::VERIFICATION_STEP_2_APPROVED,
            self::VERIFICATION_STEP_3_REQUESTED,
            self::VERIFICATION_STEP_3_APPROVED,
        ]);
    }

    public function isSignatureProcess(): bool
    {
        return in_array($this, [
            self::SIGNATURE_REQUESTED,
            self::SIGNATURE_UPLOADED,
            self::SIGNATURE_LEVEL_1_REQUESTED,
            self::SIGNATURE_LEVEL_1_UPLOADED,
            self::SIGNATURE_LEVEL_2_REQUESTED,
            self::SIGNATURE_LEVEL_2_UPLOADED,
            self::SIGNATURE_LEVEL_3_REQUESTED,
            self::SIGNATURE_LEVEL_3_UPLOADED,
            self::SIGNATURE_VERIFIED,
            self::SIGNATURE_COMPLETED,
            self::WAITING_SIGNATURE,
            self::SIGNATURE_IN_PROGRESS,
        ]);
    }

    public function canBeDownloaded(): bool
    {
        return in_array($this, [
            self::READY_FOR_PICKUP,
            self::PICKED_UP,
            self::COMPLETED
        ]);
    }

    public function nextPossibleStatuses(): array
    {
        return match($this) {
            self::SUBMITTED => [self::PENDING, self::APPROVED, self::REJECTED],
            self::PENDING => [self::APPROVED, self::REJECTED],

            self::APPROVED => [self::VERIFICATION_STEP_1_REQUESTED, self::REJECTED],

            // Verification Level 1
            self::VERIFICATION_STEP_1_REQUESTED => [
                self::VERIFICATION_STEP_1_APPROVED,
                self::VERIFICATION_REJECTED
            ],
            self::VERIFICATION_STEP_1_APPROVED => [self::VERIFICATION_STEP_2_REQUESTED],

            // Verification Level 2
            self::VERIFICATION_STEP_2_REQUESTED => [
                self::VERIFICATION_STEP_2_APPROVED,
                self::VERIFICATION_REJECTED
            ],
            self::VERIFICATION_STEP_2_APPROVED => [self::VERIFICATION_STEP_3_REQUESTED],

            // Verification Level 3 (Final)
            self::VERIFICATION_STEP_3_REQUESTED => [
                self::VERIFICATION_STEP_3_APPROVED,
                self::VERIFICATION_REJECTED
            ],
            self::VERIFICATION_STEP_3_APPROVED => [self::SIGNATURE_REQUESTED, self::PROCESSING],

            self::VERIFICATION_REJECTED => [],

            self::PROCESSING => [self::READY_FOR_PICKUP],

            // 🔥 NEW: 3-Level Signature Flow
            self::SIGNATURE_REQUESTED => [self::SIGNATURE_LEVEL_1_REQUESTED],
            self::SIGNATURE_LEVEL_1_REQUESTED => [self::SIGNATURE_LEVEL_1_UPLOADED],
            self::SIGNATURE_LEVEL_1_UPLOADED => [self::SIGNATURE_LEVEL_2_REQUESTED],
            self::SIGNATURE_LEVEL_2_REQUESTED => [self::SIGNATURE_LEVEL_2_UPLOADED],
            self::SIGNATURE_LEVEL_2_UPLOADED => [self::SIGNATURE_LEVEL_3_REQUESTED],
            self::SIGNATURE_LEVEL_3_REQUESTED => [self::SIGNATURE_LEVEL_3_UPLOADED],
            self::SIGNATURE_LEVEL_3_UPLOADED => [self::SIGNATURE_VERIFIED],
            self::SIGNATURE_UPLOADED => [self::SIGNATURE_VERIFIED],
            self::SIGNATURE_VERIFIED => [self::SIGNATURE_COMPLETED],
            self::SIGNATURE_COMPLETED => [self::READY_FOR_PICKUP],

            self::WAITING_SIGNATURE => [self::SIGNATURE_IN_PROGRESS, self::REJECTED],
            self::SIGNATURE_IN_PROGRESS => [self::SIGNATURE_COMPLETED, self::WAITING_SIGNATURE],

            self::READY_FOR_PICKUP => [self::PICKED_UP, self::COMPLETED],
            self::PICKED_UP => [self::COMPLETED],
            self::COMPLETED => [],
            self::REJECTED => [],
        };
    }

    public function description(): string
    {
        return match($this) {
            self::SUBMITTED => 'Permohonan Anda telah diterima dan menunggu proses verifikasi oleh admin.',
            self::PENDING => 'Permohonan sedang menunggu persetujuan dari admin.',
            self::APPROVED => 'Permohonan Anda telah disetujui dan akan segera diproses.',

            self::VERIFICATION_STEP_1_REQUESTED => 'Dokumen sedang diverifikasi oleh Ketua Akademik (Step 1 dari 3).',
            self::VERIFICATION_STEP_1_APPROVED => 'Verifikasi Ketua Akademik selesai. Lanjut ke Wakil Ketua 3.',

            self::VERIFICATION_STEP_2_REQUESTED => 'Dokumen sedang diverifikasi oleh Wakil Ketua 3 - Kemahasiswaan (Step 2 dari 3).',
            self::VERIFICATION_STEP_2_APPROVED => 'Verifikasi Wakil Ketua 3 selesai. Lanjut ke Direktur.',

            self::VERIFICATION_STEP_3_REQUESTED => 'Dokumen sedang menunggu approval final dari Direktur (Step 3 dari 3).',
            self::VERIFICATION_STEP_3_APPROVED => 'Semua verifikasi selesai! Dokumen akan diproses untuk penandatanganan.',

            self::VERIFICATION_REJECTED => 'Verifikasi dokumen ditolak. Silakan periksa alasan penolakan.',

            self::REJECTED => 'Permohonan Anda ditolak. Silakan periksa alasan penolakan.',
            self::PROCESSING => 'Dokumen Anda sedang dalam proses pembuatan.',

            // 🔥 NEW: 3-Level Signature Descriptions
            self::SIGNATURE_REQUESTED => 'Request tanda tangan digital telah dikirim ke Ketua Akademik.',
            self::SIGNATURE_UPLOADED => 'Tanda tangan sedang dalam proses upload.',
            self::SIGNATURE_LEVEL_1_REQUESTED => 'Menunggu upload TTD dari Ketua Akademik (Level 1/3).',
            self::SIGNATURE_LEVEL_1_UPLOADED => 'TTD Ketua Akademik selesai. Lanjut ke Wakil Ketua 3.',
            self::SIGNATURE_LEVEL_2_REQUESTED => 'Menunggu upload TTD dari Wakil Ketua 3 (Level 2/3).',
            self::SIGNATURE_LEVEL_2_UPLOADED => 'TTD Wakil Ketua 3 selesai. Lanjut ke Direktur.',
            self::SIGNATURE_LEVEL_3_REQUESTED => 'Menunggu upload TTD dari Direktur (Level 3/3 - Final).',
            self::SIGNATURE_LEVEL_3_UPLOADED => 'Semua TTD selesai diupload! Menunggu verifikasi admin.',
            self::SIGNATURE_VERIFIED => 'Admin telah memverifikasi semua TTD. Dokumen akan di-embed.',
            self::SIGNATURE_COMPLETED => 'Tanda tangan digital telah selesai, dokumen akan segera diupload.',

            self::WAITING_SIGNATURE => 'Menunggu tanda tangan digital dari pejabat yang berwenang.',
            self::SIGNATURE_IN_PROGRESS => 'Proses tanda tangan digital sedang berlangsung.',

            self::READY_FOR_PICKUP => 'Dokumen Anda sudah siap! Silakan ambil atau download.',
            self::PICKED_UP => 'Dokumen telah diambil/didownload.',
            self::COMPLETED => 'Proses permohonan dokumen telah selesai.',
        };
    }

    public function progressPercentage(): int
    {
        return match($this) {
            self::SUBMITTED => 5,
            self::PENDING => 10,
            self::APPROVED => 15,

            self::VERIFICATION_STEP_1_REQUESTED => 25,
            self::VERIFICATION_STEP_1_APPROVED => 35,

            self::VERIFICATION_STEP_2_REQUESTED => 45,
            self::VERIFICATION_STEP_2_APPROVED => 55,

            self::VERIFICATION_STEP_3_REQUESTED => 65,
            self::VERIFICATION_STEP_3_APPROVED => 75,

            self::VERIFICATION_REJECTED => 0,

            self::REJECTED => 0,
            self::PROCESSING => 80,

            // 🔥 NEW: 3-Level Signature Progress
            self::SIGNATURE_REQUESTED => 76,
            self::SIGNATURE_UPLOADED => 77,
            self::SIGNATURE_LEVEL_1_REQUESTED => 78,
            self::SIGNATURE_LEVEL_1_UPLOADED => 81,
            self::SIGNATURE_LEVEL_2_REQUESTED => 83,
            self::SIGNATURE_LEVEL_2_UPLOADED => 86,
            self::SIGNATURE_LEVEL_3_REQUESTED => 88,
            self::SIGNATURE_LEVEL_3_UPLOADED => 91,
            self::SIGNATURE_VERIFIED => 93,
            self::SIGNATURE_COMPLETED => 95,

            self::WAITING_SIGNATURE => 85,
            self::SIGNATURE_IN_PROGRESS => 88,

            self::READY_FOR_PICKUP => 97,
            self::PICKED_UP => 99,
            self::COMPLETED => 100,
        };
    }

    /**
     * Get verification level from status
     */
    public function getVerificationLevel(): ?int
    {
        return match($this) {
            self::VERIFICATION_STEP_1_REQUESTED,
            self::VERIFICATION_STEP_1_APPROVED => 1,

            self::VERIFICATION_STEP_2_REQUESTED,
            self::VERIFICATION_STEP_2_APPROVED => 2,

            self::VERIFICATION_STEP_3_REQUESTED,
            self::VERIFICATION_STEP_3_APPROVED => 3,

            default => null,
        };
    }

    /**
     * 🔥 NEW: Get signature level from status
     */
    public function getSignatureLevel(): ?int
    {
        return match($this) {
            self::SIGNATURE_LEVEL_1_REQUESTED,
            self::SIGNATURE_LEVEL_1_UPLOADED => 1,

            self::SIGNATURE_LEVEL_2_REQUESTED,
            self::SIGNATURE_LEVEL_2_UPLOADED => 2,

            self::SIGNATURE_LEVEL_3_REQUESTED,
            self::SIGNATURE_LEVEL_3_UPLOADED => 3,

            default => null,
        };
    }

    /**
     * Check if all verifications completed
     */
    public function isAllVerificationsCompleted(): bool
    {
        return $this === self::VERIFICATION_STEP_3_APPROVED;
    }

    /**
     * 🔥 NEW: Check if all signatures uploaded
     */
    public function isAllSignaturesUploaded(): bool
    {
        return $this === self::SIGNATURE_LEVEL_3_UPLOADED;
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return $this->color();
    }

    public function getIcon(): string
    {
        return $this->icon();
    }
}
