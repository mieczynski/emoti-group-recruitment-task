<?php

namespace App\Factory\Reservation;

use App\Entity\Reservation;
use App\Entity\RoomType;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ReservationFactory
{

    public function __construct(
        private Security $security
    )
    {
    }

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
        $reservation->setUser($this->security->getUser());
        return $reservation;
    }

}
