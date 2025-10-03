<?php

declare(strict_types=1);

namespace App\Helper\Price;

use App\DTO\Price\AvailabilityDayDTO;

interface PriceCalculatorInterface
{
    /**
     * @param AvailabilityDayDTO[] $days
     */
    public function sum(array $days): string;
}
