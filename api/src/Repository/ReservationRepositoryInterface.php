<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Reservation\ReservationListParamsDTO;
use App\Entity\Reservation;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;
    public function findAllByParams(ReservationListParamsDTO $params): array;
}
