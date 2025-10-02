<?php
declare(strict_types=1);

namespace App\Repository\VacancyCalendar;

interface VacancyCalendarRepositoryInterface
{
    public function fetchRangeForUpdate(int $roomTypeId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;
    public function decrementRange(int $roomTypeId, \DateTimeImmutable $start, \DateTimeImmutable $end): void;
    public function fetchRangeReadOnly(?int $roomTypeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array;
}
