<?php

namespace App\Enums;

enum AuthorityType: string
{
    case KETUA_AKADEMIK = 'ketua_akademik';
    case WAKIL_KETUA_3 = 'wakil_ketua_3';  // 🔥 FIXED: Dari 'kemahasiswaan' jadi 'wakil_ketua_3'
    case KETUA = 'ketua';

    public function label(): string
    {
        return match($this) {
            self::KETUA_AKADEMIK => 'Ketua Akademik',
            self::WAKIL_KETUA_3 => 'Wakil Ketua 3 (Kemahasiswaan)',  // 🔥 UPDATED label
            self::KETUA => 'Direktur',  // 🔥 UPDATED label
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::KETUA_AKADEMIK => 'Akademik',
            self::WAKIL_KETUA_3 => 'Wakil Ketua 3',  // 🔥 UPDATED
            self::KETUA => 'Direktur',  // 🔥 UPDATED
        };
    }

    public function color(): string
    {
        return match($this) {
            self::KETUA_AKADEMIK => 'blue',
            self::WAKIL_KETUA_3 => 'green',
            self::KETUA => 'purple',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::KETUA_AKADEMIK => 'book-open',
            self::WAKIL_KETUA_3 => 'users',
            self::KETUA => 'crown',
        };
    }

    public function level(): int
    {
        return match($this) {
            self::KETUA_AKADEMIK => 1,
            self::WAKIL_KETUA_3 => 2,
            self::KETUA => 3,
        };
    }

    public function priority(): int
    {
        return $this->level();
    }

    public static function toArray(): array
    {
        return [
            self::KETUA_AKADEMIK->value => self::KETUA_AKADEMIK->label(),
            self::WAKIL_KETUA_3->value => self::WAKIL_KETUA_3->label(),
            self::KETUA->value => self::KETUA->label(),
        ];
    }

    public static function fromLevel(int $level): ?self
    {
        return match($level) {
            1 => self::KETUA_AKADEMIK,
            2 => self::WAKIL_KETUA_3,
            3 => self::KETUA,
            default => null,
        };
    }

    public function next(): ?self
    {
        return match($this) {
            self::KETUA_AKADEMIK => self::WAKIL_KETUA_3,
            self::WAKIL_KETUA_3 => self::KETUA,
            self::KETUA => null,
        };
    }

    public function isFinal(): bool
    {
        return $this === self::KETUA;
    }

    public function isFirst(): bool
    {
        return $this === self::KETUA_AKADEMIK;
    }

    public function badgeClass(): string
    {
        $colorMap = [
            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
            'green' => 'bg-green-100 text-green-800 border-green-200',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
        ];

        return $colorMap[$this->color()] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    }

    public function description(): string
    {
        return match($this) {
            self::KETUA_AKADEMIK => 'Verifikasi kelayakan akademik mahasiswa (Level 1)',
            self::WAKIL_KETUA_3 => 'Verifikasi administrasi dan kemahasiswaan (Level 2)',
            self::KETUA => 'Approval final dari Direktur (Level 3)',
        };
    }
}
