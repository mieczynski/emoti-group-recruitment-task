<?php

namespace App\Factory\Reservation;

use App\Entity\Reservation;
use App\Entity\RoomType;

class ReservationFactory
{
    public function buildReservation(
        RoomType $roomType,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        string $guestName,
        string $email,
        string $total
    ): Reservation {
        $reservation = new Reservation($roomType, $start, $end, $guestName, $email);
        $reservation->setTotalPrice($total);
        return $reservation;
    }

}
