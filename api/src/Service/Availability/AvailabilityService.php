<?php
declare(strict_types=1);

namespace App\Service\Availability;

use App\DTO\Availability\AvailabilityListParamsDTO;
use App\DTO\Price\AvailabilityDayDTO;
use App\Factory\Availability\FreeTermFactoryInterface;
use App\Helper\Price\PriceCalculatorInterface;
use App\Repository\VacancyCalendar\VacancyCalendarRepositoryInterface;

final readonly class AvailabilityService implements AvailabilityServiceInterface
{
    public function __construct(
        private VacancyCalendarRepositoryInterface $vacancyCalendarRepository,
        private FreeTermFactoryInterface $freeTermFactory,
        private PriceCalculatorInterface $priceCalculator
    ) {}

    public function find(AvailabilityListParamsDTO $dto): array
    {
        $days = $this->vacancyCalendarRepository->fetchRangeReadOnly($dto->roomTypeId, $dto->from, $dto->to);

        usort($days, fn(AvailabilityDayDTO $a, AvailabilityDayDTO $b) => $a->date <=> $b->date);

        $nights      = $dto->nights ?? 1;
        $minCapacity = $dto->minCapacity > 0 ? $dto->minCapacity : 1;

        if ($nights === 1) {
            return $this->singleNightTerms($days, $minCapacity);
        }
        return $this->windowedTerms($days, $nights, $minCapacity);
    }

    private function singleNightTerms(array $days, int $minCapacity): array
    {
        $out = [];
        foreach ($days as $d) {
            if ($d->capacityAvailable >= $minCapacity) {
                $start = $d->date;
                $end   = $d->date->modify('+1 day');
                $out[] = $this->freeTermFactory->buildFreeTerm($start, $end, 1, (string)$d->price);
            }
        }
        return $out;
    }

    private function windowedTerms(array $days, int $nights, int $minCapacity): array
    {
        $out = [];
        $n = \count($days);
        if ($n < $nights) {
            return $out;
        }

        for ($i = 0; $i <= $n - $nights; $i++) {
            $window = \array_slice($days, $i, $nights);

            if (!$this->isConsecutive($window)) {
                continue;
            }

            $ok = true;
            foreach ($window as $d) {
                if ($d->capacityAvailable < $minCapacity) {
                    $ok = false; break;
                }
            }
            if (!$ok) {
                continue;
            }

            $start = $window[0]->date;
            $end   = $window[$nights - 1]->date->modify('+1 day');
            $price = $this->priceCalculator->sum($window);
            $out[] = $this->freeTermFactory->buildFreeTerm($start, $end, $nights, $price);
        }

        return $out;
    }

    private function isConsecutive(array $days): bool
    {
        for ($k = 1, $n = \count($days); $k < $n; $k++) {
            if ($days[$k]->date->format('Y-m-d') !== $days[$k-1]->date->modify('+1 day')->format('Y-m-d')) {
                return false;
            }
        }
        return true;
    }
}
