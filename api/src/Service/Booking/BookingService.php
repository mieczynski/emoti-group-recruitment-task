<?php
declare(strict_types=1);

namespace App\Service\Booking;

use App\DTO\Reservation\CreateReservationDTO;
use App\Entity\Reservation;
use App\Entity\ReservationDate;
use App\Entity\RoomType;
use App\Factory\Reservation\ReservationFactoryInterface;
use App\Helper\Price\PriceCalculatorInterface;
use App\Infrastructure\Transaction\TransactionManagerInterface;
use App\Repository\Reservation\ReservationRepositoryInterface;
use App\Repository\RoomType\RoomTypeRepositoryInterface;
use App\Repository\VacancyCalendar\VacancyCalendarRepositoryInterface;
use App\Validator\Booking\BookingValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class BookingService implements BookingServiceInterface
{
    public function __construct(
        private RoomTypeRepositoryInterface        $roomTypesRepository,
        private VacancyCalendarRepositoryInterface $vacancyCalendarRepository,
        private ReservationRepositoryInterface     $reservationRepository,
        private PriceCalculatorInterface           $priceCalculator,
        private ReservationFactoryInterface        $reservationFactory,
        private TransactionManagerInterface        $transactionManager,
        private EntityManagerInterface             $entityManager,
        private BookingValidatorInterface          $validator,
    ) {}

    public function createReservation(CreateReservationDTO $dto): Reservation
    {
        $this->validator->assertDateRange($dto);

        $start    = $dto->startDate;
        $end      = $dto->endDate;
        $roomType = $this->getRoomTypeOrFail($dto->roomTypeId);

        return $this->transactionManager->transactional(function () use ($roomType, $start, $end, $dto) {
            $days = $this->vacancyCalendarRepository->fetchRangeForUpdate($roomType->getId(), $start, $end);

            $this->validator->assertAllDaysConfigured($days, $start, $end);
            $this->validator->assertCapacityAvailable($days);

            $total = $this->priceCalculator->sum($days);

            $reservation = $this->reservationFactory->buildReservation(
                roomType:  $roomType,
                start:     $start,
                end:       $end,
                guestName: $dto->guestName,
                email:     $dto->email,
                total:     $total
            );

            foreach ($days as $d) {
                $line = new ReservationDate($d->date, $d->price);
                $reservation->addReservationDate($line);
                $this->entityManager->persist($line);
            }

            $this->reservationRepository->save($reservation);
            $this->vacancyCalendarRepository->decrementRange($roomType->getId(), $start, $end);
            $this->entityManager->flush();

            return $reservation;
        });
    }

    private function getRoomTypeOrFail(int $roomTypeId): RoomType
    {
        return $this->roomTypesRepository->getById($roomTypeId);
    }
}
