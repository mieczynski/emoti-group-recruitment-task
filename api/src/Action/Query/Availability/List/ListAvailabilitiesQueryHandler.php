<?php
declare(strict_types=1);

namespace App\Action\Query\Availability\List;

use App\Service\Availability\AvailabilityServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListAvailabilitiesQueryHandler
{
    public function __construct(private AvailabilityServiceInterface $availabilityService) {}

    public function __invoke(ListAvailabilitiesQuery $q): array
    {
        return $this->availabilityService->find($q->params);
    }
}
