<?php
declare(strict_types=1);

namespace App\Action\Query\RoomType\List;

use App\Repository\RoomType\RoomTypeRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListRoomTypesQueryHandler
{
    public function __construct(private RoomTypeRepositoryInterface $roomTypeRepository) {}

    public function __invoke(ListRoomTypesQuery $q): array
    {
        return $this->roomTypeRepository->findAll();
    }
}
