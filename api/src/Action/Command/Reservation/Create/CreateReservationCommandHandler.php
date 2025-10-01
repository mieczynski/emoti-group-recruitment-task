<?php

declare(strict_types=1);

namespace App\Action\Command\Reservation\Create;

use App\Entity\Reservation;
use App\Service\Booking\BookingServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateReservationCommandHandler
{
    public function __construct(
        private readonly BookingServiceInterface $bookingService,
    ) {}


    public function __invoke(CreateReservationCommand $cmd): Reservation
    {
        return $this->bookingService->createReservation($cmd->getDto());
    }
}
