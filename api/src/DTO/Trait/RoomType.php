<?php

namespace App\DTO\Trait;

use Symfony\Component\Validator\Constraints as Assert;
trait RoomType
{
    #[Assert\Positive]
    public ?int $roomTypeId = null;
}
