<?php
declare(strict_types=1);

namespace App\Util\Pagination;

trait BuildsPaginatedResponseTrait
{
    /**
     * @param iterable $items
     * @param int $total
     * @param int $page
     * @param int $limit
     * @return array{data: iterable, meta: array{page:int,limit:int,total:int,pages:int}}
     */
    private function buildPaginatedResponse(iterable $items, int $total, int $page, int $limit): array
    {
        $page  = max(1, $page);
        $limit = max(1, $limit);
        $pages = (int) \ceil($total / max(1, $limit));

        return [
            'data' => $items,
            'meta' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => $pages,
            ],
        ];
    }
}
