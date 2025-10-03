<?php
declare(strict_types=1);

namespace App\Repository\VacancyCalendar;

use App\DTO\Price\AvailabilityDayDTO;

interface VacancyCalendarRepositoryInterface
{

    /** @return array<AvailabilityDayDTO> */
    public function fetchRangeForUpdate(int $roomTypeId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;
    public function decrementRange(int $roomTypeId, \DateTimeImmutable $start, \DateTimeImmutable $end): void;
    /** @return array<AvailabilityDayDTO> */
    public function fetchRangeReadOnly(?int $roomTypeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array;
}
