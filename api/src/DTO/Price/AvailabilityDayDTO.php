<?php

declare(strict_types=1);

namespace App\DTO\Price;

final readonly class AvailabilityDayDTO
{
    public function __construct(
        public \DateTimeImmutable $date,
        public int                $capacityAvailable,
        public string             $price
    ) {}
}
