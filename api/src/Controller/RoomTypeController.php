<?php
declare(strict_types=1);

namespace App\Controller;

use App\Action\Query\RoomType\List\ListRoomTypesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/room-type')]
final class RoomTypeController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus
    ) {}

    #[Route('', name: 'cget_room_type', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $result = $this->handle(new ListRoomTypesQuery());
        return $this->json($result, 200, [], ['groups' => ['roomtype:read']]);
    }
}
