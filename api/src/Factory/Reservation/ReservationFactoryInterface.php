<?php

namespace App\Factory\Reservation;

use App\Entity\Reservation;
use App\Entity\RoomType;

interface ReservationFactoryInterface
{
    public function buildReservation(
        RoomType $roomType,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        string $guestName,
        string $email,
        string $total
    ): Reservation;
}
