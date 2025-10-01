<?php

declare(strict_types=1);

namespace App\DTO\Reservation;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

final class CreateReservationDTO
{
//    #[JMS\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Assert\NotNull, Assert\Type(\DateTimeImmutable::class)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]

    public \DateTimeImmutable $startDate;

    #[Assert\NotNull, Assert\Type(\DateTimeImmutable::class)]
    public \DateTimeImmutable $endDate; // exclusive

    #[Assert\NotBlank, Assert\Length(max: 120)]
    public string $guestName;

    #[Assert\NotBlank, Assert\Email]
    public string $email;

    #[Assert\NotNull, Assert\Positive]
    public int $roomTypeId;
}
