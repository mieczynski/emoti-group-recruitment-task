<?php
declare(strict_types=1);

namespace App\Action\Query\Availability\List;

use App\DTO\Availability\AvailabilityListParamsDTO;

final readonly class ListAvailabilitiesQuery
{
    public function __construct(public AvailabilityListParamsDTO $params) {}
}
