<?php
declare(strict_types=1);

namespace App\Controller;

use App\Action\Query\Availability\List\ListAvailabilitiesQuery;
use App\DTO\Availability\AvailabilityListParamsDTO;
use App\Http\Attribute\FromQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/availability')]
final class AvailabilityController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus
    ) {}

    #[Route('', name: 'cget_availabilities', methods: ['GET'])]
    public function list(#[FromQuery] AvailabilityListParamsDTO $dto): JsonResponse
    {
        $result = $this->handle(new ListAvailabilitiesQuery($dto));
        return $this->json($result, 200, [], ['groups' => ['availability:read']]);
    }
}
