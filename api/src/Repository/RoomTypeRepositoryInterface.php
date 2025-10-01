<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\RoomType;

interface RoomTypeRepositoryInterface
{
    public function getById(int $id): RoomType;
}
