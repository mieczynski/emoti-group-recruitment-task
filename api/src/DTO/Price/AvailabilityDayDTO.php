<?php

declare(strict_types=1);

namespace App\DTO\Price;

final class AvailabilityDayDTO
{
    public function __construct(
        public readonly \DateTimeImmutable $date,
        public readonly int $capacityAvailable,
        public readonly string $price
    ) {}
}
