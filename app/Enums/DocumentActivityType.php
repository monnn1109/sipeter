<?php

namespace App\Enums;

enum DocumentActivityType: string
{
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PROCESSING = 'processing';
    case FILE_UPLOADED = 'file_uploaded';
    case READY = 'ready';
    case PICKED_UP = 'picked_up';
    case DOWNLOADED = 'downloaded';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NOTE_ADDED = 'note_added';
    case STATUS_UPDATED = 'status_updated';

    case VERIFICATION_REQUESTED = 'verification_requested';
    case VERIFICATION_APPROVED = 'verification_approved';
    case VERIFICATION_REJECTED = 'verification_rejected';

    case SIGNATURE_REQUESTED = 'signature_requested';
    case SIGNATURE_UPLOADED = 'signature_uploaded';
    case SIGNATURE_VERIFIED = 'signature_verified';
    case SIGNATURE_REJECTED = 'signature_rejected';
    case SIGNATURE_COMPLETED = 'signature_completed';

    case MARKED_AS_TAKEN = 'marked_as_taken';


    public function label(): string
    {
        return match($this) {
            self::SUBMITTED => 'Pengajuan Dibuat',
            self::APPROVED => 'Pengajuan Disetujui',
            self::REJECTED => 'Pengajuan Ditolak',
            self::PROCESSING => 'Dokumen Sedang Diproses',
            self::FILE_UPLOADED => 'File Dokumen Diupload',
            self::READY => 'Dokumen Siap Diambil/Download',
            self::PICKED_UP => 'Dokumen Telah Diambil',
            self::DOWNLOADED => 'Dokumen Telah Didownload',
            self::COMPLETED => 'Pengajuan Selesai',
            self::CANCELLED => 'Pengajuan Dibatalkan',
            self::NOTE_ADDED => 'Catatan Ditambahkan',
            self::STATUS_UPDATED => 'Status Diperbarui',

            self::VERIFICATION_REQUESTED => 'Verifikasi Diminta',
            self::VERIFICATION_APPROVED => 'Verifikasi Disetujui',
            self::VERIFICATION_REJECTED => 'Verifikasi Ditolak',

            self::SIGNATURE_REQUESTED => 'TTD Digital Diminta',
            self::SIGNATURE_UPLOADED => 'TTD Digital Diupload',
            self::SIGNATURE_VERIFIED => 'TTD Digital Diverifikasi',
            self::SIGNATURE_REJECTED => 'TTD Digital Ditolak',
            self::SIGNATURE_COMPLETED => 'Semua TTD Digital Selesai',
            self::MARKED_AS_TAKEN => 'Ditandai Sudah Diambil',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::SUBMITTED => 'file-plus',
            self::APPROVED => 'check-circle',
            self::REJECTED => 'x-circle',
            self::PROCESSING => 'refresh-cw',
            self::FILE_UPLOADED => 'upload',
            self::READY => 'package',
            self::PICKED_UP => 'shopping-bag',
            self::DOWNLOADED => 'download',
            self::COMPLETED => 'check-square',
            self::CANCELLED => 'slash',
            self::NOTE_ADDED => 'message-square',
            self::STATUS_UPDATED => 'edit',

            self::VERIFICATION_REQUESTED => 'shield-question',
            self::VERIFICATION_APPROVED => 'shield-check',
            self::VERIFICATION_REJECTED => 'shield-x',

            self::SIGNATURE_REQUESTED => 'send',
            self::SIGNATURE_UPLOADED => 'upload',
            self::SIGNATURE_VERIFIED => 'check-square',
            self::SIGNATURE_REJECTED => 'x-square',
            self::SIGNATURE_COMPLETED => 'award',
            self::MARKED_AS_TAKEN => 'check-circle',
        };
    }


    public function color(): string
    {
        return match($this) {
            self::SUBMITTED => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::PROCESSING => 'yellow',
            self::FILE_UPLOADED => 'purple',
            self::READY => 'indigo',
            self::PICKED_UP => 'teal',
            self::DOWNLOADED => 'cyan',
            self::COMPLETED => 'gray',
            self::CANCELLED => 'red',
            self::NOTE_ADDED => 'orange',
            self::STATUS_UPDATED => 'blue',

            self::VERIFICATION_REQUESTED => 'amber',
            self::VERIFICATION_APPROVED => 'emerald',
            self::VERIFICATION_REJECTED => 'rose',

            self::SIGNATURE_REQUESTED => 'purple',
            self::SIGNATURE_UPLOADED => 'indigo',
            self::SIGNATURE_VERIFIED => 'blue',
            self::SIGNATURE_REJECTED => 'orange',
            self::SIGNATURE_COMPLETED => 'emerald',
            self::MARKED_AS_TAKEN => 'teal',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [
            self::SUBMITTED,
            self::APPROVED,
            self::PROCESSING,
            self::FILE_UPLOADED,
            self::READY,
            self::PICKED_UP,
            self::DOWNLOADED,
            self::COMPLETED,
            self::VERIFICATION_REQUESTED,
            self::VERIFICATION_APPROVED,
            self::SIGNATURE_REQUESTED,
            self::SIGNATURE_UPLOADED,
            self::SIGNATURE_VERIFIED,
            self::SIGNATURE_COMPLETED,
            self::MARKED_AS_TAKEN,
        ]);
    }


    public function isNegative(): bool
    {
        return in_array($this, [
            self::REJECTED,
            self::CANCELLED,
            self::VERIFICATION_REJECTED,
            self::SIGNATURE_REJECTED,
        ]);
    }


    public function isVerificationActivity(): bool
    {
        return in_array($this, [
            self::VERIFICATION_REQUESTED,
            self::VERIFICATION_APPROVED,
            self::VERIFICATION_REJECTED
        ]);
    }


    public function isSignatureActivity(): bool
    {
        return in_array($this, [
            self::SIGNATURE_REQUESTED,
            self::SIGNATURE_UPLOADED,
            self::SIGNATURE_VERIFIED,
            self::SIGNATURE_REJECTED,
            self::SIGNATURE_COMPLETED
        ]);
    }


    public function isCritical(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::REJECTED,
            self::VERIFICATION_APPROVED,
            self::VERIFICATION_REJECTED,
            self::FILE_UPLOADED,
            self::READY,
            self::COMPLETED,
            self::SIGNATURE_VERIFIED,
            self::SIGNATURE_REJECTED,
        ]);
    }


    public function getDescriptionTemplate(): string
    {
        return match($this) {
            self::SUBMITTED => 'Dokumen {code} diajukan oleh {user}',
            self::APPROVED => 'Dokumen {code} disetujui oleh {admin}',
            self::REJECTED => 'Dokumen {code} ditolak oleh {admin}',
            self::PROCESSING => 'Dokumen {code} sedang diproses',
            self::FILE_UPLOADED => 'File dokumen {code} diupload oleh {admin}',
            self::READY => 'Dokumen {code} siap untuk diambil/download',
            self::PICKED_UP => 'Dokumen {code} diambil oleh {user}',
            self::DOWNLOADED => 'Dokumen {code} didownload oleh {user}',
            self::COMPLETED => 'Dokumen {code} selesai diproses',
            self::CANCELLED => 'Dokumen {code} dibatalkan',
            self::NOTE_ADDED => 'Catatan ditambahkan pada {code}',
            self::STATUS_UPDATED => 'Status dokumen {code} diperbarui',

            self::VERIFICATION_REQUESTED => 'Verifikasi diminta ke {authority} ({authority_position})',
            self::VERIFICATION_APPROVED => 'Dokumen diverifikasi dan disetujui oleh {authority}',
            self::VERIFICATION_REJECTED => 'Verifikasi ditolak oleh {authority}: {reason}',

            self::SIGNATURE_REQUESTED => 'TTD digital diminta ke {authority} ({authority_type})',
            self::SIGNATURE_UPLOADED => 'TTD digital dari {authority} telah diupload',
            self::SIGNATURE_VERIFIED => 'TTD digital dari {authority} diverifikasi oleh {admin}',
            self::SIGNATURE_REJECTED => 'TTD digital dari {authority} ditolak oleh {admin}',
            self::SIGNATURE_COMPLETED => 'Semua TTD digital telah selesai',
            self::MARKED_AS_TAKEN => 'Dokumen ditandai sudah diambil oleh {user}',
        };
    }


    public function getLabel(): string
    {
        return $this->label();
    }

    public function colorClass(): string
    {
        $colorMap = [
            'blue' => 'bg-blue-500 text-blue-700 border-blue-200',
            'green' => 'bg-green-500 text-green-700 border-green-200',
            'red' => 'bg-red-500 text-red-700 border-red-200',
            'yellow' => 'bg-yellow-500 text-yellow-700 border-yellow-200',
            'amber' => 'bg-amber-500 text-amber-700 border-amber-200',
            'purple' => 'bg-purple-500 text-purple-700 border-purple-200',
            'indigo' => 'bg-indigo-500 text-indigo-700 border-indigo-200',
            'teal' => 'bg-teal-500 text-teal-700 border-teal-200',
            'cyan' => 'bg-cyan-500 text-cyan-700 border-cyan-200',
            'gray' => 'bg-gray-500 text-gray-700 border-gray-200',
            'orange' => 'bg-orange-500 text-orange-700 border-orange-200',
            'emerald' => 'bg-emerald-500 text-emerald-700 border-emerald-200',
            'rose' => 'bg-rose-500 text-rose-700 border-rose-200',
        ];

        return $colorMap[$this->color()] ?? 'bg-gray-500 text-gray-700 border-gray-200';
    }
}
