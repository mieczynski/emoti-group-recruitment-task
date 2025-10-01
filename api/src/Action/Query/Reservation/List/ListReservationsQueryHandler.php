<?php

declare(strict_types=1);

namespace App\Action\Query\Reservation\List;

use App\Repository\ReservationRepositoryInterface;
use App\Util\Pagination\BuildsPaginatedResponseTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ListReservationsQueryHandler
{
    use BuildsPaginatedResponseTrait;
    public function __construct(private readonly ReservationRepositoryInterface $reservationRepository) {}

    public function __invoke(ListReservationsQuery $q): array
    {
        [$items, $total] = $this->reservationRepository->findAllByParams($q->params);
        return $this->buildPaginatedResponse($items, $total, $q->params->page, $q->params->limit);
    }
}
