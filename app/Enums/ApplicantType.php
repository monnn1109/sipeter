<?php

namespace App\Enums;

enum ApplicantType: string
{
    case MAHASISWA = 'mahasiswa';
    case DOSEN = 'dosen';
    case STAFF = 'staff';

    public function label(): string
    {
        return match($this) {
            self::MAHASISWA => 'Mahasiswa',
            self::DOSEN => 'Dosen',
            self::STAFF => 'Staff',
        };
    }

    public function identifierLabel(): string
    {
        return match($this) {
            self::MAHASISWA => 'NIM',
            self::DOSEN => 'NIP/NIDN',
            self::STAFF => 'NIP',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getIdentifierLabel(): string
    {
        return $this->identifierLabel();
    }

    public function requiresLogin(): bool
    {
        return in_array($this, [self::DOSEN, self::STAFF]);
    }

    public function isInternal(): bool
    {
        return $this->requiresLogin();
    }

    public function isGuest(): bool
    {
        return $this === self::MAHASISWA;
    }

    public function icon(): string
    {
        return match($this) {
            self::MAHASISWA => 'user',
            self::DOSEN => 'user-check',
            self::STAFF => 'users',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::MAHASISWA => 'blue',
            self::DOSEN => 'green',
            self::STAFF => 'purple',
        };
    }

    public static function options(): array
    {
        return [
            self::MAHASISWA->value => self::MAHASISWA->label(),
            self::DOSEN->value => self::DOSEN->label(),
            self::STAFF->value => self::STAFF->label(),
        ];
    }
}
