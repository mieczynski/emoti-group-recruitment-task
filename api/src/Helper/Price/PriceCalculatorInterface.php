<?php

declare(strict_types=1);

namespace App\Helper\Price;

interface PriceCalculatorInterface
{
    /**
     * @param AvailabilityDay[] $days
     */
    public function sum(array $days): string; // decimal(10,2) as string
}
