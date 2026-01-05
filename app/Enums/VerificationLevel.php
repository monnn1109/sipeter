<?php

namespace App\Enums;

enum VerificationLevel: int
{
    case NOT_STARTED = 0;      
    case AKADEMIK = 1;
    case WAKIL_KETUA_3 = 2;
    case KETUA = 3;

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Belum Dimulai',
            self::AKADEMIK => 'Verifikasi Ketua Akademik',
            self::WAKIL_KETUA_3 => 'Verifikasi Wakil Ketua 3',
            self::KETUA => 'Verifikasi Direktur (Final)',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Belum Mulai',
            self::AKADEMIK => 'Akademik',
            self::WAKIL_KETUA_3 => 'Wakil Ketua 3',
            self::KETUA => 'Direktur',
        };
    }

    public function authorityType(): ?AuthorityType
    {
        return match($this) {
            self::NOT_STARTED => null,
            self::AKADEMIK => AuthorityType::KETUA_AKADEMIK,
            self::WAKIL_KETUA_3 => AuthorityType::WAKIL_KETUA_3,
            self::KETUA => AuthorityType::KETUA,
        };
    }

    public function next(): ?self
    {
        return match($this) {
            self::NOT_STARTED => self::AKADEMIK,
            self::AKADEMIK => self::WAKIL_KETUA_3,
            self::WAKIL_KETUA_3 => self::KETUA,
            self::KETUA => null,
        };
    }

    public function previous(): ?self
    {
        return match($this) {
            self::NOT_STARTED => null,
            self::AKADEMIK => self::NOT_STARTED,
            self::WAKIL_KETUA_3 => self::AKADEMIK,
            self::KETUA => self::WAKIL_KETUA_3,
        };
    }

    public function isCompleted(): bool
    {
        return $this === self::KETUA;
    }

    public function isNotStarted(): bool
    {
        return $this === self::NOT_STARTED;
    }

    public function isInProgress(): bool
    {
        return in_array($this, [self::AKADEMIK, self::WAKIL_KETUA_3, self::KETUA]);
    }

    public function progressPercentage(): int
    {
        return match($this) {
            self::NOT_STARTED => 0,
            self::AKADEMIK => 33,
            self::WAKIL_KETUA_3 => 66,
            self::KETUA => 100,
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NOT_STARTED => 'gray',
            self::AKADEMIK => 'blue',
            self::WAKIL_KETUA_3 => 'green',
            self::KETUA => 'purple',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::NOT_STARTED => 'circle',
            self::AKADEMIK => 'book-open',
            self::WAKIL_KETUA_3 => 'users',
            self::KETUA => 'crown',
        };
    }

    public function badgeClass(): string
    {
        $colorMap = [
            'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
            'green' => 'bg-green-100 text-green-800 border-green-200',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
        ];

        return $colorMap[$this->color()] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    }

    public function description(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Verifikasi belum dimulai',
            self::AKADEMIK => 'Sedang diverifikasi oleh Ketua Akademik',
            self::WAKIL_KETUA_3 => 'Sedang diverifikasi oleh Wakil Ketua 3 (Kemahasiswaan)',
            self::KETUA => 'Sedang menunggu approval final dari Direktur',
        };
    }

    public static function toArray(): array
    {
        return [
            self::NOT_STARTED->value => self::NOT_STARTED->label(),
            self::AKADEMIK->value => self::AKADEMIK->label(),
            self::WAKIL_KETUA_3->value => self::WAKIL_KETUA_3->label(),
            self::KETUA->value => self::KETUA->label(),
        ];
    }

    public static function verificationLevels(): array
    {
        return [
            self::AKADEMIK,
            self::WAKIL_KETUA_3,
            self::KETUA,
        ];
    }
}
