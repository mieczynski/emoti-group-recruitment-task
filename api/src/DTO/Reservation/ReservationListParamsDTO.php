<?php
declare(strict_types=1);

namespace App\DTO\Reservation;

use App\DTO\Common\PaginationSortDTO;
use App\Enum\ReservationStatus;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

final class ReservationListParamsDTO extends PaginationSortDTO
{
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $from = null;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $to = null;

    #[Assert\Positive]
    public ?int $roomTypeId = null;

    public ?ReservationStatus $status = null;

    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Callback]
    public function validateRange(): void
    {
        if ($this->from && $this->to && $this->from > $this->to) {
            throw new \InvalidArgumentException('Query: "from" must be <= "to".');
        }
    }
}
