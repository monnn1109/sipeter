<?php

namespace App\Enums;

enum SignatureStatus: string
{
    case REQUESTED = 'requested';
    case UPLOADED = 'uploaded';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::REQUESTED => 'Menunggu TTD',
            self::UPLOADED => 'TTD Sudah Diupload',
            self::VERIFIED => 'TTD Terverifikasi',
            self::REJECTED => 'TTD Ditolak',
        };
    }


    public function color(): string
    {
        return match($this) {
            self::REQUESTED => 'warning',
            self::UPLOADED => 'info',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
        };
    }


    public function icon(): string
    {
        return match($this) {
            self::REQUESTED => 'clock',
            self::UPLOADED => 'upload',
            self::VERIFIED => 'check-circle',
            self::REJECTED => 'x-circle',
        };
    }


    public function isFinal(): bool
    {
        return in_array($this, [self::VERIFIED, self::REJECTED]);
    }


    public function canBeUploaded(): bool
    {
        return $this === self::REQUESTED;
    }


    public function canBeVerified(): bool
    {
        return $this === self::UPLOADED;
    }
}
