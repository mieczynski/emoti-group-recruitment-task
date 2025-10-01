<?php

declare(strict_types=1);

namespace App\Helper\Price;


use App\DTO\Price\AvailabilityDayDTO;

final class PriceCalculator implements PriceCalculatorInterface
{
    public function sum(array $days): string
    {
        $total = '0.00';
        /** @var AvailabilityDayDTO $day */
        foreach ($days as $day) {
            \assert($day instanceof AvailabilityDayDTO);
            $total = bcadd($total, $day->price, 2);
        }
        return $total;
    }
}
