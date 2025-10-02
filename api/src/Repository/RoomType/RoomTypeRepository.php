<?php

declare(strict_types=1);

namespace App\Repository\RoomType;

use App\Entity\RoomType;
use App\Exception\Booking\RoomTypeNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RoomTypeRepository extends ServiceEntityRepository implements RoomTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomType::class);
    }

    public function getById(int $id): RoomType
    {
        $rt = parent::find($id);

        if (!$rt) {
            throw new RoomTypeNotFoundException('roomTypeId not found');
        }
        return $rt;
    }
}
