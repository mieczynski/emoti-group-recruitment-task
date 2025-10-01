<?php
declare(strict_types=1);

namespace App\Enum;

enum RoomType: string
{
    case STANDARD = 'STD';
    case DELUXE   = 'DLX';
    case SUITE    = 'STE';

    public function label(): string
    {
        return match ($this) {
            self::STANDARD => 'Standard',
            self::DELUXE   => 'Deluxe',
            self::SUITE    => 'Suite',
        };
    }
}
