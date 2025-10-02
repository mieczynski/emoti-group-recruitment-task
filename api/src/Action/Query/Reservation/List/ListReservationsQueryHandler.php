<?php

declare(strict_types=1);

namespace App\Action\Query\Reservation\List;

use App\Repository\Reservation\ReservationRepositoryInterface;
use App\Util\Pagination\BuildsPaginatedResponseTrait;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ListReservationsQueryHandler
{
    use BuildsPaginatedResponseTrait;
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly Security $security,
    ) {}

    public function __invoke(ListReservationsQuery $q): array
    {
        [$items, $total] = $this->reservationRepository->findAllByParams($q->params, $this->security->getUser(), $this->security->isGranted('ROLE_ADMIN'));
        return $this->buildPaginatedResponse($items, $total, $q->params->page, $q->params->limit);
    }
}
