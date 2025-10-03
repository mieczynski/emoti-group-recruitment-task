<?php
declare(strict_types=1);

namespace App\Repository\RoomType;

use App\Entity\RoomType;

interface RoomTypeRepositoryInterface
{
    public function getById(int $id): RoomType;
    /** @return array<RoomType> */
    public function findAll(): array;
}
