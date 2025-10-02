<?php
declare(strict_types=1);

namespace App\DTO\Reservation;

use App\DTO\Common\PaginationSortDTO;
use App\DTO\Trait\FromToParams;
use App\DTO\Trait\RoomType;
use App\Enum\ReservationStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class ReservationListParamsDTO extends PaginationSortDTO
{
    use FromToParams;
    use RoomType;
    public ?ReservationStatus $status = null;

    #[Assert\Email]
    public ?string $email = null;
}
