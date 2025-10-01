<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AvailabilityController extends AbstractController
{
    /**
     * GET /api/availability?from=YYYY-MM-DD&to=YYYY-MM-DD&roomTypeId=1
     * - end date is EXCLUSIVE (bookings usually check out on that day)
     * - returns rows from vacancy_calendar for each date (and room type if provided)
     */
    #[Route('/api/availability', name: 'api_availability', methods: ['GET'])]
    public function __invoke(Request $request, Connection $db): JsonResponse
    {
        return new JsonResponse('tst');
        $fromStr = (string) $request->query->get('from', '');
        $toStr   = (string) $request->query->get('to', '');
        $roomTypeIdStr = $request->query->get('roomTypeId'); // optional

        // Defaults: today..today+7 if not provided
        $today = new \DateTimeImmutable('today');
        $from = $fromStr !== '' ? \DateTimeImmutable::createFromFormat('Y-m-d', $fromStr) ?: null : $today;
        $to   = $toStr   !== '' ? \DateTimeImmutable::createFromFormat('Y-m-d', $toStr)     ?: null : $today->modify('+7 days');

        if (!$from || !$to) {
            return $this->error(400, 'Invalid date format. Use YYYY-MM-DD, e.g. from=2025-10-01&to=2025-10-08.');
        }
        if ($from >= $to) {
            return $this->error(400, '`from` must be earlier than `to` (end date is exclusive).');
        }
        if ($to > $from->modify('+366 days')) {
            return $this->error(400, 'Range too large. Max window is 366 days.');
        }

        $params = [
            'from' => $from->format('Y-m-d'),
            'to'   => $to->format('Y-m-d'),
        ];

        $sql = <<<SQL
SELECT
  date,
  room_type_id,
  capacity_total,
  capacity_available,
  price
FROM vacancy_calendar
WHERE date >= :from AND date < :to
SQL;

        if ($roomTypeIdStr !== null && $roomTypeIdStr !== '') {
            $sql .= ' AND room_type_id = :roomTypeId';
            $params['roomTypeId'] = (int) $roomTypeIdStr;
        }

        $sql .= ' ORDER BY date ASC, room_type_id ASC';

        $rows = $db->fetchAllAssociative($sql, $params);

        return $this->json([
            'range' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'endExclusive' => true,
            ],
            'count' => \count($rows),
            'data' => \array_map(static function (array $r): array {
                return [
                    'date' => $r['date'],
                    'roomTypeId' => (int) $r['room_type_id'],
                    'capacity_total' => (int) $r['capacity_total'],
                    'capacity_available' => (int) $r['capacity_available'],
                    'price' => $r['price'] !== null ? (float) $r['price'] : null,
                ];
            }, $rows),
        ]);
    }

    private function error(int $status, string $detail): JsonResponse
    {
        return $this->json([
            'title' => 'Bad Request',
            'status' => $status,
            'detail' => $detail,
        ], $status);
    }
}
