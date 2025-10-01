<?php
declare(strict_types=1);

namespace App\Validator\Booking;

use App\DTO\Price\AvailabilityDayDTO;
use App\DTO\Reservation\CreateReservationDTO;

interface BookingValidatorInterface
{
    public function assertDateRange(CreateReservationDTO $dto): void;

    /**
     * @param array<int,AvailabilityDayDTO> $days
     */
    public function assertAllDaysConfigured(array $days, \DateTimeImmutable $start, \DateTimeImmutable $end): void;

    /**
     * @param array<int,AvailabilityDayDTO> $days
     */
    public function assertCapacityAvailable(array $days): void;
}
