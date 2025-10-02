<?php

namespace App\Factory\Availability;

use App\DTO\Availability\FreeTermDTO;

readonly class FreeTermFactory implements FreeTermFactoryInterface
{
    public function buildFreeTerm(\DateTimeImmutable $start, \DateTimeImmutable $end, int $nights, string $price): FreeTermDTO
    {
        return new FreeTermDTO($start, $end, $nights, $price);
    }
}
