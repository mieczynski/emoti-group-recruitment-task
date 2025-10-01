<?php
declare(strict_types=1);

namespace App\Validator\Booking;

use App\DTO\Reservation\CreateReservationDTO;
use App\Exception\Booking\CalendarNotConfiguredException;
use App\Exception\Booking\CapacityUnavailableException;
use App\Exception\Booking\InvalidDateRangeException;

final readonly class BookingValidator implements BookingValidatorInterface
{
    public function assertDateRange(CreateReservationDTO $dto): void
    {
        if (!$dto->startDate || !$dto->endDate) {
            throw new InvalidDateRangeException('Both startDate and endDate are required.');
        }
        if ($dto->startDate >= $dto->endDate) {
            throw new InvalidDateRangeException('endDate must be later than startDate (exclusive).');
        }
    }

    public function assertAllDaysConfigured(array $days, \DateTimeImmutable $start, \DateTimeImmutable $end): void
    {
        $expected = [];
        for ($d = $start; $d < $end; $d = $d->modify('+1 day')) {
            $expected[] = $d->format('Y-m-d');
        }

        $returned = array_map(
            static fn($x) => $x->date->format('Y-m-d'),
            $days
        );

        $missing = array_values(array_diff($expected, $returned));
        if ($missing !== []) {
            throw new CalendarNotConfiguredException(
                sprintf('Missing vacancy_calendar rows for: %s', implode(', ', $missing))
            );
        }
    }

    public function assertCapacityAvailable(array $days): void
    {
        foreach ($days as $d) {
            if ($d->capacityAvailable < 1) {
                throw new CapacityUnavailableException('No vacancies for one or more dates.');
            }
        }
    }
}
