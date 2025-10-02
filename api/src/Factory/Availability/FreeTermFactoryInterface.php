<?php

namespace App\Factory\Availability;

use App\DTO\Availability\FreeTermDTO;

interface FreeTermFactoryInterface
{
    public function buildFreeTerm(\DateTimeImmutable $start, \DateTimeImmutable $end, int $nights, string $price): FreeTermDTO;
}
