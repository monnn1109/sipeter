<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case DOSEN = 'dosen';
    case STAFF = 'staff';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::DOSEN => 'Dosen',
            self::STAFF => 'Staff',
        };
    }


    public function getLabel(): string
    {
        return $this->label();
    }


    public function color(): string
    {
        return match($this) {
            self::ADMIN => 'red',
            self::DOSEN => 'blue',
            self::STAFF => 'green',
        };
    }


    public function icon(): string
    {
        return match($this) {
            self::ADMIN => 'fa-user-shield',
            self::DOSEN => 'fa-chalkboard-teacher',
            self::STAFF => 'fa-user-tie',
        };
    }


    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }


    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
