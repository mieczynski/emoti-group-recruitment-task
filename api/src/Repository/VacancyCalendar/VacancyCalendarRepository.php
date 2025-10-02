<?php

declare(strict_types=1);

namespace App\Repository\VacancyCalendar;

use App\DTO\Price\AvailabilityDayDTO;
use App\Entity\VacancyCalendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

final class VacancyCalendarRepository extends ServiceEntityRepository implements VacancyCalendarRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacancyCalendar::class);
    }

    public function fetchRangeForUpdate(int $roomTypeId, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $qb = $this->createQueryBuilder('vc')
            ->andWhere('IDENTITY(vc.roomType) = :rt')
            ->andWhere('vc.date >= :from AND vc.date < :to')
            ->orderBy('vc.date', 'ASC')
            ->setParameter('rt', $roomTypeId) // int
            ->setParameter('from', $start, Types::DATE_IMMUTABLE)
            ->setParameter('to', $end, Types::DATE_IMMUTABLE);

        $query = $qb->getQuery();
        $query->setLockMode(LockMode::PESSIMISTIC_WRITE);

        /** @var VacancyCalendar[] $rows */
        $rows = $query->getResult();

        return array_map(
            fn (VacancyCalendar $vc) => new AvailabilityDayDTO(
                $vc->getDate(),
                $vc->getCapacityAvailable(),
                (string)($vc->getPrice() ?? '0.00')
            ),
            $rows
        );
    }


    public function decrementRange(int $roomTypeId, \DateTimeImmutable $start, \DateTimeImmutable $end): void
    {
        $em = $this->getEntityManager();

        $em->createQuery(
            'UPDATE ' . VacancyCalendar::class . ' vc
         SET vc.capacityAvailable = vc.capacityAvailable - 1
         WHERE IDENTITY(vc.roomType) = :rt
           AND vc.date >= :from AND vc.date < :to'
        )
            ->setParameter('rt', $roomTypeId)
            ->setParameter('from', $start, Types::DATE_IMMUTABLE)
            ->setParameter('to', $end, Types::DATE_IMMUTABLE)
            ->execute();
    }

    public function fetchRangeReadOnly(?int $roomTypeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('vc')
            ->select('vc.date AS d, vc.capacityAvailable AS c, COALESCE(vc.price, 0) AS p')
            ->andWhere('vc.date >= :from AND vc.date < :to')
            ->orderBy('vc.date', 'ASC')
            ->setParameter('from', $from, Types::DATE_IMMUTABLE)
            ->setParameter('to', $to, Types::DATE_IMMUTABLE);

        if($roomTypeId){
            $qb->andWhere('vc.roomType = :rt')
                ->setParameter('rt', $roomTypeId);
        }

        $q = $qb->getQuery();
        $q->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
        $q->setHint(Query::HINT_READ_ONLY, true);

        $rows = $q->getResult();

        $out = [];
        foreach ($rows as $r) {
            $out[] = new AvailabilityDayDTO(
                $r['d'],
                (int)$r['c'],
                (string)$r['p']
            );
        }
        return $out;
    }
}
