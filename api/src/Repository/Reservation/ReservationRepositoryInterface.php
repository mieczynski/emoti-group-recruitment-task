<?php

declare(strict_types=1);

namespace App\Repository\Reservation;

use App\DTO\Reservation\ReservationListParamsDTO;
use App\Entity\Reservation;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;
    public function findAllByParams(ReservationListParamsDTO $params, UserInterface $user, bool $isAdmin): array;
}
