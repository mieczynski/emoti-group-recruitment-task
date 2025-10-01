<?php
declare(strict_types=1);

namespace App\Service\Booking;

use App\DTO\Reservation\CreateReservationDTO;
use App\Entity\Reservation;

interface BookingServiceInterface
{
    public function createReservation(CreateReservationDTO $dto): Reservation;
}
