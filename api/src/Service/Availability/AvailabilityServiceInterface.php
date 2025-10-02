<?php
declare(strict_types=1);

namespace App\Service\Availability;

use App\DTO\Availability\AvailabilityListParamsDTO;
use App\DTO\Availability\FreeTermDTO;

interface AvailabilityServiceInterface
{
    /** @return list<FreeTermDTO> */
    public function find(AvailabilityListParamsDTO $dto): array;
}
