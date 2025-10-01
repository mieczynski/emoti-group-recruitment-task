<?php
declare(strict_types=1);

namespace App\DTO\Common;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationSortDTO
{
    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Positive]
    #[Assert\LessThanOrEqual(100)]
    public int $limit = 20;

    #[Assert\Length(max: 64)]
    public ?string $orderBy = 'createdAt';

    #[Assert\Choice(['asc', 'ASC', 'desc', 'DESC'])]
    public ?string $order = 'desc';
}
