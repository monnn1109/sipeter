<?php

namespace App\Enums;

enum DeliveryMethod: string
{
    case PICKUP = 'pickup';
    case DOWNLOAD = 'download';


    public function label(): string
    {
        return match($this) {
            self::PICKUP => 'Ambil di Tempat',
            self::DOWNLOAD => 'Download Online',
        };
    }


    public function icon(): string
    {
        return match($this) {
            self::PICKUP => 'building',
            self::DOWNLOAD => 'download',
        };
    }


    public function color(): string
    {
        return match($this) {
            self::PICKUP => 'blue',
            self::DOWNLOAD => 'green',
        };
    }


    public function getLabel(): string
    {
        return $this->label();
    }

    public function getIcon(): string
    {
        return $this->icon();
    }

    public function getColor(): string
    {
        return $this->color();
    }


    public function badgeClass(): string
    {
        $colorMap = [
            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
            'green' => 'bg-green-100 text-green-800 border-green-200',
        ];

        return $colorMap[$this->color()] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    }

    public function isPickup(): bool
    {
        return $this === self::PICKUP;
    }


    public function isDownload(): bool
    {
        return $this === self::DOWNLOAD;
    }

    public function description(): string
    {
        return match($this) {
            self::PICKUP => 'Ambil dokumen fisik langsung di kampus',
            self::DOWNLOAD => 'Unduh dokumen secara online (PDF)',
        };
    }

    public static function options(): array
    {
        return [
            self::PICKUP->value => self::PICKUP->label(),
            self::DOWNLOAD->value => self::DOWNLOAD->label(),
        ];
    }

    public static function fromString(string $value): ?self
    {
        return match(strtolower($value)) {
            'pickup' => self::PICKUP,
            'download' => self::DOWNLOAD,
            default => null,
        };
    }
}

