<?php
declare(strict_types=1);

namespace App\Action\Query\Reservation\List;

use App\DTO\Reservation\ReservationListParamsDTO;

final class ListReservationsQuery
{
    public function __construct(public readonly ReservationListParamsDTO $params) {}
}
