<?php
declare(strict_types=1);

namespace App\Controller;

use App\Action\Command\Reservation\Create\CreateReservationCommand;
use App\Action\Query\Reservation\List\ListReservationsQuery;
use App\DTO\Reservation\CreateReservationDTO;
use App\DTO\Reservation\ReservationListParamsDTO;
use App\Http\Attribute\FromBody;
use App\Http\Attribute\FromQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/reservations')]
final class ReservationController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus,
    )
    {
    }

    #[Route('', name: 'post_reservation', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(#[FromBody] CreateReservationDTO $dto): JsonResponse
    {
        $result = $this->handle(new CreateReservationCommand($dto));
        return $this->json($result, JsonResponse::HTTP_CREATED, [], [
            'groups' => ['reservation:read']
        ]);
    }

    #[Route('', name: 'cget_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(#[FromQuery] ReservationListParamsDTO $dto): JsonResponse
    {
        $result = $this->handle(new ListReservationsQuery($dto));
        return $this->json($result, JsonResponse::HTTP_OK, [], [
            'groups' => ['reservation:read']
        ]);
    }
}
