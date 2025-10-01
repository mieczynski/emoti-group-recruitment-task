<?php
declare(strict_types=1);

namespace App\Service\Filter;

use App\DTO\Reservation\ReservationListParamsDTO;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;

final class ReservationQueryFilter
{
    /** map API orderBy → DQL path */
    private const SORT_MAP = [
        'createdAt'  => 'r.createdAt',
        'startDate'  => 'r.startDate',
        'endDate'    => 'r.endDate',
        'totalPrice' => 'r.totalPrice',
        'status'     => 'r.status',
    ];

    public function applyFilters(QueryBuilder $qb, ReservationListParamsDTO $p): void
    {
        if ($p->from) {
            $qb->andWhere('r.startDate >= :from')->setParameter('from', $p->from, Types::DATE_IMMUTABLE);
        }
        if ($p->to) {
            $qb->andWhere('r.endDate <= :to')->setParameter('to', $p->to, Types::DATE_IMMUTABLE);
        }
        if ($p->roomTypeId) {
            $qb->andWhere('rt.id = :rtId')->setParameter('rtId', $p->roomTypeId);
        }
        if ($p->status) {
            $qb->andWhere('r.status = :status')->setParameter('status', $p->status->value);
        }
        if ($p->email) {
            $qb->andWhere('LOWER(r.email) LIKE :email')->setParameter('email', '%' . \mb_strtolower($p->email) . '%');
        }
    }

    public function applySorting(QueryBuilder $qb, ReservationListParamsDTO $p): void
    {
        $orderBy = $p->orderBy ?? 'createdAt';
        $dir     = \strtoupper($p->order ?? 'DESC');

        $field = self::SORT_MAP[$orderBy] ?? self::SORT_MAP['createdAt'];
        if ($dir !== 'ASC' && $dir !== 'DESC') {
            $dir = 'DESC';
        }

        $qb->orderBy($field, $dir);
    }
}
