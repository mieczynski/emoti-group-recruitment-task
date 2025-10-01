<?php

declare(strict_types=1);

namespace App\Action\Command\Reservation\Create;

use App\DTO\Reservation\CreateReservationDTO;

final readonly class CreateReservationCommand
{
    public function __construct(private readonly CreateReservationDTO $dto)
    {
    }

    public function getDto(): CreateReservationDTO
    {
        return $this->dto;
    }
}
