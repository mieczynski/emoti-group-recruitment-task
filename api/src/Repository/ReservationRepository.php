<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Reservation\ReservationListParamsDTO;
use App\Entity\Reservation;
use App\Service\Filter\ReservationQueryFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

final class ReservationRepository extends ServiceEntityRepository implements ReservationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private readonly ReservationQueryFilter $filter)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $reservation): void
    {
        $em = $this->getEntityManager();
        $em->persist($reservation);
    }


    public function findAllByParams(ReservationListParamsDTO $params, UserInterface $user, bool $isAdmin): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.roomType', 'rt')->addSelect('rt');

        $this->filter->applyFilters($qb, $params);

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(r.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $this->filter->applySorting($qb, $params);
        $this->filter->applyUserScope($qb, $user, $isAdmin);

        $page  = max(1, $params->page);
        $limit = max(1, $params->limit);
        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        $items = $qb->getQuery()->getResult();

        return [$items, $total];
    }
}
