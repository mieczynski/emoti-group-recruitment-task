<?php

declare(strict_types=1);

namespace App\Enum;

enum ReservationStatus: string
{
    case BOOKED = 'BOOKED';
    case CANCELLED = 'CANCELLED';
}
